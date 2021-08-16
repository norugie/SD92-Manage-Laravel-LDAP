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
        $employees = Group::findBy('cn', 'tempstudent')->members()->get();

        foreach($employees as $employee): 
            DB::connection('mysql2')
            ->table('lglist')
            ->where('userid', $employee->getFirstAttribute('samaccountname'))
            ->delete();

            DB::connection('mysql2')
            ->table('lglist')
            ->updateOrInsert(
                [
                    'userid' => $employee->getFirstAttribute('samaccountname')
                ],
                [
                    'userid' => $employee->getFirstAttribute('samaccountname'),
                    'school' => 'Withdrawn',
                    'localgroup' => 'nondistrict'
                ]
            );

            DB::connection('mysql2')
            ->table('users')
            ->where('userid', $employee->getFirstAttribute('samaccountname'))
            ->update(
                [
                    'comment' => 'Withdrawn'
                ]
            );
            echo $employee->getFirstAttribute('samaccountname') . " " . "<br>";
        endforeach;

        DB::connection('mysql2')
        ->table('info')
        ->where('Teacher', 'like', '%locker%')
        ->update(
            [
                'School' => 'NESS',
                'Student' => NULL,
                'user_uid' => NULL
            ]
        );
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

        // dd($employee);
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
