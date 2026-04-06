<?php

namespace App\Controllers;

use App\Models\ApiKeyModel;

class ApiKeysController extends BaseController
{
    private ApiKeyModel $model;

    public function __construct()
    {
        $this->model = new ApiKeyModel();
    }

    public function index(): string
    {
        return view('apikeys/index', [
            'title' => 'API Keys — PCP Locations',
            'keys'  => $this->model->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }

    public function create(): string
    {
        return view('apikeys/form', [
            'title' => 'New API Key — PCP Locations',
        ]);
    }

    public function store(): \CodeIgniter\HTTP\ResponseInterface
    {
        $name = trim($this->request->getPost('name') ?? '');

        if ($name === '') {
            return redirect()->back()->withInput()->with('error', 'Name is required.');
        }

        $key = $this->model->generateKey();

        $this->model->insert([
            'name'       => $name,
            'api_key'    => $key,
            'active'     => 1,
            'created_by' => session()->get('user_id'),
        ]);

        // Flash the key once — it won't be shown again
        session()->setFlashdata('new_key', $key);

        return redirect()->to('/apikeys');
    }

    public function revoke(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $this->model->update($id, ['active' => 0]);
        return redirect()->to('/apikeys')->with('success', 'Key revoked.');
    }

    public function delete(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $this->model->delete($id);
        return redirect()->to('/apikeys')->with('success', 'Key deleted.');
    }
}
