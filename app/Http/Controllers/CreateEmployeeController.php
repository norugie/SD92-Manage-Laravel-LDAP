<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperEmployeeController;
use Illuminate\Http\Request;
use App\Ldap\User;
use App\Ldap\Group;
use LdapRecord\Models\Attributes\AccountControl;

class CreateEmployeeController extends Controller
{
    public function __construct ()
    {
        $this->helpers = new HelperEmployeeController;
    }

    /**
     * Handle process for creating employee account
     *
     * @param \Illuminate\Http\Request $request
     */
    public function createEmployee (Request $request)
    {
        // Backend validation for POST $request
        $request->validate( 
            [
                'employee_firstname' => 'required',
                'employee_lastname' => 'required',
                'employee_department' => 'required'
            ],
            [
                'employee_firstname.required' => 'This field is required.',
                'employee_lastname.required' => 'This field is required.',
                'employee_department.required' => 'Select employee department/school'
            ]
        );

        // Username availability check
        $firstname = $request->employee_firstname;
        $lastname = $request->employee_lastname;
        $username = strtolower(substr($firstname, 0, 1) . $lastname);
        $usernamectr = User::whereContains('cn', $username)->get();
        if(count($usernamectr) >= 1) {
            $ctr = count($usernamectr);
            $username = $username . $ctr++;
        }

        // Set variable info
        $fullname = $firstname . ' ' . $lastname;
        $email = $username . '@nisgaa.bc.ca';
        $password = $this->helpers->stringGenerator();
        $company = 'SD92';
        $employee_id = $request->employee_id;
        $employee_rfid = $request->employee_rfid;

        // Setting employee object values
        $employee = new User();

        // Set employee object DN value
        $employee->setDn('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        $employee->cn = $username;
        $employee->name = $username;
        $employee->uid = $username;
        $employee->mailnickname = $username;
        $employee->samaccountname = $username;
        $employee->displayname = $fullname;
        $employee->givenname = $firstname;
        $employee->sn = $lastname;
        $employee->mail = $email;
        $employee->unicodepwd = $this->helpers->passwordConverter($password);
        $employee->company = $company;
        $employee->proxyaddresses = 'SMTP:' . $email;
        $employee->employeeID = $employee_id;
        $employee->employeeNumber = $employee_rfid;

        // Save set object values for employee
        $employee->save();
        $employee->refresh();

        // Set UAC values. Enable the employee account with password not expiring.
        $uac = new AccountControl();
        $uac->accountIsNormal();
        $uac->passwordDoesNotExpire();

        $employee->userAccountControl = $uac;
        $employee->save();

        // Set employee roles
        $roles = $this->helpers->setEmployeeRoles($username, $request);

        // Set employee initial password in K12Admin
        $this->helpers->setEmployeePasswordInK12Admin($username, $password);

        if($roles !== NULL){
            // Adding role groups
            foreach($roles as $role): 
                $employee_group = Group::findBy('cn', $role);
                $employee->groups()->attach($employee_group);
            endforeach;
        }
        
        // Log activity
        $message = 'An account for <b><a href="/cms/employees/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been created successfully.';
        $this->inputLog(session('userName'), $message);
        $message =  $message . ' Please take note of the password for this user before refreshing the page: <b>' . $password . '</b>';
        
        return redirect('/cms/employees/' . $username . '/view')
            ->with('status', 'success')
            ->with('message', $message);
    }
}
