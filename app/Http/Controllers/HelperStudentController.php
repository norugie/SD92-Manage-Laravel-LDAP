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
     * @return Array $student
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

     // --- END: K12Admin-related processes here --- //
}
