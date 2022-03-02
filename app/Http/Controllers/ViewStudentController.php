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
    public function enabledStudentAccountsIndex ($prefix)
    {

        echo $prefix;

        // $students = Group::findBy('cn', 'student')->members()->get();
        // // dd($students);

        // foreach($students as $student):
        //     $k12student = $this->helpers->getStudentInfoFromK12Admin($student->getFirstAttribute('samaccountname'));
        //     $fullname = explode(',', $k12student->fullname);
        //     $fullname = $fullname[1] . " " . $fullname[0];
        //     $school = explode(' ', $k12student->comment);
        //     $school = $school[0];
        //     $student->setAttribute('fullname', $fullname);
        //     $student->setAttribute('school', $school);
        // endforeach;

        // return view('cms.student.student', [
        //     'students' => $students
        // ]);
    }
}
