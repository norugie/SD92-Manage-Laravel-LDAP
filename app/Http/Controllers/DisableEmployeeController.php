<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperEmployeeController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Ldap\User;
use App\Ldap\Group;
use LdapRecord\Models\Attributes\AccountControl;

class DisableEmployeeController extends Controller
{
    public function __construct ()
    {
        $this->helpers = new HelperEmployeeController;
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
        
        $this->alertDetails($message, 'success');

        return redirect('/cms/employees');
    }
    
    /**
     * Handle process for disabling multiple employee accounts
     *
     * @param \Illuminate\Http\Request $request
     */
    public function disableEmployeeMultiple (Request $request)
    {
        // Set string of usernames into an array
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

        $this->alertDetails($message, 'success');

        return redirect('/cms/employees');
    }

    /**
     * Handle process for moving disabled employee account to appropriate groups
     *
     * @param String $username
     * @return String $fullname
     */
    public function disableEmployeeAccounts (String $username)
    {
        // Set employee object values
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
            $this->helpers->removeEmployeeLocalGroupInK12Admin($employee->getFirstAttribute('samaccountname'), $eg->getName());
        endforeach;

        // Remove all employee groups
        $employee->groups()->detachAll();

        // Add employee to inactive employee list
        $employee_group = Group::findBy('cn', 'inactivestaff');
        $employee->groups()->attach($employee_group);

        // Add employee to oldstaff group
        $employee_group = Group::findBy('cn', 'oldstaff');
        $employee->groups()->attach($employee_group);

        // Add employee to nondistrict group
        $employee_group = Group::findBy('cn', 'nondistrict');
        $employee->groups()->attach($employee_group);

        // Move employee to A1 Staff Assignment to assign A1 license
        $employee_group = Group::findBy('cn', 'A1 Staff Assignment');
        $employee->groups()->attach($employee_group);

        // Add employee to nondistrict/oldstaff group in K12Admin
        $this->helpers->setEmployeeLocalGroupInK12Admin($username, '', 'nondistrict');
        $this->helpers->setEmployeeLocalGroupInK12Admin($username, '', 'Incoming');

        // Disable employee ID card
        $this->helpers->disableEmployeeAllIDAccessInK12Admin($employee->getFirstAttribute('uidNumber'));
        $this->helpers->disableEmployeeIDInK12Admin($employee->getFirstAttribute('uidNumber'));

        // Set account description to oldstaff in K12Admin
        $this->helpers->setEmployeeCommentInK12Admin($employee->getFirstAttribute('samaccountname'), 'oldstaff');

        // Disable employee account.
        $uac = new AccountControl();
        $uac->accountIsDisabled();

        $employee->userAccountControl = $uac;
        $employee->save();

        return $employee->getFirstAttribute('displayname');
    }
}
