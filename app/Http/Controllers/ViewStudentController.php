<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperStudentController;
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

        $this->helpers = new HelperStudentController;
    }

    /**
     * Return data for /students page
     * 
     * @return \Illuminate\View\View
     */
    public function enabledStudentAccountsIndex ()
    {

        $students = Group::findBy('cn', 'student')->members()->get();

        foreach($students as $student):
            $info = $this->getStudentInfo($student->getFirstAttribute('samaccountname'));
            $student->setAttribute('fullname', $info['fullname']);
            $student->setAttribute('school', $info['school']);
            $student->setAttribute('initialpassword', $info['pt']);
        endforeach;

        return view('cms.student.student', [
            'students' => $students
        ]);
    }

    /**
     * Return student name with better formatting
     * 
     * @param String $username
     * @return Array $info
     */
    public function getStudentInfo ($username)
    {
        $k12student = $this->helpers->getStudentInfoFromK12Admin($username);
        $fullname = explode(',', $k12student->fullname);
        $fullname = $fullname[1] . " " . $fullname[0];
        $school = explode(' ', $k12student->comment);
        $school = $school[0];

        $info = ['fullname' => $fullname, 'school' => $school, 'pt' => $k12student->pt];

        return $info;
    }
}
