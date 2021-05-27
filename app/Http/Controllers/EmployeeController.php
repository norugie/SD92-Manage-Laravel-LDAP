<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Ldap\User;
use App\Ldap\Group;

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
        $firstname = $request->employee_firstname;
        $lastname = $request->employee_lastname;
        $fullname = $firstname . " " . $lastname;

        // Username check
        $username = strtolower(substr($firstname, 0, 1) . $lastname);
        $usernamectr = User::whereContains('cn', $username)->get();
        if(count($usernamectr) >= 1) {
            $ctr = count($usernamectr);
            $username = $username . $ctr++;
        }

        $email = $username . "@nisgaa.bc.ca";
        $password = "SD924now!";
        $company = "SD92";
        $department = $request->employee_department;

        $employee = new User();
        
        $employee->cn = $username;
        $employee->displayname = $fullname;
        $employee->givenname = $firstname;
        $employee->sn = $lastname;
        $employee->mail = $email;
        $employee->company = $company;
        $employee->department = $department;

        $employee->save();
        echo $fullname . "<br>" . $username . "<br>" . $email . "<br>" . $password . "<br>" . $department . "<br>" . $company;
        // exit();

    }

    public function stringGenerator ()
    {  
        $length = 8;
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
        
        return substr(str_shuffle(str_repeat($chars, $length)),0,$length);
    }
}
