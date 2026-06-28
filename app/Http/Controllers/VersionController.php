<?php

namespace App\Http\Controllers;

use App\Models\ForcedUpdatesVersions;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    public function index(){
        $latestVersion = ForcedUpdatesVersions::all()->last();
        return $this->jsonSuccess(200, 'fetched successfully', $latestVersion, 'version');
    }
}
