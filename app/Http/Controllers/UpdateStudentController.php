<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperStudentController;
use Illuminate\Http\Request;
use App\Ldap\User;
use App\Ldap\Group;

class UpdateStudentController extends Controller
{
    public function __construct ()
    {
        $this->helpers = new HelperStudentController;
    }
    
    /**
     * Handle process for assigning/updating student profile ID RFID info
     *
     * @param String $username
     * @param \Illuminate\Http\Request $request
     */
    public function updateStudentProfileID (String $username, Int $userID, Request $request)
    {
        // Fetch student data
        $student = User::find('cn=' . $username . ',ou="Domain Users",dc=nisgaa,dc=bc,dc=ca');
        
        if($request->student_rfid){
            $this->helpers->setStudentIDInK12Admin($userID, $request->student_rfid);
            $student->employeeNumber = $request->student_rfid;
        } else {
            $this->helpers->disableStudentIDInK12Admin($userID);
            $student->employeeNumber = NULL;
        }

        $student->save();

        // Set student object values
        $student = $this->helpers->getStudentInfoFromK12Admin($username);
        $fullname = $student->fullname;
        
        // Log activity
        $message = 'The profile ID RFID code for <b><a href="/cms/students/' . $username . '/view" class="alert-link">' . $fullname . '</a></b> has been updated successfully.';
        $this->inputLog(session('userName'), $message);

        return redirect('/cms/students/' . $username . '/view')
            ->with('status', 'success')
            ->with('message', $message);
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
}
