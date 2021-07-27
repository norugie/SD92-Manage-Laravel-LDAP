<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB; // Remove after testing
use App\Ldap\User;
use App\Ldap\Group;

class ViewEmployeeController extends Controller
{
    public function __construct ()
    {
        // Fetch config setup for locations, roles, and sub-departments
        $json = file_get_contents('cms/config.json');
        $this->config = json_decode($json, true);
    }

    public function test ()
    {
        $students = Group::findBy('cn', 'tempstudent')->members()->get();
        
        foreach ($students as $student): 
            // if($student->getFirstAttribute('displayname') === NULL)
            //     echo $student->getFirstAttribute('displayname') . " - " . $student->getFirstAttribute('samaccountname') . " - " . $student->getFirstAttribute('mail') . "<br>";
            // else {
            //     // Do stuff to move students here

            //     // Add student to oldstaff group
            //     $student_group = Group::findBy('cn', 'A1 Student Assignment');
            //     $student->groups()->attach($student_group);
            // }

            // Add student to oldstaff group
            // $student_group = Group::findBy('cn', 'A1 Student Assignment');
            // $student->groups()->attach($student_group);

            echo $student->getFirstAttribute('displayname') . " - " . $student->getFirstAttribute('samaccountname') . " - " . $student->getFirstAttribute('mail') . "<br>";
        endforeach;
    }

    /**
     * Return data for /employees page
     * 
     * @return \Illuminate\View\View
     */
    public function enabledEmployeeAccountsIndex ()
    {
        $employees = Group::findBy('cn', 'activestaff')->members()->get();
        return view('cms.employee.employee', [
            'employees' => $employees,
            'config' => $this->config
        ]);
    }

    /**
     * Return data for /employees/create page
     * 
     * @return \Illuminate\View\View
     */
    public function createEmployeeForm ()
    {
        return view('cms.employee.create.employee', [
            'config' => $this->config
        ]);
    }

    /**
     * Return data for /employees/{username}/{action} page
     *
     * @param String $username
     * @param String $action
     * @return \Illuminate\View\View
     */
    public function viewEmployeeProfileUpdate (String $username, String $action)
    {
        // Fetch employee data
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        // Redirect to /employees page if {username} is NULL
        if($employee === NULL) 
            return redirect('/cms/employees')
                ->with('status', 'danger')
                ->with('message', 'The user you are looking for does not exist in our directory.');

        // Fetch employee groups data
        $groups = $employee->groups()->get();
        $locations = [];
        $sub_departments = [];
        $check = [];

        // Set up $check to compare against config setup
        foreach($this->config['locations'] as $key => $value): 
            array_push($check, $key);
        endforeach;

        // Separate set grous to $locations and $sub_departments, based off of $check value compared against config setup
        foreach($groups as $group):
            $group = $group->getName();
            if(in_array($group, $check) ? array_push($locations, $group) : array_push($sub_departments, $group));
        endforeach;

        // Set up path based on {action}
        // Default {action} value = "view"
        // Redirect paths {action} value = "view" - /employees/{username}/view, {action} value = "update" - /employees/{username}/update
        if(isset($action) && !empty($action) && $action == 'update' ? $path = 'update.employee' : $path = 'profile');

        return view('cms.employee.' . $path, [
            'employee' => $employee,
            'config' => $this->config,
            'locations' => $locations,
            'sub_departments' => $sub_departments
        ]);
    }

    /**
     * Return data for /inactive page
     */
    public function disabledEmployeeAccountsIndex ()
    {
        $employees = Group::findBy('cn', 'inactivestaff')->members()->get();
        return view ('cms.inactive.inactive', [
            'employees' => $employees,
            'config' => $this->config
        ]);
    }
}
