<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;



class StockOpnameController extends Controller
{
    public function index()
    {

        $data = ['title' => 'Stock Opname'];
        return view('pages.stock_opname.index', $data);
    }
}
