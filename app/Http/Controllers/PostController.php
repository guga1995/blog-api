<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostFile;
use App\Notifications\AuthorReceivedCommentNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function storeFile(Post $post, Request $request)
    {
        $type = $request->input('type');

        $sizeMap = [
            'image' => [
                'type' => 'jpg,jpeg,png',
                'size' => '4000',
            ],
            'video' => [
                'type' => 'mpeg,mp4,webm',
                'size' => '2097152',
            ],
        ];

        $request->validate([
            'file' => 'required|file|mimes:' . $sizeMap[$type]['type'] . '|max:' . $sizeMap[$type]['size'],
            'type' => ['required', Rule::in(config('enums.post_file_types'))]
        ]);

        $fileModel = new PostFile();

        $fileModel->type = $request->type;

        $file = $request->file('file');

        if ($file && $file->isValid()) {
            $path = $file->store('post_files');
            $filename = pathinfo($path)['basename'];
            if ($path) {
                $fileModel->setAttribute('filename', $filename);
            }
        }

        $post->files()->save($fileModel);

        return new JsonResource($fileModel);
    }

    public function storeComment(Post $post, Request $request)
    {
        $post->load('user');

        $authUser = null;

        if (Auth::guard('sanctum')->check()) {
            Auth::shouldUse('sanctum');
            $authUser = Auth::user();
        }

        $request->validate([
            'text' => 'required|string'
        ]);

        $comment = new Comment([
            'text' => $request->text,
            'post_owner_user_id' => $post->user_id,
            'user_id' => @$authUser->id
        ]);

        $post->comments()->save($comment);

        $post->user->notify(new AuthorReceivedCommentNotification);

        return $comment;
    }
}
