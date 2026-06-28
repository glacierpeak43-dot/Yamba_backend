<?php

namespace App\Http\Controllers;

use App\Models\University;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        return $this->jsonSuccess(200, "Request Successful", University::all(), "universities");
    }

    public function show(University $university): \Illuminate\Http\JsonResponse
    {
        return $this->jsonSuccess(200, "Request Successful", $university, "university");
    }
}
