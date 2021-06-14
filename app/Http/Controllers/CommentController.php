<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Notifications\AuthorReceivedInnerCommentNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function storeComment(Comment $comment, Request $request)
    {
        $comment->load('postOwnerUser');

        $authUser = null;

        if (Auth::guard('sanctum')->check()) {
            Auth::shouldUse('sanctum');
            $authUser = Auth::user();
        }

        $request->validate([
            'text' => 'required|string'
        ]);

        $innerComment = new Comment([
            'text' => $request->text,
            'post_id' => $comment->post_id,
            'post_owner_user_id' => $comment->post_owner_user_id,
            'user_id' => @$authUser->id
        ]);

        $comment->comments()->save($innerComment);

        $comment->postOwnerUser->notify(new AuthorReceivedInnerCommentNotification);

        return new JsonResource($innerComment);
    }
}
