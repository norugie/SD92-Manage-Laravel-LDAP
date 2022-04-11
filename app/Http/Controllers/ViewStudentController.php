<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Http\Controllers\HelperStudentController;
use Illuminate\Http\Request;
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

        // Initialize FPDF for cards
        $this->fpdf = new Fpdf('L','mm',array(85,54));
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

    public function getStudentInfo($student)
    {
        $student_info = $this->helpers->getStudentInfoFromK12Admin($student->getFirstAttribute('samaccountname'));
        $grade = Group::findBy('cn', $student_info->localgroup)->getFirstAttribute('description');

        $student->setAttribute('fullname', $student_info->fullname);
        $student->setAttribute('sysid', $student_info->uid);
        $student->setAttribute('school', $student_info->school);
        $student->setAttribute('initialpassword', $student_info->pt);
        $student->setAttribute('studentpic', $student_info->student_pic);
        $student->setAttribute('grade', str_replace($student->school, '', $grade));

        return $student;
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
        
        $student = $this->getStudentInfo($student);

        return view('cms.student.profile', [
            'student' => $student,
            'config' => $this->config
        ]);
    }

    /**
     * Handle process for updating student profile ID image
     *
     * @param String $username
     * @param String $userID
     * @param \Illuminate\Http\Request $request
     */
    public function updateStudentProfileIDImage (String $username, Int $userID, Request $request)
    {
        // Base path for images
        $url = '/cms/images/users/';

        // Decode base64 data for image
        $data = $request->image;
        $image_parts = explode(";base64", $data);
        $data = base64_decode($image_parts[1]);
        
        // Set path for new image
        $filename = $userID . '_' . $username . '.png';
        $path = $url . $filename;

        // Upload image to designated image folder
        file_put_contents(public_path($path), $data);

        // Set student object values
        $student = $this->helpers->getStudentInfoFromK12Admin($username);
        $fullname = $student->fullname;
        
        // Log activity
        $message = 'The profile ID card for <b><a href="/cms/students/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been updated successfully.';
        $this->inputLog(session('userName'), $message);

        session()->flash('status', 'success');
        session()->flash('message', $message);
    }


    /**
     * Handle process for downloading student profile ID image
     *
     * @param String $username
     */
    public function viewStudentProfileIDImageDownload (String $username)
    {
        // Set student object values
        $student = User::find('cn=' . $username . ',ou="Domain Users",dc=nisgaa,dc=bc,dc=ca');
        $student = $this->getStudentInfo($student);

        $name = $student->getFirstAttribute('fullname');
        $school = $this->config['locations'][$student->getFirstAttribute('school')]['name'];
        $address = $this->config['locations'][$student->getFirstAttribute('school')]['address'];
        $city_province_postal = $this->config['locations'][$student->getFirstAttribute('school')]['city'] . " " . 
                                $this->config['locations'][$student->getFirstAttribute('school')]['province'] . " " . 
                                $this->config['locations'][$student->getFirstAttribute('school')]['postal_code'];
        $phone = $this->config['locations'][$student->getFirstAttribute('school')]['phone'];

        $logo = public_path('/nisgaa-icon.png');
        $barcode = public_path('/cms/images/barcode.png');
        $student_pic = public_path($student->getFirstAttribute('studentpic'));

        $this->fpdf->SetMargins(0,0,0);
        $this->fpdf->AddPage();
        $this->fpdf->SetFont('Arial','B', 10);
        $this->fpdf->SetTextColor(255,255,255);
        $this->fpdf->SetFillColor(224,19,24);
        $this->fpdf->Rect(0,38,85,16,'F');
        $this->fpdf->SetFillColor(0,0,0);
        $this->fpdf->Rect(0,0,85,16,'F');
        $this->fpdf->Rect(3,15,33,33,'F');
        $this->fpdf->SetXY(5.75,7);
        $this->fpdf->Cell(28,2.5,'STUDENT',0,0,'C',FALSE);
        $this->fpdf->SetFont('Arial', '', 6.1);
        $this->fpdf->SetXY(36,1.8);
        $this->fpdf->Cell(37,2,$school,0,0,'R',FALSE);
        $this->fpdf->SetFont('Arial', '', 6);
        $this->fpdf->SetXY(36,5.5);
        $this->fpdf->Cell(37,1,'School District 92 (Nisga\'a)',0,0,'R',FALSE);
        $this->fpdf->SetXY(36,8);
        $this->fpdf->Cell(37,1,$address,0,0,'R',FALSE);
        $this->fpdf->SetXY(36,10.4);
        $this->fpdf->Cell(37,1,$city_province_postal,0,0,'R',FALSE);
        $this->fpdf->SetXY(36,13);
        $this->fpdf->Cell(37,1,$phone,0,0,'R',FALSE);
        $this->fpdf->Image($logo,73.5,1.5,8.5,13.3,'PNG');
        $this->fpdf->SetTextColor(0,0,0);
        $this->fpdf->SetFont('Arial', 'B', 12);
        $this->fpdf->SetXY(36,22);
        $this->fpdf->Cell(49,4,$name,0,0,'C',FALSE);
        $this->fpdf->SetXY(36,23.25);
        $this->fpdf->Image($barcode,43,28,34,6,'PNG');
        $this->fpdf->Image($student_pic,3.2,12.5,32.5,35,'PNG');

        $this->fpdf->Output('card_' . $username . '.pdf', 'D');
    }
}
