<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Ldap\User;
use App\Ldap\Group;
use LdapRecord\Models\Attributes\AccountControl;
use LdapRecord\Models\Attributes\Password;

class EmployeeController extends Controller
{
    public function index ()
    {
        $json = file_get_contents('cms/config.json');
        $config = json_decode($json, true);
        $employees = Group::findBy('cn', 'activestaff')->members()->get();
        return view ( 'cms.employee.employee', [
            'employees' => $employees,
            'config' => $config
        ]);
    }

    public function createEmployeeForm ()
    {
        $json = file_get_contents('cms/config.json');
        $config = json_decode($json, true);
        return view ( 'cms.employee.create.employee', [
            'config' => $config
        ]);
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
        // $password = $this->stringGenerator();
        $password = 'SD924now';
        $company = 'SD92';
        $department = $request->employee_department;
        $locations = $request->employee_locations;
        $employee_roles = $request->employee_roles;
        $roles = [];

        if($employee_roles !== NULL){
            // Remove dept- tag from sub-departments
            foreach($employee_roles as $i):
                if(strpos($i, 'dept-') === FALSE){
                    array_push($roles, $i);
                } else {
                    $i = substr_replace($i, '', 0, 5);
                    array_push($roles, $i);
                }
            endforeach;
        }

        array_push($roles, $department, 'employee', 'activestaff', $this->licensingSorter($employee_roles));
        $roles = array_merge($roles, $locations);

        // Setting employee object values
        $employee = new User();

        $employee->setDn('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        $employee->cn = $username;
        $employee->name = $username;
        $employee->samaccountname = $username;
        $employee->displayname = $fullname;
        $employee->givenname = $firstname;
        $employee->sn = $lastname;
        $employee->mail = $email;
        $employee->unicodepwd = $this->passwordConverter($password);
        $employee->company = $company;
        $employee->department = $department;
        $employee->description = $department . " employee";
        $employee->proxyaddresses = 'SMTP:' . $email;

        $employee->save();

        $employee->refresh();

        // Enable the employee account with password not expiring.
        $uac = new AccountControl();
        $uac->accountIsNormal();
        $uac->passwordDoesNotExpire();

        $employee->userAccountControl = $uac;
        $employee->save();

        if($roles !== NULL){
            // Adding role groups
            foreach($roles as $role): 
                $employee_group = Group::findBy('cn', $role);
                $employee->groups()->attach($employee_group);
            endforeach;
        }
        
        $message = 'An account for <b><a href="/cms/employees/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been created successfully.';

        $this->inputLog(session('userName'), $message);
        
        return redirect('/cms/employees/' . $username . '/view')
            ->with('status', 'success')
            ->with('message', $message);

    }

    public function viewEmployeeProfileUpdate ( String $username, String $action )
    {
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        // Redirect to employee list if employee is NULL
        if($employee === NULL) 
            return redirect('/cms/employees')
                ->with('status', 'danger')
                ->with('message', 'The user you are looking for does not exist in our directory.');

        $groups = $employee->groups()->get();
        $locations = [];
        $sub_departments = [];
        $check = [];

        $json = file_get_contents('cms/config.json');
        $config = json_decode($json, true);

        foreach($config['locations'] as $key => $value): 
            array_push($check, $key);
        endforeach;

        foreach($groups as $group):
            $group = $group->getName();
            if(in_array($group, $check) ? array_push($locations, $group) : array_push($sub_departments, $group));
        endforeach;

        $sub_departments = array_diff($sub_departments, ['employee']);

        if(isset($action) && !empty($action) && $action == 'update' ? $path = 'update.employee' : $path = 'profile');

        return view( 'cms.employee.' . $path, [
            'employee' => $employee,
            'config' => $config,
            'locations' => $locations,
            'sub_departments' => $sub_departments
        ]);
    }

    public function updateEmployeeProfile (String $username, Request $request)
    {
        $firstname = $request->employee_firstname;
        $lastname = $request->employee_lastname;

        $fullname = $firstname . ' ' . $lastname;
        $company = 'SD92';
        $department = $request->employee_department;
        $locations = $request->employee_locations;
        $employee_roles = $request->employee_roles;
        $roles = []; 
        $current_groups = [];

        if($employee_roles !== NULL){
            // remove dept- tag from sub-departments
            foreach($employee_roles as $i):
                if(strpos($i, 'dept-') === FALSE){
                    array_push($roles, $i);
                } else {
                    $i = substr_replace($i, '', 0, 5);
                    array_push($roles, $i);
                }
            endforeach;
        }

        array_push($roles, $department, 'employee', 'activestaff', $this->licensingSorter($employee_roles));
        $roles = array_merge($roles, $locations);

        // Setting employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        $employee->displayname = $fullname;
        $employee->givenname = $firstname;
        $employee->sn = $lastname;
        $employee->department = $department;
        $employee->description = $department . " employee";

        $employee->save();

        $employee->refresh();

        $employee_groups = $employee->groups()->get();

        foreach($employee_groups as $eg):
            array_push($current_groups, $eg->getName());
            // Remove from user's employee groups if not a part of updated locations, roles, and sub-departments
            if($roles !== NULL && !in_array($eg->getName(), $roles)) {
                $employee_group = Group::findBy('cn', $eg->getName());
                $employee->groups()->detach($employee_group);
            }
        endforeach;

        if($roles !== NULL){
            // Adding role groups if not already a part of the user's group
            foreach($roles as $role): 
                if(!in_array($role, $current_groups)) {
                    $employee_group = Group::findBy('cn', $role);
                    $employee->groups()->attach($employee_group);
                }
            endforeach;
        }

        $message = 'The account for <b><a href="/cms/employees/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been updated successfully.';

        $this->inputLog(session('userName'), $message);
        
        return redirect('/cms/employees/' . $username . '/view')
            ->with('status', 'success')
            ->with('message', $message);
    }

    public function updateEmployeeMultiple (Request $request)
    {
        echo $request->employee_multiple . "<br>";
        echo $request->employee_department;
    }

    public function disableEmployeeProfile (String $username)
    {
        // Setting employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');
        $fullname = $employee->getFirstAttribute('displayname');
        $employee->department = "";
        $employee->description = "Inactive employee";

        // Removing all employee groups
        $employee->groups()->detachAll();

        // Adding employee to inactive employee list
        $employee_group = Group::findBy('cn', 'inactivestaff');
        $employee->groups()->attach($employee_group);

        // Disabling employee account.
        $uac = new AccountControl();
        $uac->accountIsDisabled();

        $employee->userAccountControl = $uac;
        $employee->save();

        $message = 'The account for <b>' . $fullname . '</b> has been disabled successfully.';

        $this->inputLog(session('userName'), $message);
        
        return redirect('/cms/employees')
            ->with('status', 'success')
            ->with('message', $message);
    }

    public function stringGenerator ()
    {  
        $length = 8;
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
        
        return substr(str_shuffle(str_repeat($chars, $length)), 0, $length);
    }

    public function passwordConverter (String $password)
    {
        return iconv("UTF-8", "UTF-16LE", '"' . $password . '"');
    }

    public function licensingSorter($groups)
    {
        $license;

        if($groups === NULL) $license = "A1 Staff Assignment";
        else {
            foreach(file('cms/groups-with-a3-license.txt', FILE_IGNORE_NEW_LINES)as $a3):
                if(in_array($a3, $groups) ? $license = "A3 Staff Assignment" : $license = "A1 Staff Assignment");
                if($license === "A3 Staff Assignment") break;
            endforeach;
            
            if(in_array('A3 Staff Exceptions', $groups)) $license = "A1 Staff Assignment";
        }

        return $license;
    }
}
