<?php

namespace App\Services;

use App\Models\Comment;

class CommentService
{
    public function update(Comment $comment, string $newText)
    {
        $comment->comment = $newText;
        $comment->save();
    }

    public function delete(Comment $comment): bool
    {
        if ($comment->parent) {
            foreach ($comment->childComments as $childComment) {
                $childComment->delete();
            }
        }

        return $comment->delete();
    }
}
