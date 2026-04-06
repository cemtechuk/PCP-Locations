<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RateLimitFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = session()->get('role');

        if ($role === 'viewer') {
            $limit   = max(1, (int) setting('viewer_rate_limit', '100'));
            $cacheKey = 'rl_viewer_';
        } elseif ($role === 'guest') {
            $limit   = max(1, (int) setting('guest_rate_limit', '20'));
            $cacheKey = 'rl_guest_';
        } else {
            return;
        }

        $userId = (int) session()->get('user_id');
        $key    = $cacheKey . $userId;
        $cache  = \Config\Services::cache();

        $data = $cache->get($key);

        if ($data === null) {
            // First request in this window
            $cache->save($key, ['count' => 1, 'reset_at' => time() + 3600], 3600);
            return;
        }

        $count   = $data['count'] + 1;
        $ttl     = max(1, $data['reset_at'] - time());
        $resetAt = $data['reset_at'];

        if ($count > $limit) {
            $minutes = (int) ceil($ttl / 60);
            return $this->tooManyRequests($limit, $minutes, $request);
        }

        $cache->save($key, ['count' => $count, 'reset_at' => $resetAt], $ttl);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}

    private function tooManyRequests(int $limit, int $minutesLeft, RequestInterface $request)
    {
        $response = \Config\Services::response();
        $response->setStatusCode(429);

        // Return JSON for AJAX requests
        if ($request->isAJAX() || str_starts_with($request->getUri()->getPath(), 'api/')) {
            return $response->setJSON([
                'error'       => 'Rate limit exceeded.',
                'limit'       => $limit,
                'retry_in'    => "{$minutesLeft} minute(s)",
            ]);
        }

        $siteName = setting('site_title', 'PCP Locations');

        $body = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Rate Limit Exceeded — {$siteName}</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
            <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">
        </head>
        <body style="background:#f9f9f9; display:flex; align-items:center; justify-content:center; min-height:100vh;">
            <div style="text-align:center; max-width:420px; padding:2rem;">
                <div style="font-family:'Share Tech Mono',monospace; font-size:.7rem; color:#c8001e; letter-spacing:.12em; margin-bottom:.6rem;">429 — RATE LIMIT EXCEEDED</div>
                <h1 style="font-family:'Share Tech Mono',monospace; font-size:1.2rem; font-weight:400; margin-bottom:.8rem;">Too Many Requests</h1>
                <p style="color:#777; font-size:.875rem; margin-bottom:1.5rem;">
                    You have reached the limit of <strong>{$limit}</strong> requests per hour.<br>
                    Try again in approximately <strong>{$minutesLeft} minute(s)</strong>.
                </p>
                <a href="/" style="font-family:'Share Tech Mono',monospace; font-size:.75rem; color:#999; text-decoration:none; letter-spacing:.06em; text-transform:uppercase;">
                    &larr; Home
                </a>
            </div>
        </body>
        </html>
        HTML;

        return $response->setBody($body);
    }
}
