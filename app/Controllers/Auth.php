<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SessionModel;

class Auth extends BaseController
{
    protected $userModel;
    protected $sessionModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->sessionModel = new SessionModel();
    }

    public function index()
    {
        return redirect()->to('/login');
    }

    public function login()
    {
        // Jika sudah login, redirect ke dashboard
        if ($this->isLoggedIn()) {
            return redirect()->to('/dashboard');
        }

        // Jika method POST, proses login
        if ($this->request->getMethod() === 'POST') {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            $user = $this->userModel->findByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                // Hapus session lama user ini
                $this->sessionModel->deleteUserSessions($user['id']);

                // Buat session baru
                $session = $this->sessionModel->createNewSession($user['id']);

                if (!$session) {
                    return redirect()->back()->with('error', 'Gagal membuat session');
                }

                // Set cookie dengan access token menggunakan setcookie() langsung
                setcookie(
                    'access_token',
                    $session['access_token'],
                    [
                        'expires' => strtotime('+30 days'),
                        'path' => '/',
                        'domain' => '',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]
                );

                return redirect()->to('/dashboard');
            }

            return redirect()->back()->with('error', 'Username atau password salah');
        }

        // Tampilkan form login
        return view('auth/login');
    }

    public function logout()
    {
        // Dapatkan token dari cookie
        $accessToken = $this->request->getCookie('access_token');
        
        if ($accessToken) {
            // Hapus session dari database menggunakan model
            $this->sessionModel->deleteSession($accessToken);
        }
        
        // Hapus cookie
        $this->response->deleteCookie('access_token');
        
        return redirect()->to('/');
    }
} 