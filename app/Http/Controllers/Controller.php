<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Log;
use Carbon\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function inputLog(String $user, String $description)
    {
        $log = new Log();
        $log->log_description = $description;
        $log->log_user = $user;
        $log->save();
    }

    public function requestLog ()
    {
        $log = new Log();

        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        return $log->whereBetween("created_at", [$startDate, $endDate])
                ->orderBy('created_at', 'DESC')
                ->get();
    }
}
