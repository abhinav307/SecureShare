<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\File;

class DashboardController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Protect route
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
    }

    public function index()
    {
        header("Location: /chat");
        exit;
    }
}
