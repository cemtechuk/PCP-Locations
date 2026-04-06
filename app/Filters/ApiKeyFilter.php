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

        $model->touchLastUsed((int) $record['id']);

        (new ApiLogModel())->record(
            (int) $record['id'],
            $record['name'],
            $request->getUri()->getPath(),
            $request->getIPAddress()
        );
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
