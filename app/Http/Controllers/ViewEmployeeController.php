<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB; // Remove after testing
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Ldap\User;
use App\Ldap\Group;

class ViewEmployeeController extends Controller
{
    protected $fpdf;

    public function __construct ()
    {
        // Fetch config setup for locations, roles, and sub-departments
        $json = file_get_contents('cms/config.json');
        $this->config = json_decode($json, true);

        // Initialize FPDF for cards
        $this->fpdf = new Fpdf('L','mm',array(85,54));
    }

    public function test ()
    {
        ini_set('max_execution_time', 600);

        // // phpinfo();
        $students = Group::findBy('cn', 'student')->members()->get();
        $nessK=0; $ness01=0; $ness02=0; $ness03=0; $ness04=0; $ness05=0; $ness06=0; $ness07=0; $ness08=0; $ness09=0; $ness10=0; $ness11=0; $ness12=0;
        $aamesK=0; $aames01=0; $aames02=0; $aames03=0; $aames04=0; $aames05=0; $aames06=0; $aames07=0;
        $nbesK=0; $nbes01=0; $nbes02=0; $nbes03=0; $nbes04=0; $nbes05=0; $nbes06=0; $nbes07=0;
        $gesK=0; $ges01=0; $ges02=0; $ges03=0; $ges04=0; $ges05=0; $ges06=0; $ges07=0;


        foreach($students as $student): 
            $groups = $student->groups()->get();
            foreach($groups as $group):
                if(strpos($group->getName(), 'ness') !== FALSE || strpos($group->getName(), 'aames') !== FALSE || strpos($group->getName(), 'ges') !== FALSE || strpos($group->getName(), 'nbes') !== FALSE) {
                    ${$group->getName()}++;
                    $grade = $group->getName();
                }
            endforeach;
            
            if($grade === 'nessK' || $grade === 'aamesK' || $grade === 'gesK' || $grade === 'nbesK') {
                $school = strtoupper(str_replace('K', '', $grade));
                DB::connection('mysql2')
                ->table('lglist')
                ->upsert([
                    [
                        'userid' => $student->getFirstAttribute('samaccountname'),
                        'school' => $school,
                        'localgroup' => $grade
                    ]
                ], ['userid'], ['localgroup']);
            }
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
            "<br>Grade 8 Students: " . $ness08 . 
            "<br>Grade 9 Students: " . $ness09 . 
            "<br>Grade 10 Students: " . $ness10 . 
            "<br>Grade 11 Students: " . $ness11 . 
            "<br>Grade 12 Students: " . $ness12 . 
            "<br>Total NESS Students: " . $nessK+$ness01+$ness02+$ness03+$ness04+$ness05+$ness06+$ness07+$ness08+$ness09+$ness10+$ness11+$ness12 . "<br><br>";

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

        // // DB::connection('mysql2')
        // // ->table('info')
        // // ->where('Teacher', 'like', '%locker%')
        // // ->update(
        // //     [
        // //         'School' => 'NESS',
        // //         'Student' => NULL,
        // //         'user_uid' => NULL
        // //     ]
        // // );

        // echo "=============================================<br>";

        // $lockers = DB::connection('mysql2')->table('cart')
        // ->leftJoin('cart_type', 'cart_type.cart_type_id', '=', 'cart.cart_type_id')
        // ->select('cart.cart_id', 'cart.school_id', 'cart.cart_desc', 'cart.slot_start_number')
        // ->where('cart_type.cart_type_name', 'like', '%Locker%')
        // ->orderBy('cart.cart_name', 'asc')
        // ->get();

        // $slot_num = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28];

        // $studs = DB::connection('mysql2')->table('users')
        //         ->join('lglist', 'lglist.userid', '=', 'users.userid')
        //         ->select('users.fullname', 'users.comment', 'users.uid', 'lglist.localgroup')
        //         ->where('lglist.localgroup', 'ness09')
        //         ->where('users.comment', 'like', '%Student%')
        //         ->get();

        // foreach($lockers as $cart): 
        //     foreach($slot_num as $sn): 
        //         $slot = DB::connection('mysql2')->table('info')
        //         ->select('Name', 'user_uid')
        //         ->where('Cart', $cart->cart_id)
        //         ->where('Cart_Slot', $sn)
        //         ->orderBy('Cart_Slot', 'asc')
        //         ->first();


        //         DB::connection('mysql2')
        //         ->table('info')
        //         ->where('Name', $slot->Name)
        //         ->update(['user_uid' => NULL]);
        //         echo "<br>";

        //     endforeach;
        // endforeach;

        // foreach($studs as $st):
        //     echo "<b>Computer Assignment for " . $st->fullname . " - " . $st->uid . "</b>: ";

        //     foreach($slot_num as $sn): 
        //         $slot = DB::connection('mysql2')->table('info')
        //         ->select('Name', 'user_uid')
        //         ->where('Cart', 60)
        //         ->where('Cart_Slot', $sn)
        //         ->orderBy('Cart_Slot', 'asc')
        //         ->first();

        //         if($slot->user_uid === NULL) {
        //             // var_dump($slot);
        //             // echo "<br>";
        //             // break;
        //             DB::connection('mysql2')
        //             ->table('info')
        //             ->where('Name', $slot->Name)
        //             ->update(['user_uid' => $st->uid]);
        //             echo $slot->Name . "<br>";
        //             break;
        //         }
        //     endforeach;
        // endforeach;

        // echo "=============================================<br>";

        // $file = fopen("/Users/rbarrameda/Desktop/studdata.csv","r");

        // var_dump(fgetcsv($file));

        // echo "<br><br>";
        
        // $uid = 7013;
        // // $uid = 7800;
        // while ($row = fgetcsv($file)) {
        //     $studnum = str_replace('/[\xA0\xC2]/', '', $row[1]);
        //     $student = User::find('cn=' . $studnum . ',ou=Domain Users,dc=nisgaa,dc=bc,dc=ca');

        //     if($student !== NULL) {
        //         $school = $row[45];
        //         $grade = "Grade " . $row[26];

        //         // convert school
        //         switch($school){
        //             case "Nisga'a K-12": 
        //                 $school = "NESS";
        //                 break;
        //             case "Nathan Barton Elementary": 
        //                 $school = "NBES";
        //                 break;
        //             case "Alvin A.McKay Elementary": 
        //                 $school = "AAMES";
        //                 break;
        //             case "Gitwinksihlkw Elementary": 
        //                 $school = "GES";
        //                 break;
        //         }

        //         // if($grade != "Grade 12" && $grade != "Grade 11" && $grade != "Grade 10" && $grade != "Grade KF") $grade = str_replace("Grade ", "Grade 0", $grade);
        //         $grade = strtolower($school) . str_replace("Grade ", "", $grade);
        //         $grade = str_replace("F", "", $grade);
        //         $firstname = $row[10];
        //         $lastname = $row[9];
        //         $fullname = $lastname . " " . $firstname;
        //         $username = $row[1];
        //         $description = $school . " Student";
        //         $password = "P@ss" . substr($row[4], -4);
        //         $password = strval($password);

        //         echo $username . " = " . $lastname . " " . $firstname . " - " . $student->getFirstAttribute('mail') . " - " . $school . " - " . $grade . " - " . $password . "<br>";

        //         DB::connection('mysql2')
        //         ->table('users')
        //         ->updateOrInsert(
        //             ['userid' => $username],
        //             [
        //                 'userid' => $username,
        //                 'fullname' => $fullname,
        //                 'comment' => $description,
        //                 'pt' => $password
        //             ]
        //         );
                
        //         DB::connection('mysql2')
        //         ->table('lglist')
        //         ->upsert([
        //             [
        //                 'userid' => $username,
        //                 'school' => $school,
        //                 'localgroup' => 'student'
        //             ],
        //             [
        //                 'userid' => $username,
        //                 'school' => $school,
        //                 'localgroup' => $grade
        //             ],
        //             [
        //                 'userid' => $username,
        //                 'school' => $school,
        //                 'localgroup' => 'A3 Student Assignment'
        //             ]
        //         ], ['userid', 'school'], ['localgroup']);
        //     }
        // }

        // fclose($file);

        // $slot = 183;

        // for($i=155;$i<=$slot;$i++){
        //     DB::connection('mysql2')
        //     ->table('cart_slot')
        //     ->upsert([
        //         [
        //             'cart' => 63,
        //             'abs_slotindex' => $i,
        //             'connection_status' => 0
        //         ]
        //     ], ['cart', 'connection_status'], ['abs_slotindex']); 
        // }
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
     * Return data for /employees/create page
     * 
     * @param Object #employee
     * @return $employee_info
     */
    public function collectEmployeeInfo (Object $employee)
    {
        $employee_info = [];

        // Base path for profile images
        $url = '/cms/images/users/';

        // Check image directory if profile image for user exists
        $image_directory = glob(public_path($url) . $employee->getFirstAttribute('uidNumber') ."*.png");
        if($image_directory ? $employee_pic = $url . pathinfo($image_directory[0], PATHINFO_BASENAME) : $employee_pic = $url . "user-placeholder.png");

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

        // array_push($employee_info, $employee_pic, $locations, $sub_departments);
        $employee_info['employee_pic'] = $employee_pic;
        $employee_info['locations'] = $locations;
        $employee_info['sub_departments'] = $sub_departments;

        return $employee_info;
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

        $employee_info = $this->collectEmployeeInfo($employee);

        // Set up path based on {action}
        // Default {action} value = "view"
        // Redirect paths {action} value = "view" - /employees/{username}/view, {action} value = "update" - /employees/{username}/update
        if(isset($action) && !empty($action) && $action == 'update' ? $path = 'update.employee' : $path = 'profile');

        return view('cms.employee.' . $path, [
            'employee' => $employee,
            'employee_pic' => $employee_info['employee_pic'],
            'config' => $this->config,
            'locations' => $employee_info['locations'],
            'sub_departments' => $employee_info['sub_departments']
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

    /**
     * Handle process for downloading employee profile ID image
     *
     * @param String $username
     */
    public function viewEmployeeProfileIDImageDownload (String $username)
    {
        // Set employee object values
        $employee = User::find('cn=' . $username . ',cn=Users,dc=nisgaa,dc=bc,dc=ca');

        // dd($employee);
        $employee_info = $this->collectEmployeeInfo($employee);

        $name = $employee->getFirstAttribute('displayname');
        $department = $this->config['locations'][$employee->getFirstAttribute('department')]['name'];
        $address = $this->config['locations'][$employee->getFirstAttribute('department')]['address'];
        $city_province_postal = $this->config['locations'][$employee->getFirstAttribute('department')]['city'] . " " . 
                                $this->config['locations'][$employee->getFirstAttribute('department')]['province'] . " " . 
                                $this->config['locations'][$employee->getFirstAttribute('department')]['postal_code'];
        $phone = $this->config['locations'][$employee->getFirstAttribute('department')]['phone'];

        $logo = public_path('/nisgaa-icon.png');
        $barcode = public_path('/cms/images/barcode.png');
        $employee_pic = public_path($employee_info['employee_pic']);

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
        $this->fpdf->Cell(28,2.5,'EMPLOYEE',0,0,'C',FALSE);
        $this->fpdf->SetFont('Arial', '', 6.1);
        $this->fpdf->SetXY(36,1.8);
        $this->fpdf->Cell(37,2,$department,0,0,'R',FALSE);
        $this->fpdf->SetFont('Arial', '', 6);
        $this->fpdf->SetXY(36,5.5);
        $this->fpdf->Cell(37,1,'School District 92 (Nisga\'a)',0,0,'R',FALSE);
        $this->fpdf->SetXY(36,8);
        $this->fpdf->Cell(37,1,$address,0,0,'R',FALSE);
        $this->fpdf->SetXY(36,10.6);
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
        $this->fpdf->Image($employee_pic,3.2,12.5,32.5,35,'PNG');

        $this->fpdf->Output();

        exit;

        // dd($employee_info);
        // if (!isset($_GET['studID']))
        //         die('No student selected.');

        // $sdID = $_GET['studID'];                // The uid of the student to create the id for (will be changed to get it from the previous page)
        // $student = new User($sdID);                                                             // Creates the student object based off the uid received

        // $original_photo = "upload/user_photos/uid_$sdID.jpg";
        // if (!file_exists($original_photo))
        // die('Photo no longer exists.');

        // $fname = $student->getFname();
        // if (isset($_GET['fname']))
        //         $fname = $_GET['fname'];

        // $schoolObj = $student -> getSchool();
        // if (is_array($schoolObj)) {
        //         $schoolObj = $schoolObj[count($schoolObj) - 1];
        // }
        // $school = $schoolObj -> getName();                              // string - School abreviation associated with the selected student
        // $addr1 = $schoolObj -> getAddress();                    // string - Street address (if it exists) of the school
        // $addr2 = $schoolObj -> getCity();                               // string - City that the school is in
        // $tele = $schoolObj -> getPhone();                               // string - telephone number of the school
        // $bcID = $student -> getStudID();                                                        // int - BC ID for the selected student
        // $name = $fname . " ".$student->getLname();              // Assigned the full name of the student
        // $logo = 'images/id/logo.png';                                                   // The URL of the School District Logo
        // $code = "upload/temp/barcode_$sdID.png";                                                                                // Creates the barcode for the student, based on uid
        // barcode($sdID, $code);
        // $studPhoto = "upload/temp/cropped_$bcID.jpg";                           // The URL of the student photo to be used

        // // generate cropped & resized image
        // $crop = $student->getPortraitCrop();
        // if (!$crop)
        //         die('Could not find image crop information.');
        //         $x1 = $crop['x1'];
        // $y1 = $crop['y1'];
        // $x2 = $crop['x2'];
        // $y2 = $crop['y2'];
        // $tempWidth = $x2-$x1;                                           // calculates the width of the selection
        // $tempHeight = $y2-$y1;                                          // calculates the height of the selection
        // $tempImg = imagecreatetruecolor(219*2, 250*2);  // Creates an image object the width of what the student ID photo will be
        // $origImg = imagecreatefromjpeg($original_photo);        // Opens an image object of the current student ID
        // // uses the selected part of the image, and resamples it into the new image to be used for the ID
        // imagecopyresampled($tempImg, $origImg, 0, 0, $x1, $y1, 219*2, 250*2, $tempWidth, $tempHeight);
        // imagejpeg($tempImg, $studPhoto, 100);                   // saves the image to that location
        // imagedestroy($tempImg);                                         // destroys the temp image stored in memory

        // //This creates the PDF document with the correct formatting, and displays it in the browser
        // $card = new FPDF('L','mm',array(85,54));
        // $card->SetMargins(0,0,0);
        // $card->AddPage();
        // $card->SetFont('Helvetica','B',12);
        // $card->SetTextColor(255,255,255);
        // $card->SetFillColor(224,19,24);
        // $card->Rect(0,38,85,16,'F');
        // $card->SetFillColor(0,0,0);
        // $card->Rect(0,0,85,16,'F');
        // $card->Rect(5.4,15,30.4,30,'F');
        // $card->SetXY(5.75,5);
        // if ($student -> getPermissions() <= 1)
        //         $card->Cell(29.5,2.5,'STUDENT',0,0,'C',FALSE);
        // else
        //         $card->Cell(29.5,2.5,'EMPLOYEE',0,0,'C',FALSE);

        // $card->SetFontSize(7.5);
        // $card->SetXY(36,3.75);
        // $card->Cell(37,2,$school,0,0,'R',FALSE);
        // $card->SetFontSize(6);
        // $card->SetXY(36,7);
        // $card->Cell(37,1,'School District 92 (Nisga\'a)',0,0,'R',FALSE);
        // $card->SetFontSize(5);
        // $card->SetXY(36,9.3);
        // if ($addr1) {
        //         $card->Cell(37,1,$addr1,0,0,'R',FALSE);
        //         $card->SetXY(36,11.4);
        //         $card->Cell(37,1,$addr2,0,0,'R',FALSE);
        //         $card->SetXY(36,13.5);
        //         $card->Cell(37,1,$tele,0,0,'R',FALSE);
        // }
        // else {
        //         $card->Cell(37,1,$addr2,0,0,'R',FALSE);
        //         $card->SetXY(36,11.4);
        //         $card->Cell(37,1,$tele,0,0,'R',FALSE);
        // }
        // $card->Image($logo,73.5,2.5,8.5,13.3,'PNG');
        // $card->SetTextColor(0,0,0);
        // $card->SetFontSize(12);
        // $card->SetXY(36,18.25);
        // $card->Cell(49,4,$name,0,0,'C',FALSE);
        // $card->SetXY(36,23.25);

        // if ($student -> getPermissions() <= 1 && $bcID != $student->getUid())
        //         $card->Cell(49,4,$bcID,0,0,'C',FALSE);

        // $card->Image($code,43,29.25,34,6,'PNG');
        // $card->Image($studPhoto,5.75,9.6,29.5,35,'JPG');

        // $card->Output("card_$sdID.pdf", 'D');
        // unlink($studPhoto);                     // Deletes the temporary student photo created for thgis specific ID card
        // unlink($code);                          // Delets the temporary barcode image created for thios specific ID card

    }
}
