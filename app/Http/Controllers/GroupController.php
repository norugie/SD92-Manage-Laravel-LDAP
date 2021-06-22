<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Ldap\User;
use App\Ldap\Group;

class GroupController extends Controller
{
    public function index ()
    {
        return view ( 'cms.inactive.inactive' );
    }

    public function viewGroupProfileUpdate ( String $username, String $action )
    {
        // 
    }

    public function updateGroupProfile (String $groupname, Request $request)
    {
        // 
    }
}
