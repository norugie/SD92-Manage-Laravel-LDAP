<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Ldap\User;
use App\Ldap\Group;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index ()
    {
        $employees = Group::find('CN=employee,CN=Users,DC=nisgaa,DC=bc,DC=ca')->members()->get();
        return view ( 'cms.employee.employee',[
            'employees' => $employees
        ]);
        // var_dump($employees);
        // exit();
    }

    public function createEmployeeForm ()
    {
        return view ( 'cms.employee.create.employee' );
    }

    public function createEmployee (Request $request)
    {
        dd($request);
        exit();
    }
}
