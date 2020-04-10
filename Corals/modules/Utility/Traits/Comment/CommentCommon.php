<?php

namespace Corals\Modules\Utility\Traits\Comment;


trait CommentCommon
{
    protected $commentService;
    protected $commentableClass = null;
    protected $redirectUrl = null;
    protected $successMessage = 'Utility::messages.comment.success.add';

    protected function setCommonVariables()
    {
        $this->commentableClass = null;
        $this->redirectUrl = null;
    }
}