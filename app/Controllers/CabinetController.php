<?php

namespace App\Controllers;

use App\Models\CabinetModel;

class CabinetController extends BaseController
{
    protected CabinetModel $model;

    public function __construct()
    {
        $this->model = new CabinetModel();
    }

    // GET /exchange/create
    public function createExchange(): string
    {
        return view('exchange/form', [
            'title'      => 'Add Exchange — PCP Locations',
            'regions'    => $this->model->getRegions(),
            'errors'     => session()->getFlashdata('errors'),
            'totalCount' => $this->model->getTotalCount(),
        ]);
    }

    // POST /exchange/create
    public function storeExchange(): \CodeIgniter\HTTP\RedirectResponse
    {
        $rules = [
            'exchange' => 'required|max_length[100]',
            'db'       => 'required|max_length[5]',
            'address'  => 'permit_empty|max_length[500]',
            'lat'      => 'permit_empty|decimal',
            'lng'      => 'permit_empty|decimal',
            'notes'    => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $db       = strtoupper(trim($this->request->getPost('db')));
        $exchange = strtoupper(trim($this->request->getPost('exchange')));

        // Find db_name from the selected region
        $regions = $this->model->getRegions();
        $dbName  = '';
        foreach ($regions as $r) {
            if ($r['db'] === $db) { $dbName = $r['db_name']; break; }
        }

        if ($this->model->exchangeExists($db, $exchange)) {
            return redirect()->back()
                ->with('errors', ['exchange' => 'An exchange named "' . $exchange . '" already exists in that region.'])
                ->withInput();
        }

        // Create the EXCH record as the anchor row for this exchange
        $this->model->insert([
            'db'       => $db,
            'db_name'  => $dbName,
            'exchange' => $exchange,
            'cab'      => 'EXCH',
            'address'  => trim($this->request->getPost('address')),
            'lat'      => $this->request->getPost('lat') ?: null,
            'lng'      => $this->request->getPost('lng') ?: null,
            'notes'    => trim($this->request->getPost('notes')),
        ]);

        return redirect()->to('/exchange/' . urlencode($db) . '/' . urlencode($exchange))
            ->with('success', 'Exchange created. You can now add cabinets to it.');
    }

    // GET /cabinet/create/CL/HACKNEY
    public function create(string $db, string $exchange): string
    {
        $db       = urldecode($db);
        $exchange = urldecode($exchange);

        $info = $this->model->getExchangeInfo($db, $exchange);

        if (! $info) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Exchange not found.');
        }

        return view('cabinet/form', [
            'title'      => 'Add Cabinet — ' . $exchange,
            'cabinet'    => null,
            'db'         => $db,
            'db_name'    => $info['db_name'],
            'exchange'   => $exchange,
            'errors'     => session()->getFlashdata('errors'),
            'totalCount' => $this->model->getTotalCount(),
        ]);
    }

    // POST /cabinet/create
    public function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        $rules = [
            'db'       => 'required|max_length[5]',
            'db_name'  => 'required|max_length[50]',
            'exchange' => 'required|max_length[100]',
            'cab'      => 'required|max_length[20]',
            'address'  => 'permit_empty|max_length[500]',
            'lat'      => 'permit_empty|decimal',
            'lng'      => 'permit_empty|decimal',
            'notes'    => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $this->model->insert([
            'db'       => strtoupper(trim($this->request->getPost('db'))),
            'db_name'  => trim($this->request->getPost('db_name')),
            'exchange' => strtoupper(trim($this->request->getPost('exchange'))),
            'cab'      => strtoupper(trim($this->request->getPost('cab'))),
            'address'  => trim($this->request->getPost('address')),
            'lat'      => $this->request->getPost('lat') ?: null,
            'lng'      => $this->request->getPost('lng') ?: null,
            'notes'    => trim($this->request->getPost('notes')),
        ]);

        $db       = strtoupper(trim($this->request->getPost('db')));
        $exchange = strtoupper(trim($this->request->getPost('exchange')));

        return redirect()->to('/exchange/' . urlencode($db) . '/' . urlencode($exchange))
            ->with('success', 'Cabinet added.');
    }

    // GET /cabinet/edit/123
    public function edit(int $id): string
    {
        $cabinet = $this->model->find($id);

        if (! $cabinet) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('cabinet/form', [
            'title'      => 'Edit ' . $cabinet['exchange'] . ' ' . $cabinet['cab'] . ' — PCP Locations',
            'cabinet'    => $cabinet,
            'db'         => $cabinet['db'],
            'db_name'    => $cabinet['db_name'],
            'exchange'   => $cabinet['exchange'],
            'errors'     => session()->getFlashdata('errors'),
            'totalCount' => $this->model->getTotalCount(),
        ]);
    }

    // POST /cabinet/edit/123
    public function update(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $cabinet = $this->model->find($id);

        if (! $cabinet) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'cab'     => 'required|max_length[20]',
            'address' => 'permit_empty|max_length[500]',
            'lat'     => 'permit_empty|decimal',
            'lng'     => 'permit_empty|decimal',
            'notes'   => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $this->model->update($id, [
            'cab'     => strtoupper(trim($this->request->getPost('cab'))),
            'address' => trim($this->request->getPost('address')),
            'lat'     => $this->request->getPost('lat') ?: null,
            'lng'     => $this->request->getPost('lng') ?: null,
            'notes'   => trim($this->request->getPost('notes')),
        ]);

        return redirect()->to('/cabinet/' . $id)->with('success', 'Cabinet updated.');
    }
}
