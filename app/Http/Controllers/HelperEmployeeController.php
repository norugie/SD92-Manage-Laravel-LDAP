<?php

namespace App\Http\Controllers;

/**
 * 
 * This controller file is used across other controllers. The HelperEmployeeController class
 * contains methods used to handle most of the processes needed to create, update,
 * enable, or disable employee AD accounts.
 * 
 */

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Ldap\User;
use App\Ldap\Group;
use LdapRecord\Models\Attributes\Password;

class HelperEmployeeController extends Controller
{
    /**
     * Handle process for setting roles to employee accounts
     *
     * @param String $username
     * @param \Illuminate\Http\Request $request
     * @return Array $roles
     */
    public function setEmployeeRoles (String $username, Request $request)
    {
        // Set variable info
        $roles = []; 
        $reverse_fullname = $request->employee_lastname . " " . $request->employee_firstname;
        $department = $request->employee_department;
        $description = $department . " employee";
        $locations = $request->employee_locations;
        $employee_roles = $request->employee_roles;

        // Set employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');
        $employee->department = $department;
        $employee->description = $description;

        // Set $uidNumber as $uid to be used in the process
        $entry_check = DB::connection('mysql2')
                        ->table('users')
                        ->where('userid', $username)
                        ->pluck('uid')
                        ->first();
        if($entry_check){
            $uid = $entry_check;
        } else {
            $uid = DB::connection('mysql2')
                ->table('users')
                ->orderBy('uid', 'desc')
                ->first();
            $uid = $uid->uid + 1;
        }
        
        // Set employee uID
        $employee->uidnumber = $uid;

        // Save object values for employee
        $employee->save();
        $employee->refresh();

        // Separate sub-departments from roles in $roles array
        if($employee_roles !== NULL){
            $roles = $this->separateSubDepartmentsFromRoles($employee_roles, $roles);
        }

        // Push default roles to $roles array
        array_push($roles, $department, 'employee', 'activestaff', $this->licensingSorter($employee_roles));

        // Update K12 account information here
        $this->setEmployeeInK12Admin($username, $reverse_fullname, $description, $uid, $request->employee_rfid);
        
        foreach($roles as $role): 
            $this->setEmployeeLocalGroupInK12Admin($username, $department, $role);
        endforeach;

        // Remove department localgroup
        $this->removeEmployeeLocalGroupInK12Admin($username, $department);

        // Merge $roles and $location into one array
        if($locations !== NULL) {
            $this->disableEmployeeAllIDAccessInK12Admin($uid);
            if($request->employee_rfid !== NULL){
                // Set location access
                foreach($locations as $location): 
                    $this->setEmployeeIDAccessInK12Admin($uid, $location);
                endforeach;
            } else {
                // Disable ID
                $this->disableEmployeeIDInK12Admin($uid);
            }

            $roles = array_merge($roles, $locations);
        }

        return $roles;
    }

    /**
     * Generates a human-readable randomly generated password
     *
     * @return String $password
     */
    public function stringGenerator ()
    {  
        $words = file('cms/words.txt', FILE_IGNORE_NEW_LINES);
        $password = $words[array_rand($words)] . rand(0, 9) . $words[array_rand($words)] . rand(0, 9);

        return $password;
    }

    /**
     * Generates password converted to format accepted by Active Directory
     *
     * @return String $password
     */
    public function passwordConverter (String $password)
    {
        return iconv("UTF-8", "UTF-16LE", '"' . $password . '"');
    }

    /**
     * Separate sub-departments from roles
     *
     * @param Array $employee_roles
     * @param Array $roles
     * @return Array $roles
     */
    public function separateSubDepartmentsFromRoles (Array $employee_roles, Array $roles)
    {
        // Remove "dept-" tag from sub-departments
        foreach($employee_roles as $role):
            if(strpos($role, 'dept-') === FALSE) array_push($roles, $role);
            else {
                $role = substr_replace($role, '', 0, 5);
                array_push($roles, $role);
            }
        endforeach;
        return $roles;
    }

    /**
     * Handle sorting to which Office365 license group an account gets assigned to, based on set account groups
     *
     * @param mixed $groups
     * @return String $license
     */
    public function licensingSorter($groups)
    {
        // Automatically assign account to A1 license if $groups is NULL
        if($groups === NULL) $license = "A1 Staff Assignment";
        else {
            // Loop through groups in a3 file set to have the A3 license
            foreach(file('cms/groups-with-a3-license.txt', FILE_IGNORE_NEW_LINES) as $a3):
                // Check if current group is in $groups
                if(in_array($a3, $groups) ? $license = "A3 Staff Assignment" : $license = "A1 Staff Assignment");
                
                // Stop loop if current group is in $groups
                if($license === "A3 Staff Assignment") break;
            endforeach;
            
            // If employee is in A3 Staff Exceptions group, set license to A1
            if(in_array('A3 Staff Exceptions', $groups)) $license = "A1 Staff Assignment";
        }

        return $license;
    }

    // --- K12Admin-related processes here --- //

    /**
     * Handle process for setting account info in K12Admin
     *
     * @param String $username
     * @param String $fullname
     * @param String $description
     * @param Int $uid
     * @param mixed $rfid
     */
    public function setEmployeeInK12Admin (String $username, String $fullname, String $description, Int $uid, $rfid)
    {
        // Set employee information in K12Admin
        $data_id = '-' . $uid;
        DB::connection('mysql2')
        ->table('users')
        ->updateOrInsert(
            ['userid' => $username],
            [
                'userid' => $username,
                'fullname' => $fullname,
                'comment' => $description,
                'uid' => $uid,
                'data_id' => $data_id
            ]
        );

        // Set RFID card if $rfid is not NULL
        if($rfid !== NULL){
            DB::connection('mysql2')
            ->table('rfid')
            ->updateOrInsert(
                ['data_id' => $data_id],
                [
                    'data_id' => $data_id,
                    'keypad_id' => $rfid,
                    'rfid_active' => 1
                ]
            );
        }
    }

    /**
     * Handle process for setting employee initial password in K12Admin
     *
     * @param String $username
     * @param String $description
     */
    public function setEmployeePasswordInK12Admin (String $username, String $password)
    {
        DB::connection('mysql2')
        ->table('users')
        ->where('userid', $username)
        ->update(
            [
                'pt' => $password
            ]
        );
    }

    /**
     * Handle process for setting employee department description in K12Admin
     *
     * @param String $username
     * @param String $description
     */
    public function setEmployeeCommentInK12Admin (String $username, String $description)
    {
        DB::connection('mysql2')
        ->table('users')
        ->where('userid', $username)
        ->update(
            [
                'comment' => $description
            ]
        );
    }

    /**
     * Handle process for adding local groups to account in K12Admin
     *
     * @param String $username
     * @param mixed $department
     * @param String $localgroup
     */
    public function setEmployeeLocalGroupInK12Admin (String $username, $department, String $localgroup)
    {
        // Set employee localgroups in lglist based on the current localgroup in the loop from the employee's set of AD groups
        if($department === '') $department = 'oldstaff';
        DB::connection('mysql2')
        ->table('lglist')
        ->updateOrInsert(
            [
                'userid' => $username,
                'localgroup' => $localgroup
            ],
            [
                'userid' => $username,
                'school' => $department,
                'localgroup' => $localgroup
            ]
        );
    }

    /**
     * Handle process for removing local groups to account in K12Admin
     *
     * @param String $username
     * @param String $localgroup
     */
    public function removeEmployeeLocalGroupInK12Admin (String $username, String $localgroup)
    {
        DB::connection('mysql2')
        ->table('lglist')
        ->where('userid', $username)
        ->where('localgroup', $localgroup)
        ->delete();
    }

    /**
     * Handle process for disabling ID in K12Admin
     *
     * @param Int $uid
     */
    public function disableEmployeeIDInK12Admin (Int $uid)
    {

        $rfid = DB::connection('mysql2')
        ->table('rfid')
        ->where('data_id', '=', '-' . $uid)->first();

        if($rfid !== NULL){
            DB::connection('mysql2')
            ->table('rfid')
            ->where('data_id', '-' . $uid)
            ->update(
                [
                    'rfid_active' => 0
                ]
            );
        }
    }

    /**
     * Handle process for disabling all ID access in K12Admin
     *
     * @param Int $uid
     */
    public function disableEmployeeAllIDAccessInK12Admin (Int $uid)
    {
        DB::connection('mysql2')
        ->table('access_control_whitelist')
        ->where('uid', $uid)
        ->delete();

        DB::connection('mysql2')
        ->table('access_control_user')
        ->where('uid', $uid)
        ->delete();
    }

    /**
     * Handle process for setting account ID access permissions in K12Admin
     *
     * @param Int $uid
     * @param String $location
     */
    public function setEmployeeIDAccessInK12Admin (Int $uid, String $location)
    {
        switch($location): 
            case "SDO": 
                $loc_id = 5;
                break;
            case "TechOffice": 
                $loc_id = 1;
                break;
            case "Maintenance": 
                $loc_id = 2;
                break;
            case "AAMES": 
                $loc_id = 8;
                break;
            case "GES": 
                $loc_id = 7;
                break;
            case "NESS": 
                $loc_id = 6;
                break;
            case "NBES": 
                $loc_id = 9;
                break;
        endswitch;

        // Insert record in access control whitelist for giving access to the employee for a location
        DB::connection('mysql2')
        ->table('access_control_whitelist')
        ->insert(
            [
                'system_id' => $loc_id,
                'uid' => $uid,
            ]
        );

    }

    // --- END: K12Admin-related processes here --- //
}
