<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\ProfileLevel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProfileLevel  $profileLevel
     * @return \Illuminate\Http\Response
     */
    public function show(ProfileLevel $profileLevel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProfileLevel  $profileLevel
     * @return \Illuminate\Http\Response
     */
    public function edit(ProfileLevel $profileLevel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProfileLevel  $profileLevel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProfileLevel $profileLevel)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProfileLevel  $profileLevel
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProfileLevel $profileLevel)
    {
        //
    }
}
