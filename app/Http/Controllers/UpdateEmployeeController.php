<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Ldap\User;
use App\Ldap\Group;

class UpdateEmployeeController extends Controller
{
    public function __construct ()
    {
        $this->helpers = new HelperController;
    }

    /**
     * Handle process for updating employee account
     *
     * @param String $username
     * @param  \Illuminate\Http\Request  $request
     */
    public function updateEmployeeProfile (String $username, Request $request)
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

        // Set up variable info
        $firstname = $request->employee_firstname;
        $lastname = $request->employee_lastname;
        $fullname = $firstname . ' ' . $lastname;
        $company = 'SD92';
        $department = $request->employee_department;
        $locations = $request->employee_locations;
        $employee_id = $request->employee_id;
        $employee_rfid = $request->employee_rfid;
        $employee_roles = $request->employee_roles;

        // Set up employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        $employee->displayname = $fullname;
        $employee->givenname = $firstname;
        $employee->sn = $lastname;
        if($employee_id !== NULL) $employee->employeeID = $employee_id;
        $employee->employeeNumber = $employee_rfid;

        // Save set object values for employee
        $employee->save();
        $employee->refresh();

        // Set up employee account roles
        $this->updateEmployeeRoles($username, $request);

        // Log activity
        $message = 'The account for <b><a href="/cms/employees/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been updated successfully.';
        $this->inputLog(session('userName'), $message);
        
        return redirect('/cms/employees/' . $username . '/view')
            ->with('status', 'success')
            ->with('message', $message);
    }

    /**
     * Handle process for updating multiple employee accounts
     *
     * @param \Illuminate\Http\Request $request
     */
    public function updateEmployeeRolesMultiple (Request $request)
    {
        // Backend validation for POST $request
        $request->validate( 
            [
                'employee_department' => 'required'
            ],
            [
                'employee_department.required' => 'Select employee department/school'
            ]);
            
        // Set up string of usernames into an array
        $employees = explode(',', rtrim($request->employee_multiple, ','));

        // Loop through username array
        foreach($employees as $username): 
            // Process employee roles, return employee $fullname
            $fullname = $this->updateEmployeeRoles($username, $request);
            
            // Log activity per loop
            $message = 'The account for <b><a href="/cms/employees/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been updated successfully.';
            $this->inputLog(session('userName'), $message);
        endforeach;
        
        // Log activity for the entire process
        $message = 'The department/role(s) for multiple district accounts has been updated successfully.';
        $this->inputLog(session('userName'), $message);

        return redirect('/cms/employees')
            ->with('status', 'success')
            ->with('message', $message);
    }

    /**
     * Handle process for moving updated employee accounts to appropriate groups
     *
     * @param String $username
     * @param \Illuminate\Http\Request $request
     * @return String $fullname
     */
    public function updateEmployeeRoles (String $username, Request $request)
    {
        // Set up variable info
        $roles = $this->helpers->setEmployeeRoles($username, $request);
        $current_groups = [];

        // Set up employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        // Fetch employee groups data
        $employee_groups = $employee->groups()->get();

        foreach($employee_groups as $eg):
            array_push($current_groups, $eg->getName());
            // Remove from user's employee groups if not a part of updated locations, roles, and sub-departments
            if($roles !== NULL && !in_array($eg->getName(), $roles)) {
                // Remove localgroup from K12 account
                $this->helpers->removeEmployeeLocalGroupInK12Admin($username, $eg->getName());
                
                $employee_group = Group::findBy('cn', $eg->getName());
                $employee->groups()->detach($employee_group);
            }
        endforeach;

        if($roles !== NULL){
            // Add role groups if not already a part of the user's group
            foreach($roles as $role): 
                if(!in_array($role, $current_groups)) {
                    $employee_group = Group::findBy('cn', $role);
                    $employee->groups()->attach($employee_group);
                }
            endforeach;
        }

        return $employee->getFirstAttribute('displayname');
    }

}