<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\SessionModel;
use App\Models\UserModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'form', 'auth'];

    protected $sessionModel;
    protected $userModel;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        $this->sessionModel = new SessionModel();
        $this->userModel = new UserModel();
    }

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    protected function isLoggedIn()
    {
        $accessToken = $this->request->getCookie('access_token');
        if (!$accessToken) {
            return false;
        }

        return $this->sessionModel->isValidSession($accessToken);
    }

    protected function getCurrentUser()
    {
        $accessToken = $this->request->getCookie('access_token');
        if (!$accessToken) {
            return null;
        }

        $session = $this->sessionModel->getValidSession($accessToken);
        if (!$session) {
            return null;
        }

        // Refresh session jika masih aktif
        $this->sessionModel->refreshSession($session['id']);

        return $this->userModel->find($session['user_id']);
    }

    /**
     * Check if user is admin
     *
     * @return bool
     */
    protected function isAdmin()
    {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === 'admin';
    }

    /**
     * Require login for protected routes
     *
     * @return void
     */
    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/auth/login');
        }
    }

    /**
     * Require admin role for protected routes
     *
     * @return void
     */
    protected function requireAdmin()
    {
        if (!$this->isAdmin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk halaman ini');
        }
    }

    protected function renderView($view, $data = [])
    {
        // Tambahkan data user ke view jika user sudah login
        if ($this->isLoggedIn()) {
            $data['user'] = $this->getCurrentUser();
        }

        return view($view, $data);
    }
}
