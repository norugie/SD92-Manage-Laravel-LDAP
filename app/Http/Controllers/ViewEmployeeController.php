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
        $students = Group::findBy('cn', 'student')->members()->get();
        $nessK=0; $ness01=0; $ness02=0; $ness03=0; $ness04=0; $ness05=0; $ness06=0; $ness07=0; $ness08=0; $ness09=0; $ness10=0; $ness11=0; $ness12=0;
        $aamesK=0; $aames01=0; $aames02=0; $aames03=0; $aames04=0; $aames05=0; $aames06=0; $aames07=0;
        $nbesK=0; $nbes01=0; $nbes02=0; $nbes03=0; $nbes04=0; $nbes05=0; $nbes06=0; $nbes07=0;
        $gesK=0; $ges01=0; $ges02=0; $ges03=0; $ges04=0; $ges05=0; $ges06=0; $ges07=0;


        foreach($students as $student): 
            $groups = $student->groups()->get();
            foreach($groups as $group):
                if(strpos($group->getName(), 'ness') !== FALSE || strpos($group->getName(), 'aames') !== FALSE || strpos($group->getName(), 'ges') !== FALSE || strpos($group->getName(), 'nbes') !== FALSE) 
                    ${$group->getName()}++;
            endforeach;
        endforeach;

        echo "Total Students: " . count($students) . "<br><br>";
        echo "<b>NESS</b><br>NESS Kindergarten Students: " . $nessK . 
            "<br>Grade 1 Students: " . $ness01 .
            "<br>Grade 2 Students: " . $ness02 . 
            "<br>Grade 3 Students: " . $ness03 . 
            "<br>Grade 4 Students: " . $ness04 . 
            "<br>Grade 5 Students: " . $ness05 . 
            "<br>Grade 6 Students: " . $ness06 . 
            "<br>Grade 7 Students: " . $ness07 . 
            "<br>Total NESS Students: " . $nessK+$ness01+$ness02+$ness03+$ness04+$ness05+$ness06+$ness07 . "<br><br>";

        echo "<b>AAMES</b><br>AAMES Kindergarten Students: " . $aamesK . 
            "<br>Grade 1 Students: " . $aames01 .
            "<br>Grade 2 Students: " . $aames02 . 
            "<br>Grade 3 Students: " . $aames03 . 
            "<br>Grade 4 Students: " . $aames04 . 
            "<br>Grade 5 Students: " . $aames05 . 
            "<br>Grade 6 Students: " . $aames06 . 
            "<br>Grade 7 Students: " . $aames07 . 
            "<br>Total AAMES Students: " . $aamesK+$aames01+$aames02+$aames03+$aames04+$aames05+$aames06+$aames07 . "<br><br>";

        echo "<b>GES</b><br>GES Kindergarten Students: " . $gesK . 
            "<br>Grade 1 Students: " . $ges01 .
            "<br>Grade 2 Students: " . $ges02 . 
            "<br>Grade 3 Students: " . $ges03 . 
            "<br>Grade 4 Students: " . $ges04 . 
            "<br>Grade 5 Students: " . $ges05 . 
            "<br>Grade 6 Students: " . $ges06 . 
            "<br>Grade 7 Students: " . $ges07 . 
            "<br>Total GES Students: " . $gesK+$ges01+$ges02+$ges03+$ges04+$ges05+$ges06+$ges07 . "<br><br>";

        echo "<b>NBES</b><br>NBES Kindergarten Students: " . $nbesK . 
            "<br>Grade 1 Students: " . $nbes01 .
            "<br>Grade 2 Students: " . $nbes02 . 
            "<br>Grade 3 Students: " . $nbes03 . 
            "<br>Grade 4 Students: " . $nbes04 . 
            "<br>Grade 5 Students: " . $nbes05 . 
            "<br>Grade 6 Students: " . $nbes06 . 
            "<br>Grade 7 Students: " . $nbes07 . 
            "<br>Total NBES Students: " . $nbesK+$nbes01+$nbes02+$nbes03+$nbes04+$nbes05+$nbes06+$nbes07 . "<br><br>";
        // foreach($employees as $employee): 
        //     DB::connection('mysql2')
        //     ->table('lglist')
        //     ->where('userid', $employee->getFirstAttribute('samaccountname'))
        //     ->delete();

        //     DB::connection('mysql2')
        //     ->table('lglist')
        //     ->updateOrInsert(
        //         [
        //             'userid' => $employee->getFirstAttribute('samaccountname')
        //         ],
        //         [
        //             'userid' => $employee->getFirstAttribute('samaccountname'),
        //             'school' => 'Withdrawn',
        //             'localgroup' => 'nondistrict'
        //         ]
        //     );

        //     DB::connection('mysql2')
        //     ->table('users')
        //     ->where('userid', $employee->getFirstAttribute('samaccountname'))
        //     ->update(
        //         [
        //             'comment' => 'Withdrawn'
        //         ]
        //     );
        //     echo $employee->getFirstAttribute('samaccountname') . " " . "<br>";
        // endforeach;

        // DB::connection('mysql2')
        // ->table('info')
        // ->where('Teacher', 'like', '%locker%')
        // ->update(
        //     [
        //         'School' => 'NESS',
        //         'Student' => NULL,
        //         'user_uid' => NULL
        //     ]
        // );
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
