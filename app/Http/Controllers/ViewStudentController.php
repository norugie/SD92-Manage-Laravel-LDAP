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
            $student_info = $this->getStudentInfo($student);
            $student->setAttribute('fullname', $student_info['fullname']);
            $student->setAttribute('school', $student_info['school']);
        endforeach;

        return view('cms.student.student', [
            'students' => $students
        ]);
    }

    /**
     * Return student info taken from K12Admin with better formatting
     * 
     * @param Object $student
     * @return Array $student_info
     */
    public function getStudentInfo (Object $student)
    {
        $student_info = [];

        $k12student = $this->helpers->getStudentInfoFromK12Admin($student->getFirstAttribute('samaccountname'));
        $fullname = explode(',', $k12student->fullname);
        $fullname = $fullname[1] . " " . $fullname[0];
        $school = explode(' ', $k12student->comment);
        $school = $school[0];

        // Base path for profile images
        $url = '/cms/images/users/';

        // Check image directory if profile image for user exists
        $image_directory = glob(public_path($url) . $k12student->uid ."*.png");
        if($image_directory ? $student_pic = $url . pathinfo($image_directory[0], PATHINFO_BASENAME) : $student_pic = $url . "user-placeholder.png");

        $student_info = [
            'fullname' => $fullname,
            'sysid' => $k12student->uid, 
            'school' => $school, 
            'pt' => $k12student->pt, 
            'student_pic' => $student_pic
        ];
        
        return $student_info;
    }

    /**
     * Return data for /students/{username}/view page
     *
     * @param String $username
     * @return \Illuminate\View\View
     */
    public function viewStudentProfile (String $username)
    {
        // Fetch student data
        $student = User::find('cn=' . $username . ',ou="Domain Users",dc=nisgaa,dc=bc,dc=ca');

        // Redirect to /students page if {username} is NULL
        if($student === NULL) 
            return redirect('/cms/students')
                ->with('status', 'danger')
                ->with('message', 'The user you are looking for does not exist in our directory.');
        else {
            // Redirect to /students page if {username} has a disabled account
            $uac = new AccountControl($student->getFirstAttribute('userAccountControl'));
            if($uac->has(AccountControl::ACCOUNTDISABLE))
                return redirect('/cms/students')
                    ->with('status', 'danger')
                    ->with('message', 'The user you are looking for no longer has an active account in our directory');
        }
        
        $student_info = $this->getStudentInfo($student);

        $student->setAttribute('fullname', $student_info['fullname']);
        $student->setAttribute('sysid', $student_info['sysid']);
        $student->setAttribute('school', $student_info['school']);
        $student->setAttribute('initialpassword', $student_info['pt']);
        $student->setAttribute('studentpic', $student_info['student_pic']);

        return view('cms.student.profile', [
            'student' => $student,
            'config' => $this->config
        ]);
    }
}
