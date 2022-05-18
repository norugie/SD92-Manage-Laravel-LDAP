<?php

namespace App\Http\Controllers;

/**
 * 
 * This controller file is used across other controllers. The HelperStudentController class
 * contains methods used to handle most of the processes needed to manage student accounts.
 */

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HelperStudentController extends Controller
{
    // --- K12Admin-related processes here --- //

    // ---*** Students ***--- //

    /**
     * Handle process for getting student info from K12Admin
     *
     * @return Object $students
     */
    public function getStudentIndexFromK12Admin ()
    {
        // $students = DB::connection('mysql2')
        // ->table('users')
        // ->leftJoin('lglist', 'users.userid', '=', 'lglist.userid')
        // ->select('users.fullname', 'users.userid', 'users.uid', 'users.pt', 'lglist.school', 'lglist.localgroup')
        // ->where('lglist.localgroup', 'student')
        // ->where('users.comment', 'like', '%student%')
        // ->orderBy('users.userid', 'ASC')
        // ->get();

        $students = DB::connection('mysql2')
        ->table('users')
        ->leftJoin('lglist', 'users.userid', '=', 'lglist.userid')
        ->select('users.fullname', 'users.userid', 'users.uid', 'users.pt', 'lglist.school', 'lglist.localgroup')
        ->where('lglist.localgroup', '!=', 'student')
        ->where('lglist.localgroup', 'not like', '%A3 Student Assignment%')
        ->where('users.comment', 'like', '%student%')
        ->where('users.userid', 'regexp', '^-?[0-9]+$')
        ->orderBy('users.userid', 'ASC')
        ->get();

        return $students;
    }

    /**
     * Handle process for getting student info from K12Admin
     *
     * @param String $username
     * @return Object $student
     */
    public function getStudentInfoFromK12Admin (String $username)
    {
        $student = DB::connection('mysql2')
        ->table('users')
        ->leftJoin('lglist', 'users.userid', '=', 'lglist.userid')
        ->select('users.fullname', 'users.uid', 'users.uid', 'users.pt', 'lglist.school', 'lglist.localgroup')
        ->where('users.userid', $username)
        ->where('localgroup', '!=', 'student')
        ->first();

        $fullname = explode(',', $student->fullname);
        $student->fullname = $fullname[1] . " " . $fullname[0];

        // Base path for profile images
        $url = '/cms/images/users/';

        // Check image directory if profile image for user exists
        $image_directory = glob(public_path($url) . $student->uid ."*.png");
        if($image_directory ? $student->student_pic = $url . pathinfo($image_directory[0], PATHINFO_BASENAME) : $student->student_pic = $url . "user-placeholder.png");

        return $student;
    }

    /**
     * Handle process for setting ID info in K12Admin
     *
     * @param Int $uid
     * @param mixed $rfid
     */
    public function setStudentIDInK12Admin (Int $uid, $rfid)
    {
        $data_id = '-' . $uid;

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
     * Handle process for disabling ID in K12Admin
     *
     * @param Int $uid
     */
    public function disableStudentIDInK12Admin (Int $uid)
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

    // ---*** Lockers ***--- //

    /**
     * Handle process for getting cart info from K12Admin
     *
     * @return Object $carts
     */
    public function getLockerCartIndexFromK12Admin()
    {
        $carts = DB::connection('mysql2')
        ->table('cart')
        ->leftJoin('cart_type', 'cart.cart_type_id', '=', 'cart_type.cart_type_id')
        ->select('cart.cart_id', 'cart.school_id', 'cart.cart_desc', 'cart.slot_start_number', 'cart_type.slot_amount')
        ->where('cart_type.cart_type_name', 'like', '%Locker%')
        ->orderBy('cart.cart_name', 'ASC')
        ->get();

        return $carts;
    }

    /**
     * Handle process for getting cart info from K12Admin
     *
     * @param Object $cart
     * @return Object $lockers
     */
    public function getLockerInfoFromK12Admin(Object $cart)
    {
        $cid = $cart->cart_id;
        $start_number = $cart->slot_start_number;
        $lockers = DB::connection('mysql2')
        ->table('cart_slot')
        ->leftJoin('info', function($join) use ($cid, $start_number){
            $join->on('info.Cart', '=', \DB::raw($cid))
            ->on(\DB::raw($start_number . '+ info.Cart_Slot - 1'), '=', 'cart_slot.abs_slotindex');
        })
        ->leftJoin('users', 'users.uid', '=', 'info.user_uid')
        ->select('info.Name', 'users.fullname', 'users.userid', 'users.uid', 'cart_slot.abs_slotindex', 'cart_slot.connection_status')
        ->where('cart_slot.cart', '=', $cid)
        ->orderBy('cart_slot.abs_slotindex', 'ASC')
        ->get();
        
        return $lockers;
    }
     // --- END: K12Admin-related processes here --- //
}
