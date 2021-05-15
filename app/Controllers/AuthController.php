<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Session\Session;

class AuthController extends Controller
{
    public function logout(Session $session)
    {
        $session->clear();

        return back();
    }
}