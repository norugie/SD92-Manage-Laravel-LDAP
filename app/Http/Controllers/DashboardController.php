<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Alert;

class DashboardController extends Controller
{
    public function index ()
    {
        $message = 'An account for <b><a href="/cms/employees/rbarrameda/view" class="alert-link">Rugie Ann Barrameda</a></b> has been created successfully.';
        $message =  $message . '<br><br>Please take note of the initial password for this user before closing this notice: <b>12345test</b>';
        
        Alert::html('New Account Created', $message, 'success');
        // ->persistent(true)
        // ->showCloseButton()
        // ->showConfirmButton('CLOSE', '#607d8b');

        return view ( 'cms.dashboard.dashboard', [
            'logs' => $this->requestLog()
        ]);
    }
}
