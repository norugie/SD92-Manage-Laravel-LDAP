<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Ldap\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index ()
    {
        $users = User::get();
        return view ( 'cms.employee.employee',[
            'users' => $users
        ]);
    }
}
