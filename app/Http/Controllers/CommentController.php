<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentAddRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Models\Comment;
use App\Models\Film;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * getting a list of comments to a film
     * 
     * @return Responsable
     */
    public function index(Film $film)
    {
        return $this->success($film->comments()->latest()->get());
    }

    /**
     * addition of a review to a movie
     * 
     * @param CommentAddRequest $request
     * @return Responsable
     */
    public function add(CommentAddRequest $request, Film $film)
    {
        $film->comments()->create([
            'parent_id' => $request->parent_id,
            'rating' => $request->rating,
            'text' => $request->text,
            'user_id' => Auth::id(),
        ]);
        return $this->success([], 201);
    }

    /**
     * comment editing
     * 
     * @param  CommentUpdateRequest  $request
     * @param \App\Models\Comment $comment
     * @return Responsable
     */
    public function update(CommentUpdateRequest $request, Comment $comment)
    {
        $comment->update($request->validated());

        return $this->success($comment);
    }

    /**
     * comment deleting
     * 
     * @return Responsable
     */
    public function delete(Comment $comment, Gate $gate)
    {
        Gate::authorize('comment-delete', $comment);

        $comment->comments()->delete();
        $comment->delete();
        
        return $this->success([], 201);
    }
}
