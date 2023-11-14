<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * @return UserResource
     */
    public function getUserData(): UserResource
    {
        $user = auth()->user();
        $user->load(['level', 'cards']);

        return new UserResource($user);
    }
}
