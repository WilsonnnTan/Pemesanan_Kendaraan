<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SessionModel;
use App\Models\UserModel;

class AdminFilter implements FilterInterface
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

        // Cek session di database
        $session = $this->sessionModel->getValidSession($accessToken);
        
        if (!$session) {
            // Hapus cookie jika session tidak valid
            $response = service('response');
            $response->deleteCookie('access_token');
            return redirect()->to('/auth/login');
        }

        // Cek role user
        $user = $this->userModel->find($session['user_id']);
        if (!$user || !in_array($user['role'], ['admin', 'approver'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        // Refresh session jika masih aktif
        $this->sessionModel->refreshSession($session['id']);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
} 