<?php


namespace Corals\Modules\CMS\Http\Controllers;

use Corals\Modules\CMS\Models\Post;
use Corals\Modules\Utility\Http\Controllers\Comment\CommentBaseController;

class CommentController extends CommentBaseController
{
    protected function setCommonVariables()
    {
        $this->commentableClass = Post::class;
    }

}