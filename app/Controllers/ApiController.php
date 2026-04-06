<?php

namespace App\Controllers;

use App\Models\CabinetModel;
use CodeIgniter\RESTful\ResourceController;

class ApiController extends BaseController
{
    private CabinetModel $cabinets;

    public function __construct()
    {
        $this->cabinets = new CabinetModel();
    }

    // ------------------------------------------------------------------
    // GET /api/v1/exchanges?q=HACK&limit=20
    // ------------------------------------------------------------------
    public function exchanges(): \CodeIgniter\HTTP\ResponseInterface
    {
        $q      = trim($this->request->getGet('q') ?? '');
        $limit  = min((int) ($this->request->getGet('limit') ?? 20), 100);

        $results = $this->cabinets->searchExchanges($q);

        if ($limit > 0) {
            $results = array_slice($results, 0, $limit);
        }

        $data = array_map(fn($r) => [
            'db'            => $r['db'],
            'db_name'       => $r['db_name'],
            'exchange'      => $r['exchange'],
            'cabinet_count' => (int) $r['cabinet_count'],
            'lat'           => $r['exch_lat'] ? (float) $r['exch_lat'] : null,
            'lng'           => $r['exch_lng'] ? (float) $r['exch_lng'] : null,
            'url'           => base_url('exchange/' . urlencode($r['db']) . '/' . urlencode($r['exchange'])),
        ], $results);

        return $this->response->setJSON(['data' => $data, 'count' => count($data)]);
    }

    // ------------------------------------------------------------------
    // GET /api/v1/exchanges/{db}/{exchange}
    // ------------------------------------------------------------------
    public function exchangeDetail(string $db, string $exchange): \CodeIgniter\HTTP\ResponseInterface
    {
        $info = $this->cabinets->getExchangeInfo($db, $exchange);

        if (! $info) {
            return $this->response->setStatusCode(404)
                ->setJSON(['error' => 'Exchange not found.']);
        }

        $cabinets = $this->cabinets->getCabinetsForExchange($db, $exchange);

        return $this->response->setJSON([
            'exchange' => [
                'db'            => $info['db'],
                'db_name'       => $info['db_name'],
                'exchange'      => $info['exchange'],
                'cabinet_count' => (int) $info['cabinet_count'],
                'lat'           => $info['exch_lat'] ? (float) $info['exch_lat'] : null,
                'lng'           => $info['exch_lng'] ? (float) $info['exch_lng'] : null,
                'url'           => base_url('exchange/' . urlencode($info['db']) . '/' . urlencode($info['exchange'])),
            ],
            'cabinets' => array_map(fn($c) => $this->formatCabinet($c), $cabinets),
        ]);
    }

    // ------------------------------------------------------------------
    // GET /api/v1/cabinets/{id}
    // ------------------------------------------------------------------
    public function cabinet(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $row = $this->cabinets->find($id);

        if (! $row) {
            return $this->response->setStatusCode(404)
                ->setJSON(['error' => 'Cabinet not found.']);
        }

        return $this->response->setJSON(['data' => $this->formatCabinet($row)]);
    }

    // ------------------------------------------------------------------
    // GET /api/v1/nearby?lat=51.5&lng=-0.1&limit=5
    // ------------------------------------------------------------------
    public function nearby(): \CodeIgniter\HTTP\ResponseInterface
    {
        $lat   = (float) $this->request->getGet('lat');
        $lng   = (float) $this->request->getGet('lng');
        $limit = min((int) ($this->request->getGet('limit') ?? 3), 20);

        if (! $lat || ! $lng) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'lat and lng query parameters are required.']);
        }

        $results = $this->cabinets->getNearbyExchanges($lat, $lng, $limit);

        $data = array_map(fn($r) => [
            'db'            => $r['db'],
            'db_name'       => $r['db_name'],
            'exchange'      => $r['exchange'],
            'cabinet_count' => (int) $r['cabinet_count'],
            'lat'           => $r['exch_lat'] ? (float) $r['exch_lat'] : null,
            'lng'           => $r['exch_lng'] ? (float) $r['exch_lng'] : null,
            'distance_km'   => (float) $r['distance_km'],
            'url'           => base_url('exchange/' . urlencode($r['db']) . '/' . urlencode($r['exchange'])),
        ], $results);

        return $this->response->setJSON(['data' => $data]);
    }

    // ------------------------------------------------------------------
    // GET /api/v1/search?exchange=hackney&cab=P1
    // ------------------------------------------------------------------
    public function search(): \CodeIgniter\HTTP\ResponseInterface
    {
        $exchange = trim($this->request->getGet('exchange') ?? '');
        $cab      = trim($this->request->getGet('cab') ?? '');

        if ($exchange === '' || $cab === '') {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'Both exchange and cab parameters are required.']);
        }

        $results = $this->cabinets->searchCabinet($exchange, $cab);

        if (empty($results)) {
            return $this->response->setStatusCode(404)
                ->setJSON(['error' => 'No cabinets found matching those parameters.']);
        }

        return $this->response->setJSON([
            'data'  => array_map(fn($c) => $this->formatCabinet($c), $results),
            'count' => count($results),
        ]);
    }

    // ------------------------------------------------------------------
    private function formatCabinet(array $c): array
    {
        return [
            'id'       => (int) $c['id'],
            'db'       => $c['db'],
            'db_name'  => $c['db_name'],
            'exchange' => $c['exchange'],
            'cab'      => $c['cab'],
            'address'  => $c['address'],
            'lat'      => $c['lat'] ? (float) $c['lat'] : null,
            'lng'      => $c['lng'] ? (float) $c['lng'] : null,
            'notes'    => $c['notes'] ?: null,
            'url'      => base_url('cabinet/' . $c['id']),
        ];
    }
}
