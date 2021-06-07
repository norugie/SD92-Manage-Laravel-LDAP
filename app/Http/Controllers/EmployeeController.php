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

        $fullname = $firstname . ' ' . $lastname;
        $email = $username . '@nisgaa.bc.ca';
        $password = $this->stringGenerator();
        $company = 'SD92';
        $department = $request->employee_department;
        $locations = $request->employee_locations;
        $roles = []; 
        $sub_departments = [];

        // Separate roles from sub-departments
        foreach($request->employee_roles as $i):
            if(strpos($i, 'dept-') === FALSE){
                array_push($roles, $i);
            } else {
                $i = substr_replace($i, '', 0, 5);
                array_push($sub_departments, $i);
            }
        endforeach;

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
        $employee->description = $department . " employee";
        $employee->proxyaddresses = 'SMTP:' . $email;

        $employee->setDn('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        $employee->save();

        $employee->refresh();

        // Enable the user. Try again with SSL connection in the future
        // $employee->userAccountControl = 512;
        // $employee->save();

        // Adding to employee group
        $employee_group = Group::findBy('cn', 'employee');
        $employee->groups()->attach($employee_group);

        // Adding to location groups
        foreach($locations as $location): 
            $employee_group = Group::findBy('cn', $location);
            $employee->groups()->attach($employee_group);
        endforeach;

        // Adding role groups
        foreach($roles as $role): 
            $employee_group = Group::findBy('cn', $role);
            $employee->groups()->attach($employee_group);
        endforeach;

        // Adding sub-department groups
        foreach($sub_departments as $sub): 
            $employee_group = Group::findBy('cn', $sub);
            $employee->groups()->attach($employee_group);
        endforeach;

        // echo $fullname . "<br>" . $username . "<br>" . $email . "<br>" . $password . "<br>department: " . $department . "<br>locations: ";
        // var_dump($locations);
        // echo "<br>roles: ";
        // var_dump($roles);
        // echo "<br>sub-departments: ";
        // var_dump($sub_departments);

        $message = 'An account for <b>' . $fullname . '</b> has been created successfully. <a href="/cms/employees/' . $username . '" class="alert-link">See account details here</a>.';
        
        return redirect('/cms/employees')
            ->with('status', 'success')
            ->with('message', $message);

    }

    public function viewEmployeeProfile( String $username ){
        return view( 'cms.employee.profile' );
    }

    public function stringGenerator ()
    {  
        $length = 8;
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
        
        return substr(str_shuffle(str_repeat($chars, $length)), 0, $length);
    }
}
