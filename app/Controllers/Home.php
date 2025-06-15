<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        if ($this->isLoggedIn()) {
            return redirect()->to('/dashboard');
        }

        return $this->renderView('auth/login');
    }
}
