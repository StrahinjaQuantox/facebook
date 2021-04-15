<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Services\CommentService;
use Illuminate\Http\Request;
use App\Models\Post;
class CommentController extends Controller
{
    public function __construct(CommentService $service){
        $this->service = $service;
    }

    public function store(Post $post, CommentRequest $request){
        return $this->service->store($request, $post);
    }

    public function listForPost(Post $post){

        return \view()->make('comments/comments_partial', compact('post'));
    }

}
