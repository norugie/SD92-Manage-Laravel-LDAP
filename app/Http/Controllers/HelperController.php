<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Ldap\User;
use App\Ldap\Group;
use LdapRecord\Models\Attributes\AccountControl;
use LdapRecord\Models\Attributes\Password;

class HelperController extends Controller
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
        // Set up variable info
        $roles = []; 
        $reverse_fullname = $request->employee_lastname . " " . $request->employee_firstname;
        $department = $request->employee_department;
        $description = $department . " employee";
        $locations = $request->employee_locations;
        $employee_roles = $request->employee_roles;

        // Set up employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');
        $employee->department = $department;
        $employee->description = $description;

        if($employee->uid === NULL) {
            $uid = DB::connection('mysql2')
                    ->table('users')
                    ->orderBy('uid', 'desc')
                    ->first();
            $uid = $uid->uid + 1;
        } else {
            $uid = DB::connection('mysql2')
                    ->table('users')
                    ->select('uid')
                    ->where('userid', $username)
                    ->first();
            $uid = $uid->uid;
        }
        
        $employee->uid = $uid;

        // Save set object values for employee
        $employee->save();
        $employee->refresh();

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
        if($locations !== NULL) $roles = array_merge($roles, $locations);

        return $roles;
    }

    /**
     * Generates random password - currently not implemented
     *
     * @param String $password
     * @return String $password
     */
    public function stringGenerator ()
    {  
        $length = 8;
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
        
        return substr(str_shuffle(str_repeat($chars, $length)), 0, $length);
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
     * Handle sorting to which Office365 license group an account gets assigned to, based on set account groups
     *
     * @param mixed $groups
     * @return String $license
     */
    public function licensingSorter($groups)
    {
        $license;

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
     * Handle process for adding local groups to account in K12Admin
     *
     * @param String $username
     * @param mixed $department
     * @param String $localgroup
     */
    public function setEmployeeLocalGroupInK12Admin (String $username, $department, String $localgroup)
    {
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
     * Handle process for disabling ID access in K12Admin
     *
     * @param String $uid
     */
    public function disableEmployeeIDAccessInK12Admin (Int $uid)
    {
        DB::connection('mysql2')
        ->table('rfid')
        ->updateOrInsert(
            ['data_id' => '-' . $uid],
            [
                'rfid_active' => 0
            ]
        );
    }

    /**
     * Handle process for setting account ID access permissions in K12Admin
     *
     * @param Int $uid
     * @param Array $locations
     */
    public function setEmployeeIDAccessInK12Admin (Int $uid, Int $location)
    {
        // 
    }

    // public function setUID ()
    // {
    //     $employees = Group::findBy('cn', 'activestaff')->members()->get();
    //     echo "<b>Changed Info: </b> <i>Employees with no data ID are assumed to have no ID assigned</i><br><br>";
    //     foreach($employees as $employee): 
    //         $e = User::find('cn=' . $employee->getFirstAttribute('samaccountname') . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');
    //         $uid = DB::connection('mysql2')
    //                 ->table('users')
    //                 ->select('uid', 'idnumber', 'data_id')
    //                 ->where('userid', $employee->getFirstAttribute('samaccountname'))
    //                 ->first();

    //         if($uid->data_id !== NULL){
    //             DB::connection('mysql2')
    //             ->table('rfid')
    //             ->updateOrInsert(
    //                 ['data_id' => $uid->data_id],
    //                 [
    //                     'data_id' => '-'.$uid->uid
    //                 ]
    //             );

    //             DB::connection('mysql2')
    //             ->table('users')
    //             ->updateOrInsert(
    //                 ['userid' => $employee->getFirstAttribute('samaccountname')],
    //                 [
    //                     'data_id' => '-'.$uid->uid
    //                 ]
    //             );

    //             $rfid = DB::connection('mysql2')
    //             ->table('rfid')
    //             ->select('keypad_id')
    //             ->where('data_id', '-'.$uid->uid)
    //             ->first();

    //             $e->employeeNumber = $rfid->keypad_id;
    //         }

    //         DB::connection('mysql2')
    //         ->table('users')
    //         ->updateOrInsert(
    //             ['userid' => $employee->getFirstAttribute('samaccountname')],
    //             [
    //                 'idnumber' => $uid->uid
    //             ]
    //         );

    //         $uid = DB::connection('mysql2')
    //         ->table('users')
    //         ->select('uid', 'idnumber', 'data_id')
    //         ->where('userid', $employee->getFirstAttribute('samaccountname'))
    //         ->first();

    //         $e->uid = $uid->uid;
    //         $e->uidNumber = $uid->data_id;
    //         $e->save();

    //         echo "<b>Username: </b> " . $employee->getFirstAttribute('samaccountname') . "<br><b>Employee Name: </b>" . $employee->getFirstAttribute('displayname') . "<br><b>Changed UID: </b>" . $uid->uid . "<br><b>Changed ID Number: </b>" . $uid->idnumber . "<br><b>Changed Data ID: </b>" . $uid->data_id . "<br><b>Employee ID RFID Code: </b>" . $employee->getFirstAttribute('employeeNumber') . "<br><br>";
    //     endforeach;
    // }

    // public function setEmployeeID ()
    // {
    //     // $text = [];
    //     // foreach(file('cms/employees.txt', FILE_IGNORE_NEW_LINES) as $e): 
    //     //     array_push($text, $e);
    //     // endforeach;
    //     // print_r($text);

    //     // echo "<br><br>In List:<br>";
    //     // $employees = Group::findBy('cn', 'activestaff')->members()->get();
    //     // foreach($employees as $employee):
    //     //     $reverse_name = $employee->getFirstAttribute('sn') . " " . $employee->getFirstAttribute('givenname');
    //     //     // echo $reverse_name . "<br>";
    //     //     if(!in_array($reverse_name, $text)) echo $employee->getFirstAttribute('displayname') . "<br>";
    //     // endforeach;

    //     // $json = file_get_contents('cms/employees.json');
    //     // $config = json_decode($json, true);
    //     // foreach($config as $key => $value): 
    //     //     $e = User::find('cn=' . $key . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');
    //     //     $e->employeeID = $value;
    //     //     $e->save();
    //     //     echo $key . " " . $value . "<br>";
    //     // endforeach;


    //     $employees = Group::findBy('cn', 'activestaff')->members()->get();
    //     foreach($employees as $employee):
    //         $employee = User::find('cn=' . $employee->getFirstAttribute('samaccountname') .  ',cn=Users,dc=nisgaa,dc=bc,dc=ca');
    //         $employee->mailnickname = $employee->getFirstAttribute('samaccountname');
    //         $employee->save();
    //     endforeach;

    //     $employees = Group::findBy('cn', 'oldstaff')->members()->get();
    //     foreach($employees as $employee):
    //         $employee = User::find('cn=' . $employee->getFirstAttribute('samaccountname') .  ',cn=Users,dc=nisgaa,dc=bc,dc=ca');
    //         $employee->mailnickname = $employee->getFirstAttribute('samaccountname');
    //         $employee->save();
    //     endforeach;

    // }

    // --- END: K12Admin-related processes here --- //
}
