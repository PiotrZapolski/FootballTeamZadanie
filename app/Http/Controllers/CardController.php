<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class CardController extends Controller
{
    public function getCards(): JsonResponse
    {
        return response()->json();
    }
}
