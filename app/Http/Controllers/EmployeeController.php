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
        $employees = Group::findBy('cn', 'activestaff')->members()->get();
        return view ( 'cms.employee.employee', [
            'employees' => $employees
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
        $roles = []; 
        $sub_departments = [];

        if($request->employee_roles !== NULL){
            // Separate roles from sub-departments
            foreach($request->employee_roles as $i):
                if(strpos($i, 'dept-') === FALSE){
                    array_push($roles, $i);
                } else {
                    $i = substr_replace($i, '', 0, 5);
                    array_push($sub_departments, $i);
                }
            endforeach;
        }

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
        $employee->unicodepwd = $this->passwordConverter($password); // Will work on this again once I have a server for this webapp that goes through SSL connection
        $employee->company = $company;
        $employee->department = $department;
        $employee->description = $department . " employee";
        $employee->proxyaddresses = 'SMTP:' . $email;

        $employee->save();

        $employee->refresh();

        // Enable the user. Try again with SSL connection in the future
        $employee->userAccountControl = 512;
        $employee->save();

        // Adding to employee group
        $employee_group = Group::findBy('cn', 'employee');
        $employee->groups()->attach($employee_group);

        // Adding to activestaff group
        $employee_group = Group::findBy('cn', 'activestaff');
        $employee->groups()->attach($employee_group);

        if($locations !== NULL){
            // Adding to location groups
            foreach($locations as $location): 
                $employee_group = Group::findBy('cn', $location);
                $employee->groups()->attach($employee_group);
            endforeach;
        }

        if($roles !== NULL){
            // Adding role groups
            foreach($roles as $role): 
                $employee_group = Group::findBy('cn', $role);
                $employee->groups()->attach($employee_group);
            endforeach;
        }

        if($sub_departments !== NULL){
            // Adding sub-department groups
            foreach($sub_departments as $sub): 
                $employee_group = Group::findBy('cn', $sub);
                $employee->groups()->attach($employee_group);
            endforeach;
        }
        
        $message = 'An account for <b><a href="/cms/employees/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been created successfully.';

        $this->inputLog(session('userName'), $message);
        
        return redirect('/cms/employees/' . $username . '/view')
            ->with('status', 'success')
            ->with('message', $message);

    }

    public function viewEmployeeProfileUpdate ( String $username, String $action ){
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
        $roles = []; 
        $sub_departments = [];
        $current_groups = [];

        if(isset($request->employee_roles)){
            // Separate roles from sub-departments
            foreach($request->employee_roles as $i):
                if(strpos($i, 'dept-') === FALSE){
                    array_push($roles, $i);
                } else {
                    $i = substr_replace($i, '', 0, 5);
                    array_push($sub_departments, $i);
                }
            endforeach;
        }

        array_push($roles, 'employee', 'activestaff');

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
            if(($locations !== NULL && $roles !== NULL && $sub_departments !== NULL) && (!in_array($eg->getName(), $locations) && !in_array($eg->getName(), $roles) && !in_array($eg->getName(), $sub_departments))) {
                $employee_group = Group::findBy('cn', $eg->getName());
                $employee->groups()->detach($employee_group);
            }
        endforeach;

        if($locations !== NULL){
            // Adding to location groups if not already a part of the user's group
            foreach($locations as $location): 
                if(!in_array($location, $current_groups)) {
                    $employee_group = Group::findBy('cn', $location);
                    $employee->groups()->attach($employee_group);
                }
            endforeach;
        }

        if($roles !== NULL){
            // Adding role groups if not already a part of the user's group
            foreach($roles as $role): 
                if(!in_array($role, $current_groups)) {
                    $employee_group = Group::findBy('cn', $role);
                    $employee->groups()->attach($employee_group);
                }
            endforeach;
        }

        if($sub_departments !== NULL){
            // Adding sub-department groups if not already a part of the user's group
            foreach($sub_departments as $sub): 
                if(!in_array($sub, $current_groups)) {
                    $employee_group = Group::findBy('cn', $sub);
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

    public function disableEmployeeProfile ()
    {
        // 
    }

    public function stringGenerator ()
    {  
        $length = 8;
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
        
        return substr(str_shuffle(str_repeat($chars, $length)), 0, $length);
    }

    public function passwordConverter ($password)
    {
        return iconv("UTF-8", "UTF-16LE", '"' . $password . '"');
    }
}
