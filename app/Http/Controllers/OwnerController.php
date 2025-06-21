<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Owner'
        ];
        return view('pages.owner.index', $data);
    }
}
