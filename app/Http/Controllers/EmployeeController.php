<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use LdapRecord\Models\Attributes\Password;
use App\Ldap\User;
use App\Ldap\Group;

class EmployeeController extends Controller
{
    public function index ()
    {
        $employees = Group::findBy('cn', 'employee')->members()->get();
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
        // Username check
        $firstname = $request->employee_firstname;
        $lastname = $request->employee_lastname;
        $username = strtolower(substr($firstname, 0, 1) . $lastname);
        $usernamectr = User::whereContains('cn', $username)->get();
        if(count($usernamectr) >= 1) {
            $ctr = count($usernamectr);
            $username = $username . $ctr++;
        }

        $fullname = $firstname . " " . $lastname;
        $email = $username . "@nisgaa.bc.ca";
        $password = "SD924now!";
        $company = "SD92";
        $department = $request->employee_department;

        // Setting employee object values
        $employee = new User();
        
        $employee->cn = $username;
        $employee->name = $username;
        $employee->samaccountname = $username;
        $employee->displayname = $fullname;
        $employee->givenname = $firstname;
        $employee->sn = $lastname;
        $employee->mail = $email;
        // $employee->unicodePwd = Password::encode($password); // Will work on this again once I have a server for this webapp that goes through SSL connection
        $employee->company = $company;
        $employee->department = $department;
        $employee->proxyaddresses = "SMTP:" . $email;

        $employee->save();

        $employee->refresh();

        // Enable the user. Try again with SSL connection in the future
        // $employee->userAccountControl = 512;
        // $employee->save();

        // Adding to employee group
        $employee_group = Group::findBy('cn', 'employee');
        $employee->groups()->attach($employee_group);

        echo $fullname . "<br>" . $username . "<br>" . $email . "<br>" . $password . "<br>" . $department . "<br>" . $company;
        // exit();

    }

    // public function stringGenerator ()
    // {  
    //     $length = 8;
    //     $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
        
    //     return substr(str_shuffle(str_repeat($chars, $length)),0,$length);
    // }
}
