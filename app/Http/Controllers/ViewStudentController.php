<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperStudentController;
use App\Ldap\User;
use App\Ldap\Group;
use App\Models\Locker;
use Illuminate\Support\Facades\DB;
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

    public function test ()
    {
        $k12students = DB::connection('mysql2')
        ->table('lglist')
        ->leftJoin('users', 'users.userid', '=', 'lglist.userid')
        ->select('users.fullname', 'users.userid', 'users.uid', 'users.pt', 'lglist.school')
        ->where('lglist.localgroup', 'student')
        ->where('users.comment', 'like', '%student%')
        ->orderBy('users.userid', 'ASC')
        ->get();

        echo "Total count:" . count($k12students) . "<br><br>";

        // dd($collection);
        foreach($k12students as $student):
            // $collection = collect($student);
            // if($collection->contains('1078947')) echo "Jesse here<br><br>";
            $adstudent = User::find('cn=' . $student->userid . ',ou="Domain Users",dc=nisgaa,dc=bc,dc=ca');
            if($adstudent === NULL){
                echo "<p style='color:red;'>" . $student->userid . " - " . $student->fullname . " - " . $student->school . "</p><br>";
            } else {
                echo $student->userid . " - " . $student->fullname . " - " . $student->school . "<br>";
            }
        endforeach;

        // $students = Group::findBy('cn', 'student')->members()->get();

        // foreach($students as $student):

        // endforeach;
    }

    /**
     * Return data for /students page
     * 
     * @return \Illuminate\View\View
     */
    public function enabledStudentAccountsIndex ()
    {
        $students = $this->helpers->getStudentIndexFromK12Admin();

        return view('cms.student.student', [
            'students' => $students
        ]);
    }

    /**
     * Return data for /lockers page
     * 
     * @return \Illuminate\View\View
     */
    public function lockerStatusDisplay ()
    {
        $carts = $this->helpers->getLockerCartIndexFromK12Admin();

        foreach ($carts as $cart):
            $lockers = $this->helpers->getLockerInfoFromK12Admin($cart);
            $cart->lockers = $lockers;
        endforeach;

        return view('cms.locker.locker', [
            'carts' => $carts
        ]);
    }

    /**
     * Return data for /lockers/logs page
     *
     * @param Int $id 
     * @return \Illuminate\View\View
     */
    public function lockerLogsDisplayIndex ()
    {
        echo "All logs";
        // return view('cms.locker.log');
    }

    /**
     * Return data for /lockers/logs/{id} page
     *
     * @param Int $id 
     * @return \Illuminate\View\View
     */
    public function lockerLogsDisplayIndexIdSpecified (Int $id)
    {
        echo $id;
        // return view('cms.locker.log');
    }

    /**
     * Return student info taken from K12Admin with better formatting
     * 
     * @param String $student
     * @return Object $student
     */
    public function getStudentInfo (String $username)
    {
        echo $username;
        $student = $this->helpers->getStudentInfoFromK12Admin($username);
        // $fullname = explode(',', $student->fullname);
        // $fullname = $fullname[1] . " " . $fullname[0];
        // $school = explode(' ', $student->comment);
        // $school = $school[0];

        // // Base path for profile images
        // $url = '/cms/images/users/';

        // // Check image directory if profile image for user exists
        // $image_directory = glob(public_path($url) . $student->uid ."*.png");
        // if($image_directory ? $student_pic = $url . pathinfo($image_directory[0], PATHINFO_BASENAME) : $student_pic = $url . "user-placeholder.png");

        // $student->setAttribute('fullname', $student_info['fullname']);
        // $student->setAttribute('sysid', $student_info['sysid']);
        // $student->setAttribute('school', $student_info['school']);
        // $student->setAttribute('initialpassword', $student_info['pt']);
        // $student->setAttribute('studentpic', $student_info['student_pic']);
        
        // return $student;
        dd($student);
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
        
        $student_info = $this->helpers->getStudentInfoFromK12Admin($student->getFirstAttribute('samaccountname'));

        $student->setAttribute('fullname', $student_info->fullname);
        $student->setAttribute('sysid', $student_info->uid);
        $student->setAttribute('school', $student_info->school);
        $student->setAttribute('initialpassword', $student_info->pt);
        $student->setAttribute('studentpic', $student_info->student_pic);

        return view('cms.student.profile', [
            'student' => $student,
            'config' => $this->config
        ]);
    }
}
