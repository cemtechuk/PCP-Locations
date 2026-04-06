<?php

namespace App\Controllers;

use App\Models\UserModel;

class UsersController extends BaseController
{
    protected UserModel $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function index(): string
    {
        return view('users/index', [
            'title'      => 'User Accounts — PCP Locations',
            'users'      => $this->model->orderBy('username', 'ASC')->findAll(),
            'totalCount' => 0,
        ]);
    }

    public function create(): string
    {
        return view('users/form', [
            'title'      => 'Add User — PCP Locations',
            'user'       => null,
            'errors'     => session()->getFlashdata('errors'),
            'totalCount' => 0,
        ]);
    }

    public function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email'    => 'required|valid_email|max_length[150]|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'role'     => 'required|in_list[viewer,user,editor,admin]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/users/create')
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $this->model->insert([
            'username' => trim($this->request->getPost('username')),
            'email'    => trim($this->request->getPost('email')),
            'role'     => $this->request->getPost('role'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
        ]);

        return redirect()->to('/users')->with('success', 'User created successfully.');
    }

    public function edit(int $id): string
    {
        $user = $this->model->find($id);

        if (! $user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('users/form', [
            'title'      => 'Edit ' . $user['username'] . ' — PCP Locations',
            'user'       => $user,
            'errors'     => session()->getFlashdata('errors'),
            'totalCount' => 0,
        ]);
    }

    public function update(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $user = $this->model->find($id);

        if (! $user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'email'    => "required|valid_email|max_length[150]|is_unique[users.email,id,{$id}]",
            'role'     => 'required|in_list[viewer,user,editor,admin]',
        ];

        $newPassword = $this->request->getPost('password');
        if ($newPassword !== '') {
            $rules['password'] = 'min_length[8]';
        }

        if (! $this->validate($rules)) {
            return redirect()->to("/users/edit/{$id}")
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $data = [
            'username' => trim($this->request->getPost('username')),
            'email'    => trim($this->request->getPost('email')),
            'role'     => $this->request->getPost('role'),
        ];

        if ($newPassword !== '') {
            $data['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        $this->model->update($id, $data);

        return redirect()->to('/users')->with('success', 'User updated successfully.');
    }

    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        // Prevent deleting your own account
        if ($id === (int) session()->get('user_id')) {
            return redirect()->to('/users')->with('error', 'You cannot delete your own account.');
        }

        $this->model->delete($id);

        return redirect()->to('/users')->with('success', 'User deleted.');
    }
}
