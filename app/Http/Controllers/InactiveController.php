<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;
use App\Ldap\User;
use App\Ldap\Group;
use LdapRecord\Models\Attributes\AccountControl;

class InactiveController extends Controller
{
    public function index ()
    {
        $json = file_get_contents('cms/config.json');
        $config = json_decode($json, true);
        $employees = Group::findBy('cn', 'inactivestaff')->members()->get();
        return view ( 'cms.inactive.inactive', [
            'employees' => $employees,
            'config' => $config
        ]);
    }

    public function enableInactiveAccounts (String $username)
    {
        // Setting employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');
        $fullname = $employee->getFirstAttribute('displayname');

        // Removing all employee groups
        $employee->groups()->detachAll();

        // Enabling employee account.
        $uac = new AccountControl();
        $uac->accountIsNormal();
        $uac->passwordDoesNotExpire();

        $employee->userAccountControl = $uac;
        $employee->save();

        return $employee->getFirstAttribute('displayname');
    }

    public function enableInactiveProfile ( String $username )
    {
        $fullname = $this->enableInactiveAccounts($username);
        
        $message = 'The account for <b><a href="/cms/employees/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been re-enabled successfully. Please update the re-enabled account profile.';

        $this->inputLog(session('userName'), $message);
        
        return redirect('/cms/employees/' . $username . "/update")
            ->with('status', 'success')
            ->with('message', $message);
    }

    public function enableInactiveMultiple (Request $request)
    {
        $update = new EmployeeController();
        $employees = explode(',', rtrim($request->employee_enable, ','));
        foreach($employees as $username): 
            $fullname = $this->enableInactiveAccounts($username);
            $update->updateEmployeeRoles($username, $request);

            $message = 'The account for <b><a href="/cms/employees/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been re-enabled successfully.';
            $this->inputLog(session('userName'), $message);
        endforeach;

        $message = 'Multiple district account(s) have been re-enabled successfully.';
        $this->inputLog(session('userName'), $message);

        return redirect('/cms/employees')
            ->with('status', 'success')
            ->with('message', $message);
    }

}
