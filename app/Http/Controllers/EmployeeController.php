<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Ldap\User;
use App\Ldap\Group;
use LdapRecord\Models\Attributes\AccountControl;
use LdapRecord\Models\Attributes\Password;

class EmployeeController extends Controller
{
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
            ]);

        // Username availability check
        $firstname = $request->employee_firstname;
        $lastname = $request->employee_lastname;
        $username = strtolower(substr($firstname, 0, 1) . $lastname);
        $usernamectr = User::whereContains('cn', $username)->get();
        if(count($usernamectr) >= 1) {
            $ctr = count($usernamectr);
            $username = $username . $ctr++;
        }

        // Set up variable info
        $fullname = $firstname . ' ' . $lastname;
        $email = $username . '@nisgaa.bc.ca';
        // $password = $this->stringGenerator();
        $password = 'SD924now';
        $company = 'SD92';
        $employee_id = $request->employee_id;
        $employee_rfid = $request->employee_rfid;

        // Setting employee object values
        $employee = new User();

        // Set employee object DN value
        $employee->setDn('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        $employee->cn = $username;
        $employee->name = $username;
        $employee->mailnickname = $username;
        $employee->samaccountname = $username;
        $employee->displayname = $fullname;
        $employee->givenname = $firstname;
        $employee->sn = $lastname;
        $employee->mail = $email;
        $employee->unicodepwd = $this->passwordConverter($password);
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
        
        // Set up employee roles
        $roles = $this->setEmployeeRoles($username, $request);

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
        
        return redirect('/cms/employees/' . $username . '/view')
            ->with('status', 'success')
            ->with('message', $message);
    }

    /**
     * Handle process for disabling employee account
     *
     * @param String $username
     */
    public function disableEmployeeProfile (String $username)
    {
        // Process employee roles for disabled account, return employee $fullname
        $fullname = $this->disableEmployeeAccounts($username);

        // Redirect to /employees page if $fullname is NULL
        if($fullname === NULL)
            return redirect('/cms/employees')
                ->with('status', 'danger')
                ->with('message', 'The user you are looking for does not exist in our directory.');

        // Log activity
        $message = 'The account for <b>' . $fullname . '</b> has been disabled successfully.';
        $this->inputLog(session('userName'), $message);
        
        return redirect('/cms/employees')
            ->with('status', 'success')
            ->with('message', $message);
    }
    
    /**
     * Handle process for disabling multiple employee accounts
     *
     * @param \Illuminate\Http\Request $request
     */
    public function disableEmployeeMultiple (Request $request)
    {
        // Set up string of usernames into an array
        $employees = explode(',', rtrim($request->employee_disable, ','));

        // Loop through username array
        foreach($employees as $username): 
            // Process employee roles for disabled account, return employee $fullname
            $fullname = $this->disableEmployeeAccounts($username);
            
            // Log activity per loop
            $message = 'The account for <b>' . $fullname . '</b> has been disabled successfully.';
            $this->inputLog(session('userName'), $message);
        endforeach;
        
        // Log activity for the entire process
        $message = 'Multiple district account(s) have been disabled successfully.';
        $this->inputLog(session('userName'), $message);

        return redirect('/cms/employees')
            ->with('status', 'success')
            ->with('message', $message);
    }

    /**
     * Handle process for moving disabled employee account to appropriate groups
     *
     * @param String $username
     * @return String $fullname
     */
    public function disableEmployeeAccounts (String $username)
    {
        // Set up employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        // Return NULL if $employee is NULL
        if($employee === NULL) return NULL;

        $employee->department = "";
        $employee->description = "Inactive employee";

        // Hide employee from MS Exchange directory list
        $employee->setFirstAttribute('msExchHideFromAddressLists', 'TRUE');

        // Fetch employee groups data
        $employee_groups = $employee->groups()->get();

        // Remove existing groups in K12Admin localgroup list
        foreach($employee_groups as $eg):
            $this->removeEmployeeLocalGroupInK12Admin($employee->getFirstAttribute('samaccountname'), $eg->getName());
        endforeach;

        // Remove all employee groups
        $employee->groups()->detachAll();

        // Add employee to inactive employee list
        $employee_group = Group::findBy('cn', 'inactivestaff');
        $employee->groups()->attach($employee_group);

        // Add employee to oldstaff group
        $employee_group = Group::findBy('cn', 'oldstaff');
        $employee->groups()->attach($employee_group);

        // Add employee to oldstaff group
        $employee_group = Group::findBy('cn', 'nondistrict');
        $employee->groups()->attach($employee_group);

        // Move employee to A1 Staff Assignment to assign A1 license
        $employee_group = Group::findBy('cn', 'A1 Staff Assignment');
        $employee->groups()->attach($employee_group);

        // Add employee to nondistrict/oldstaff group in K12Admin
        $this->setEmployeeLocalGroupInK12Admin($username, '', 'nondistrict');

        // Disable employee ID card
        $this->disableEmployeeIDAccessInK12Admin($employee->getFirstAttribute('uid'));

        // Disable employee account.
        $uac = new AccountControl();
        $uac->accountIsDisabled();

        $employee->userAccountControl = $uac;
        $employee->save();

        return $employee->getFirstAttribute('displayname');
    }

}
