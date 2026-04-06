<?php

namespace App\Controllers;

use App\Models\CabinetModel;

class Home extends BaseController
{
    protected CabinetModel $model;

    public function __construct()
    {
        $this->model = new CabinetModel();
    }

    // ---------------------------------------------------------------
    // Home — exchange search page
    // ---------------------------------------------------------------
    public function index(): string
    {
        return view('home', [
            'title'      => 'PCP Locations — Exchange Search',
            'totalCount' => $this->model->getTotalCount(),
        ]);
    }

    // ---------------------------------------------------------------
    // AJAX — nearest exchanges by GPS coordinates
    // ---------------------------------------------------------------
    public function nearbyExchanges(): \CodeIgniter\HTTP\ResponseInterface
    {
        $lat = (float) $this->request->getGet('lat');
        $lng = (float) $this->request->getGet('lng');

        if (! $lat || ! $lng) {
            return $this->response->setJSON([]);
        }

        $results = $this->model->getNearbyExchanges($lat, $lng, 3);

        $data = array_map(function (array $row): array {
            return [
                'exchange'      => $row['exchange'],
                'db_name'       => $row['db_name'],
                'db'            => $row['db'],
                'cabinet_count' => (int) $row['cabinet_count'],
                'distance_km'   => (float) $row['distance_km'],
                'detail_url'    => '/exchange/' . urlencode($row['db']) . '/' . urlencode($row['exchange']),
            ];
        }, $results);

        return $this->response->setJSON($data);
    }

    // ---------------------------------------------------------------
    // AJAX — live exchange search, returns JSON
    // ---------------------------------------------------------------
    public function exchangeSearch(): \CodeIgniter\HTTP\ResponseInterface
    {
        $query   = trim($this->request->getGet('q') ?? '');
        $results = $this->model->searchExchanges($query);

        $data = array_map(function (array $row): array {
            // Use the EXCH row coordinates if available, otherwise fall back to a name search
            if ($row['exch_lat'] && $row['exch_lng']) {
                $mapsUrl = 'https://www.google.com/maps?q=' . $row['exch_lat'] . ',' . $row['exch_lng'];
            } else {
                $mapsUrl = 'https://www.google.com/maps/search/' . urlencode($row['exchange'] . ' telephone exchange London');
            }

            return [
                'db'            => $row['db'],
                'db_name'       => $row['db_name'],
                'exchange'      => $row['exchange'],
                'cabinet_count' => (int) $row['cabinet_count'],
                'maps_url'      => $mapsUrl,
                'detail_url'    => '/exchange/' . urlencode($row['db']) . '/' . urlencode($row['exchange']),
            ];
        }, $results);

        return $this->response->setJSON($data);
    }

    // ---------------------------------------------------------------
    // Exchange detail — all cabinets for one exchange
    // ---------------------------------------------------------------
    public function exchangeDetail(string $db, string $exchange): string
    {
        $db       = urldecode($db);
        $exchange = urldecode($exchange);
        $filter   = trim($this->request->getGet('q') ?? '');

        $info     = $this->model->getExchangeInfo($db, $exchange);

        if (! $info) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Exchange not found.');
        }

        $cabinets = $this->model->getCabinetsForExchange($db, $exchange, $filter);

        return view('exchange_detail', [
            'title'      => $info['exchange'] . ' — PCP Locations',
            'info'       => $info,
            'cabinets'   => $cabinets,
            'filter'     => $filter,
            'totalCount' => $this->model->getTotalCount(),
        ]);
    }

    // ---------------------------------------------------------------
    // Cabinet detail
    // ---------------------------------------------------------------
    public function cabinet(int $id): string
    {
        $cabinet = $this->model->find($id);

        if (! $cabinet) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Cabinet not found.');
        }

        return view('cabinet_detail', [
            'title'      => $cabinet['exchange'] . ' ' . $cabinet['cab'] . ' — PCP Locations',
            'cabinet'    => $cabinet,
            'totalCount' => $this->model->getTotalCount(),
        ]);
    }
}
