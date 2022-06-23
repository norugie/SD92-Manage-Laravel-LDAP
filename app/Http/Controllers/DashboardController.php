<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Alert;

class DashboardController extends Controller
{
    public function index ()
    {
        return view ( 'cms.dashboard.dashboard', [
            'logs' => $this->requestLog()
        ]);
    }
}
