<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login(): string
    {
        if (session()->get('user_id')) {
            return redirect()->to('/');
        }

        return view('auth/login', [
            'error' => session()->getFlashdata('error'),
        ]);
    }

    public function loginPost(): \CodeIgniter\HTTP\RedirectResponse
    {
        $username = trim($this->request->getPost('username') ?? '');
        $password = $this->request->getPost('password') ?? '';

        if ($username === '' || $password === '') {
            return redirect()->to('/login')->with('error', 'Please enter your username and password.');
        }

        $user = (new UserModel())->findByUsername($username);

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->to('/login')->with('error', 'Invalid username or password.');
        }

        session()->set([
            'user_id'   => $user['id'],
            'username'  => $user['username'],
            'role'      => $user['role'] ?? 'user',
            'logged_in' => true,
        ]);

        $redirectTo = session()->get('redirect_after_login') ?? '/';
        session()->remove('redirect_after_login');

        return redirect()->to($redirectTo);
    }

    public function logout(): \CodeIgniter\HTTP\RedirectResponse
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
