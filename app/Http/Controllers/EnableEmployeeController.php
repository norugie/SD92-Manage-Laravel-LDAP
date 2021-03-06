<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UpdateEmployeeController;
use App\Http\Controllers\HelperEmployeeController;
use Illuminate\Http\Request;
use App\Ldap\User;
use App\Ldap\Group;
use LdapRecord\Models\Attributes\AccountControl;

class EnableEmployeeController extends Controller
{
    public function __construct ()
    {
        $this->update = new UpdateEmployeeController;
        $this->helpers = new HelperEmployeeController;
    }

    /**
     * Handle process for re-enabling employee account
     *
     * @param String $username
     */
    public function enableInactiveProfile (String $username)
    {
        // Process employee roles, return $employee object
        $employee = $this->enableInactiveAccounts($username);

        // Log activity
        $message = 'The account for <b><a href="/cms/employees/' . $employee->getFirstAttribute('samaccountname') . '/view" class="alert-link">' . $employee->getFirstAttribute('displayname') . '</a></b> has been re-enabled successfully. Please update the re-enabled account profile.';
        $this->inputLog(session('userName'), $message);

        $this->alertDetails($message, 'success');
        
        return redirect('/cms/employees/' . $employee->getFirstAttribute('samaccountname') . "/update");
    }

    /**
     * Handle process for re-enabling multiple employee accounts
     *
     * @param \Illuminate\Http\Request $request
     */
    public function enableInactiveMultiple (Request $request)
    {

        // Set string of usernames into an array
        $employees = explode(',', rtrim($request->employee_enable, ','));

        // Loop through username array
        foreach($employees as $username): 
            // Process employee roles, return $employee object
            $employee = $this->enableInactiveAccounts($username);

            // Add firstname and lastname info to $request object
            $request->request->add([
                'employee_firstname' => $employee->getFirstAttribute('givenname'),
                'employee_lastname' => $employee->getFirstAttribute('sn')
            ]);

            // Set employee account roles
            $this->update->updateEmployeeRoles($username, $request);

            // Log activity per loop
            $message = 'The account for <b><a href="/cms/employees/' . $employee->getFirstAttribute('samaccountname') . '/view" class="alert-link">' . $employee->getFirstAttribute('displayname') . '</a></b> has been re-enabled successfully.';
            $this->inputLog(session('userName'), $message);
        endforeach;

        // Log activity for the entire process
        $message = 'Multiple district account(s) have been re-enabled successfully.';
        $this->inputLog(session('userName'), $message);

        $this->alertDetails($message, 'success');

        return redirect('/cms/employees');
    }

    /**
     * Handle process for moving re-enabled accounts out of inactive groups
     *
     * @param String $username
     * @return String $fullname
     */
    public function enableInactiveAccounts (String $username)
    {
        // Set employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');
        
        // Check for account availability - only check if account exists
        if ($employee === NULL) {
            $message = 'The user you are looking for does not exist in our directory.';
            $this->alertDetails($message, 'error');
            return redirect('/cms/employees');
        }

        // Unset MS Exchange Hide from Address List option
        $employee->setFirstAttribute('msExchHideFromAddressLists', NULL);

        // Remove disabled account localgroups
        $this->helpers->removeEmployeeLocalGroupInK12Admin($employee->getFirstAttribute('samaccountname'), 'Incoming');
        $this->helpers->removeEmployeeLocalGroupInK12Admin($employee->getFirstAttribute('samaccountname'), 'nondistrict');

        // Remove all employee groups
        $employee->groups()->detachAll();

        // Enable employee account, set up account password to not expire
        $uac = new AccountControl();
        $uac->accountIsNormal();
        $uac->passwordDoesNotExpire();

        $employee->userAccountControl = $uac;
        $employee->save();

        return $employee;
    }
}
