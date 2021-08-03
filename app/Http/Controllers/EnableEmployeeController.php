<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UpdateEmployeeController;
use Illuminate\Http\Request;
use App\Ldap\User;
use App\Ldap\Group;
use LdapRecord\Models\Attributes\AccountControl;

class EnableEmployeeController extends Controller
{
    public function __construct ()
    {
        $this->update = new UpdateEmployeeController;
    }

    /**
     * Handle process for re-enabling employee account
     *
     * @param String $username
     */
    public function enableInactiveProfile (String $username)
    {
        // Process employee roles, return employee $fullname
        $fullname = $this->enableInactiveAccounts($username);

        // Redirect to /employees page if $fullname is NULL
        if($fullname === NULL)
            return redirect('/cms/employees')
                ->with('status', 'danger')
                ->with('message', 'The user you are looking for does not exist in our directory.');
        
        // Log activity
        $message = 'The account for <b><a href="/cms/employees/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been re-enabled successfully. Please update the re-enabled account profile.';
        $this->inputLog(session('userName'), $message);
        
        return redirect('/cms/employees/' . $username . "/update")
            ->with('status', 'success')
            ->with('message', $message);
    }

    /**
     * Handle process for re-enabling multiple employee accounts
     *
     * @param \Illuminate\Http\Request $request
     */
    public function enableInactiveMultiple (Request $request)
    {

        // Set up string of usernames into an array
        $employees = explode(',', rtrim($request->employee_enable, ','));

        // Loop through username array
        foreach($employees as $username): 
            // Process employee roles, return employee $fullname
            $fullname = $this->enableInactiveAccounts($username);

            // Set up employee account roles
            $this->update->updateEmployeeRoles($username, $request);

            // Log activity per loop
            $message = 'The account for <b><a href="/cms/employees/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been re-enabled successfully.';
            $this->inputLog(session('userName'), $message);
        endforeach;

        // Log activity for the entire process
        $message = 'Multiple district account(s) have been re-enabled successfully.';
        $this->inputLog(session('userName'), $message);

        return redirect('/cms/employees')
            ->with('status', 'success')
            ->with('message', $message);
    }

    /**
     * Handle process for moving re-enabled accounts out of inactive groups
     *
     * @param String $username
     * @return String $fullname
     */
    public function enableInactiveAccounts (String $username)
    {
        // Set up employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');
        
        // Unset MS Exchange Hide from Address List option
        $employee->setFirstAttribute('msExchHideFromAddressLists', '');

        // Return NULL if $employee is NULL
        if($employee === NULL) return NULL;

        // Remove all employee groups
        $employee->groups()->detachAll();

        // Enable employee account, set up account password to not expire
        $uac = new AccountControl();
        $uac->accountIsNormal();
        $uac->passwordDoesNotExpire();

        $employee->userAccountControl = $uac;
        $employee->save();

        return $employee->getFirstAttribute('displayname');
    }
}