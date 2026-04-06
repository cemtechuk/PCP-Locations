<?php

namespace App\Controllers;

use App\Models\UserModel;

class ProfileController extends BaseController
{
    public function index(): string
    {
        $userId = (int) session()->get('user_id');
        $user   = (new UserModel())->find($userId);

        return view('profile', [
            'title'      => 'My Profile — ' . esc(setting('site_title', 'PCP Locations')),
            'user'       => $user,
            'errors'     => session()->getFlashdata('errors'),
            'success'    => session()->getFlashdata('success'),
            'totalCount' => 0,
        ]);
    }

    public function update(): \CodeIgniter\HTTP\RedirectResponse
    {
        if (session()->get('role') === 'guest') {
            return redirect()->to('/profile')->with('errors', ['Guest accounts cannot be modified.']);
        }

        $userId = (int) session()->get('user_id');
        $model  = new UserModel();

        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$userId}]",
            'email'    => "required|valid_email|max_length[150]|is_unique[users.email,id,{$userId}]",
        ];

        $newPassword = $this->request->getPost('password');
        if ($newPassword !== '') {
            $rules['password']         = 'min_length[8]';
            $rules['password_confirm'] = 'matches[password]';
        }

        if (! $this->validate($rules)) {
            return redirect()->to('/profile')
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $data = [
            'username' => trim($this->request->getPost('username')),
            'email'    => trim($this->request->getPost('email')),
        ];

        if ($newPassword !== '') {
            $data['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        $model->update($userId, $data);

        // Keep session in sync
        session()->set('username', $data['username']);

        return redirect()->to('/profile')->with('success', 'Profile updated.');
    }
}
