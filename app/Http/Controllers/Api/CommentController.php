<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(protected CommentService $commentService) {}

    public function index($postId)
    {
        return response()->json($this->commentService->getPostComments($postId));
    }

    public function store(Request $request, $postId)
    {
        $data = $request->validate(['body' => 'required|string|max:1000']);
        $comment = $this->commentService->store(auth()->id(), $postId, $data['body']);
        return response()->json($comment, 201);
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);
        $data = $request->validate(['body' => 'required|string|max:1000']);
        return response()->json($this->commentService->update($comment, $data['body']));
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return response()->json(['message' => 'Removido com sucesso']);
    }
}