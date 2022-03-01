<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Ldap\User;
use App\Ldap\Group;
use LdapRecord\Models\Attributes\AccountControl;

class ViewStudentController extends Controller
{
    public function __construct ()
    {
        // Fetch config setup for locations, roles, and sub-departments
        $json = file_get_contents('cms/config.json');
        $this->config = json_decode($json, true);
    }

    /**
     * Return data for /students page
     * 
     * @return \Illuminate\View\View
     */
    public function enabledStudentAccountsIndex ()
    {
        $students = Group::findBy('cn', 'student')->members()->get();
        // echo count($students);

        for($i=0;$i<=count($students);$i++){
            
        }
        // return view('cms.student.student', [
        //     'students' => $students
        // ]);
    }
}
