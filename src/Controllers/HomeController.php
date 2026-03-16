<?php

namespace App\Controllers;

use App\Core\View;

class HomeController
{
    public function index()
    {
        if (isset($_SESSION['user_id'])) {
            header("Location: /chat");
            exit;
        }

        View::render('home', [
            'title' => 'Home'
        ]);
    }
}
