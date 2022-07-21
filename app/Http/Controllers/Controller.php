<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use LdapRecord\Models\Attributes\AccountControl;
use App\Models\Log;
use Carbon\Carbon;
use Alert;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Handle process for logging activity
     *
     * @param String $user
     * @param String $description
     */
    public function inputLog(String $user, String $description)
    {
        $log = new Log();
        $log->log_description = $description;
        $log->log_user = $user;
        $log->save();
    }

    /**
     * Return data for /dashboard page
     * 
     * @return \Illuminate\View\View
     */
    public function requestLog ()
    {
        $log = new Log();

        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Displayed logs only show logs from the last 30 days
        return $log->whereBetween("created_at", [$startDate, $endDate])
                ->orderBy('created_at', 'DESC')
                ->get();
    }

    /**
     * Check user existence
     * 
     * @param mixed $user
     * @return String $message
     */

     public function checkUser ($user)
     {
        $message = "";

        // Check if $user exists
        if($user === NULL) $message = 'The user you are looking for does not exist in our directory.';
        else {
            // Check if $user UAC is active
            $uac = new AccountControl($user->getFirstAttribute('userAccountControl'));
            if($uac->has(AccountControl::ACCOUNTDISABLE)) $message = 'The user you are looking for no longer has an active account in our directory.';
        }

        return $message;
     }

    /**
     * Handle process for SweetAlert after-process alerts
     *
     * @param String $type
     * @param String $message
     */
    public function alertDetails (String $message, String $type)
    {
        switch($type){
            case 'create_success':
                Alert::html('Success', $message, 'success')
                    ->persistent(true)
                    ->showCloseButton()
                    ->showCancelButton('CLOSE', '#607d8b');
                break;
            case 'error':
                Alert::error('Something went wrong...', $message)
                    ->showCloseButton();
                break;
            default:
                Alert::html('Success', $message, $type)
                    ->showCloseButton();
        }
    }
}
