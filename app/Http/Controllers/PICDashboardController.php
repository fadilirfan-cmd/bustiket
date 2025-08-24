<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PICDashboardController extends Controller
{
   public function myBusInfo() {
    $bus = auth()->user()->load('bus');
    return request()->expectsJson()
        ? response()->json(['bus' => $bus])
        : view('pic.my-bus');
   }

    

    
}
