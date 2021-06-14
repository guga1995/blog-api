<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function storePost(User $user, Request $request)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        $post = new Post([
            'text' => $request->text
        ]);

        $user->posts()->save($post);

        return new JsonResource($post);
    }

    public function showAuth()
    {
        $user = Auth::user();
        return new JsonResource($user);
    }
}
