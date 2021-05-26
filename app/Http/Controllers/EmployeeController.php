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
        $username = strtolower(substr($firstname, 0, 1) . $lastname);
        $email = $username . "@nisgaa.bc.ca";
        $password = 
        $hash_password = Hash::make('SD924now!');
        $company = 'SD92';
        $department = $request->employee_department;

        // $employee = new User();
        
        // $employee->cn = $fullname;
        // $employee->givenname = $firstname;
        // $employee->sn = $lastname;
        // $employee->company = "SD92";
        // $employee->department = $department;

        // $employee->save();
        echo $fullname . "<br>" . $username . "<br>" . $email . "<br>" . $password . "<br>" . $department . "<br>" . $company;
        exit();

    }
}
