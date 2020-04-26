<?php

namespace App\Http\Controllers;

use App\Friend;
use App\User;
use App\Http\Resources\Friend as FriendResources;
use App\Exceptions\UserNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class FriendRequestController extends Controller
{
    public function store()
    {
        try {
            $data = request()->validate([
                'friend_id' => 'required',
            ]);
        } catch(ValidationException $e) {
            throw new ValidationException(json_encode($e->errors()));
        }

        try {
            User::findOrFail($data['friend_id'])
                ->friends()->attach(auth()->user());
        } catch(ModelNotFoundException $e) {
            throw new UserNotFoundException();
        }

        return new FriendResources(
            Friend::where('user_id', auth()->user()->id)
                ->where('friend_id', $data['friend_id'])
                ->first()
        );
    }
}
