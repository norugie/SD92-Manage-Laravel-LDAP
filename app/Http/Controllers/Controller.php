<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
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
     * Handle process for SweetAlert after-process alerts
     *
     * @param String $type
     * @param String $message
     */
    public function alertDetails (String $type, String $message)
    {
        switch($type){
            case 'create_success':
                Alert::html('Success', $message, 'success')
                    ->persistent(true)
                    ->showCloseButton()
                    ->showConfirmButton('CLOSE', '#607d8b');
                break;
            case 'error':
                Alert::error('Something went wrong...', $message);
                break;
            default:
                Alert::html('Success', $message, $type);
        }
    }
}
