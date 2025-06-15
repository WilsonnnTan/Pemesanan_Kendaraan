<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SessionModel;
use App\Models\UserModel;

class AuthFilter implements FilterInterface
{
    protected $sessionModel;
    protected $userModel;

    public function __construct()
    {
        $this->sessionModel = new SessionModel();
        $this->userModel = new UserModel();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        // Cek apakah user sudah login
        $accessToken = $request->getCookie('access_token');
        
        if (!$accessToken) {
            return redirect()->to('/auth/login');
        }

        // Cek session di database menggunakan model
        if (!$this->sessionModel->isValidSession($accessToken)) {
            // Hapus cookie jika session tidak valid
            $response = service('response');
            $response->deleteCookie('access_token');
            return redirect()->to('/auth/login');
        }

        // Refresh session jika masih aktif
        $session = $this->sessionModel->getValidSession($accessToken);
        $this->sessionModel->refreshSession($session['id']);

        // Cek user masih ada
        $user = $this->userModel->find($session['user_id']);
        if (!$user) {
            // Hapus session dan cookie
            $this->sessionModel->delete($session['id']);
            $response = service('response');
            $response->deleteCookie('access_token');
            return redirect()->to('/auth/login')->with('error', 'User tidak ditemukan');
        }

        // Set user data ke request untuk digunakan di controller
        $request->user = $user;
        
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
} 