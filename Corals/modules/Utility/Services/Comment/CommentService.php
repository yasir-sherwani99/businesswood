<?php

namespace Corals\Modules\Utility\Services\Comment;

use Corals\Foundation\Services\BaseServiceClass;
use Corals\Modules\Utility\Classes\Comment\CommentManager;

class CommentService extends BaseServiceClass
{
    public function createComment($request, $commentableClass, $commentable_hashed_id)
    {
        if (is_null($commentableClass)) {
            abort(400, 'Comment class is null');
        }

        $commentable = $commentableClass::findByHash($commentable_hashed_id);

        if (!$commentable) {
            abort(404, 'Not Found!!');
        }

        $data = $request->all();

        $commentableManagerClass = new CommentManager($commentable, user());

        $comment = $commentableManagerClass->createComment([
            'body' => $data['body'],
        ]);

        return $comment;
    }


    public function replyComment($request, $comment)
    {
        $data = $request->all();

        $commentableClass = new CommentManager($comment, user());

        $reply = $commentableClass->createComment([
            'body' => $data['body'],
        ]);

        return $reply;
    }
}