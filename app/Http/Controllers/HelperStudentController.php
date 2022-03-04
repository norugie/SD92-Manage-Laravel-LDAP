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
        ->select('fullname', 'comment', 'uid', 'pt')
        ->where('userid', $username)
        ->first();

        return $student;
    }

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
        ->select('cart.cart_id', 'cart.school_id', 'cart.cart_desc', 'cart.slot_start_number')
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
        $lockers = DB::connection('mysql2')
        ->table('cart_slot')
        ->leftJoin('info', 'cart_slot.abs_slotindex', '=', 'info.Cart_Slot')
        ->leftJoin('users', 'users.uid', '=', 'info.user_uid')
        ->select('cart_slot.abs_slotindex', 'info.Name', 'info.Cart_Slot', 'users.fullname')
        ->where('cart_slot.cart', '=', $cart->cart_id)
        ->get();
        
        return $lockers;
    }
     // --- END: K12Admin-related processes here --- //
}
