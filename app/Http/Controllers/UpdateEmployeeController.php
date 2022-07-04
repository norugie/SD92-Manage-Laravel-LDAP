<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperEmployeeController;
use Illuminate\Http\Request;
use App\Ldap\User;
use App\Ldap\Group;

class UpdateEmployeeController extends Controller
{
    public function __construct ()
    {
        $this->helpers = new HelperEmployeeController;
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

        // Set employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        $employee->displayname = $fullname;
        $employee->givenname = $firstname;
        $employee->sn = $lastname;
        if($employee_id !== NULL) $employee->employeeID = $employee_id;
        $employee->employeeNumber = $employee_rfid;

        // Save set object values for employee
        $employee->save();
        $employee->refresh();

        // Set employee account roles
        $this->updateEmployeeRoles($username, $request);

        // Log activity
        $message = 'The account for <b><a href="/cms/employees/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been updated successfully.';
        $this->inputLog(session('userName'), $message);

        $this->alertDetails($message, 'success');
        
        return redirect('/cms/employees/' . $username . '/view');
    }

    /**
     * Handle process for updating employee profile ID image
     *
     * @param String $username
     * @param String $userID
     * @param \Illuminate\Http\Request $request
     */
    public function updateEmployeeProfileIDImage (String $username, Int $userID, Request $request)
    {
        // Base path for images
        $url = '/cms/images/users/';

        // Decode base64 data for image
        $data = $request->image;
        $image_parts = explode(";base64", $data);
        $data = base64_decode($image_parts[1]);
        
        // Set path for new image
        $filename = $userID . '_' . $username . '.png';
        $path = $url . $filename;

        // Upload image to designated image folder
        file_put_contents(public_path($path), $data);

        // Set employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');
        $fullname = $employee->getFirstAttribute('displayname');
        
        // Log activity
        $message = 'The profile ID card for <b><a href="/cms/employees/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been updated successfully.';
        $this->inputLog(session('userName'), $message);

        $this->alertDetails($message, 'create_success');
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
            ]
        );
            
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

        $this->alertDetails($message, 'success');

        return redirect('/cms/employees');
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
        // Set variable info
        $roles = $this->helpers->setEmployeeRoles($username, $request);
        $current_groups = [];

        // Set employee object values
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
