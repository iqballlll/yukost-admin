<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = ['title' => 'Dashboard'];
        return view('pages.dashboard.index', $data);
    }
}
