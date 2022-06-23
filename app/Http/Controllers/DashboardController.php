<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Alert;

class DashboardController extends Controller
{
    public function index ()
    {
        Alert::alert('Title', 'Message', 'Type');

        return view ( 'cms.dashboard.dashboard', [
            'logs' => $this->requestLog()
        ]);
    }
}
