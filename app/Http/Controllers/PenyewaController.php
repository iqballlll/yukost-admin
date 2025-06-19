<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenyewaController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Penyewa'
        ];
        return view('pages.penyewa.index', $data);
    }
}
