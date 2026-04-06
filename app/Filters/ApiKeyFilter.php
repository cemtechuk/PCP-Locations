<?php

namespace App\Filters;

use App\Models\ApiKeyModel;
use App\Models\ApiLogModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiKeyFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = $request->getHeaderLine('X-API-Key');

        if (empty($key)) {
            return response()->setStatusCode(401)
                ->setJSON(['error' => 'Missing API key. Send X-API-Key header.']);
        }

        $model  = new ApiKeyModel();
        $record = $model->findByKey($key);

        if (! $record) {
            return response()->setStatusCode(403)
                ->setJSON(['error' => 'Invalid or inactive API key.']);
        }

        $keyId    = (int) $record['id'];
        $apiLogs  = new ApiLogModel();

        // Per-key rate limit (NULL = unlimited)
        if (! empty($record['rate_limit'])) {
            $used = $apiLogs->countLastHour($keyId);
            if ($used >= (int) $record['rate_limit']) {
                return response()->setStatusCode(429)->setJSON([
                    'error'   => 'Rate limit exceeded for this API key.',
                    'limit'   => (int) $record['rate_limit'],
                    'used'    => $used,
                    'resets'  => 'within 1 hour of your oldest request',
                ]);
            }
        }

        $model->touchLastUsed($keyId);

        $apiLogs->record(
            $keyId,
            $record['name'],
            $request->getUri()->getPath(),
            $request->getIPAddress()
        );
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
