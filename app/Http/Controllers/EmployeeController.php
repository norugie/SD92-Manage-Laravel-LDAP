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
    /**
     * Return data for /employees page
     */
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

    /**
     * Return data for /employees/create page
     */
    public function createEmployeeForm ()
    {
        $json = file_get_contents('cms/config.json');
        $config = json_decode($json, true);
        return view ( 'cms.employee.create.employee', [
            'config' => $config
        ]);
    }

    /**
     * Handle process for creating employee account
     *
     * @param  \Illuminate\Http\Request  $request
     */
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

        // Setting up variable info
        $fullname = $firstname . ' ' . $lastname;
        $email = $username . '@nisgaa.bc.ca';
        // $password = $this->stringGenerator();
        $password = 'SD924now';
        $company = 'SD92';
        // $department = $request->employee_department;
        // $locations = $request->employee_locations;
        // $employee_roles = $request->employee_roles;
        // $roles = [];

        // // Separate sub-departments from roles
        // if($employee_roles !== NULL){
        //     // Remove dept- tag from sub-departments
        //     foreach($employee_roles as $i):
        //         if(strpos($i, 'dept-') === FALSE){
        //             array_push($roles, $i);
        //         } else {
        //             $i = substr_replace($i, '', 0, 5);
        //             array_push($roles, $i);
        //         }
        //     endforeach;
        // }

        // // Push default roles to $roles array and merge $roles and $location into one array
        // array_push($roles, $department, 'employee', 'activestaff', $this->licensingSorter($employee_roles));
        // $roles = array_merge($roles, $locations);

        // Setting employee object values
        $employee = new User();

        // Set employee object DN value
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
        // $employee->department = $department;
        // $employee->description = $department . " employee";
        $employee->proxyaddresses = 'SMTP:' . $email;

        // Save set object values for employee
        $employee->save();
        $employee->refresh();

        // Set UAC values. Enable the employee account with password not expiring.
        $uac = new AccountControl();
        $uac->accountIsNormal();
        $uac->passwordDoesNotExpire();

        $employee->userAccountControl = $uac;
        $employee->save();
        
        // Setting up employee roles
        $roles = $this->setEmployeeroles($username, $request);

        if($roles !== NULL){
            // Adding role groups
            foreach($roles as $role): 
                $employee_group = Group::findBy('cn', $role);
                $employee->groups()->attach($employee_group);
            endforeach;
        }
        
        // Logging activity
        $message = 'An account for <b><a href="/cms/employees/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been created successfully.';
        $this->inputLog(session('userName'), $message);
        
        return redirect('/cms/employees/' . $username . '/view')
            ->with('status', 'success')
            ->with('message', $message);

    }

    /**
     * Return data for /employees/{username}/{action} page
     *
     * @param String $username
     * @param String $action
     */
    public function viewEmployeeProfileUpdate ( String $username, String $action )
    {
        // Fetching employee data
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        // Redirect to /employees page if {username} is NULL
        if($employee === NULL) 
            return redirect('/cms/employees')
                ->with('status', 'danger')
                ->with('message', 'The user you are looking for does not exist in our directory.');

        // Fetching employee groups data
        $groups = $employee->groups()->get();
        $locations = [];
        $sub_departments = [];
        $check = [];

        // Fetching config setup for locations, roles, and sub-departments
        $json = file_get_contents('cms/config.json');
        $config = json_decode($json, true);

        // Setting up $check to compare against config setup
        foreach($config['locations'] as $key => $value): 
            array_push($check, $key);
        endforeach;

        // Separating set grous to $locations and $sub_departments, based off of $check value compared against config setup
        foreach($groups as $group):
            $group = $group->getName();
            if(in_array($group, $check) ? array_push($locations, $group) : array_push($sub_departments, $group));
        endforeach;

        // Setting up path based on {action}
        // Default {action} value = "view"
        // Redirect paths {action} value = "view" - /employees/{username}/view, {action} value = "update" - /employees/{username}/update
        if(isset($action) && !empty($action) && $action == 'update' ? $path = 'update.employee' : $path = 'profile');

        return view( 'cms.employee.' . $path, [
            'employee' => $employee,
            'config' => $config,
            'locations' => $locations,
            'sub_departments' => $sub_departments
        ]);
    }

    /**
     * Handle process for updating employee account
     *
     * @param String $username
     * @param  \Illuminate\Http\Request  $request
     */
    public function updateEmployeeProfile (String $username, Request $request)
    {
        // Setting variable info
        $firstname = $request->employee_firstname;
        $lastname = $request->employee_lastname;
        $fullname = $firstname . ' ' . $lastname;
        $company = 'SD92';
        $department = $request->employee_department;
        $locations = $request->employee_locations;
        $employee_roles = $request->employee_roles;

        // Setting employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        $employee->displayname = $fullname;
        $employee->givenname = $firstname;
        $employee->sn = $lastname;

        // Save set object values for employee
        $employee->save();
        $employee->refresh();

        // Set employee account roles
        $this->updateEmployeeRoles($username, $request);

        // Logging activity
        $message = 'The account for <b><a href="/cms/employees/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been updated successfully.';
        $this->inputLog(session('userName'), $message);
        
        return redirect('/cms/employees/' . $username . '/view')
            ->with('status', 'success')
            ->with('message', $message);
    }

    /**
     * Handle process for updating employee account roles
     *
     * @param String $username
     * @param  \Illuminate\Http\Request  $request
     * @return String $fullname
     */
    public function updateEmployeeRoles (String $username, Request $request)
    {
        // Setting variable info
        $roles = $this->setEmployeeroles($username, $request);
        $current_groups = [];

        // Setting employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        // Fetching employee groups data
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

        return $employee->getFirstAttribute('displayname');
    }

    public function updateEmployeeRolesMultiple (Request $request)
    {
        $employees = explode(',', rtrim($request->employee_multiple, ','));

        foreach($employees as $username): 
            $fullname = $this->updateEmployeeRoles($username, $request);
            
            $message = 'The account for <b><a href="/cms/employees/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been updated successfully.';
            $this->inputLog(session('userName'), $message);
        endforeach;
        
        $message = 'The department/role(s) for multiple district accounts has been updated successfully.';
        $this->inputLog(session('userName'), $message);

        return redirect('/cms/employees')
        ->with('status', 'success')
        ->with('message', $message);
    }

    public function setEmployeeroles (String $username, Request $request)
    {
        // Setting variable info
        $roles = []; 
        $department = $request->employee_department;
        $locations = $request->employee_locations;
        $employee_roles = $request->employee_roles;

        // Separate sub-departments from roles
        if($employee_roles !== NULL){
            // remove dept- tag from sub-departments
            foreach($employee_roles as $i):
                if(strpos($i, 'dept-') === FALSE) array_push($roles, $i);
                else {
                    $i = substr_replace($i, '', 0, 5);
                    array_push($roles, $i);
                }
            endforeach;
        }

        // Push default roles to $roles array and merge $roles and $location into one array
        array_push($roles, $department, 'employee', 'activestaff', $this->licensingSorter($employee_roles));
        if($locations !== NULL) $roles = array_merge($roles, $locations);

        // Setting employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');
        $employee->department = $department;
        $employee->description = $department . " employee";

        // Save set object values for employee
        $employee->save();
        $employee->refresh();

        return $roles;
    }

    public function disableEmployeeAccounts (String $username)
    {
        // Setting employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');
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

        return $employee->getFirstAttribute('displayname');
    }

    public function disableEmployeeProfile (String $username)
    {
        $fullname = $this->disableEmployeeAccounts($username);

        $message = 'The account for <b>' . $fullname . '</b> has been disabled successfully.';

        $this->inputLog(session('userName'), $message);
        
        return redirect('/cms/employees')
            ->with('status', 'success')
            ->with('message', $message);
    }
    
    public function disableEmployeeMultiple (Request $request)
    {
        $employees = explode(',', rtrim($request->employee_disable, ','));

        foreach($employees as $username): 
            $fullname = $this->disableEmployeeAccounts($username);
            
            $message = 'The account for <b>' . $fullname . '</b> has been disabled successfully.';
            $this->inputLog(session('userName'), $message);
        endforeach;
        
        $message = 'Multiple district account(s) have been disabled successfully.';
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
