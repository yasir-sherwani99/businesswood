@section('css')
    <style>
        .comment {
            display: block;
            position: relative;
            margin-bottom: 30px;
            padding-left: 66px;
        }

        .comment .comment-author-ava {
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            width: 50px;
            border-radius: 50%;
            overflow: hidden;
        }

        .comment .comment-author-ava > img {
            display: block;
            width: 100%;
        }

        .comment .comment-body {
            position: relative;
            padding: 24px;
            border: 1px solid #e1e7ec;
            border-radius: 7px;
            background-color: #ffffff;
        }

        .comment .comment-body::after, .comment .comment-body::before {
            position: absolute;
            top: 12px;
            right: 100%;
            width: 0;
            height: 0;
            border: solid transparent;
            content: '';
            pointer-events: none;
        }

        .comment .comment-body::after {
            border-width: 9px;
            border-color: transparent;
            border-right-color: #ffffff;
        }

        .comment .comment-body::before {
            margin-top: -1px;
            border-width: 10px;
            border-color: transparent;
            border-right-color: #e1e7ec;
        }

        .comment .comment-title {
            margin-bottom: 8px;
            color: #606975;
            font-size: 14px;
            font-weight: 500;
        }

        .comment .comment-text {
            margin-bottom: 12px;
        }

        .comment .comment-footer {
            display: table;
            width: 100%;
        }

        .comment .comment-footer > .column {
            display: table-cell;
            vertical-align: middle;
        }

        .comment .comment-footer > .column:last-child {
            text-align: right;
        }

        .comment .comment-meta {
            color: #9da9b9;
            font-size: 13px;
        }

        .comment .reply-link {
            transition: color .3s;
            color: #606975;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: .07em;
            text-transform: uppercase;
            text-decoration: none;
        }

        .comment .reply-link > i {
            display: inline-block;
            margin-top: -3px;
            margin-right: 4px;
            vertical-align: middle;
        }

        .comment .reply-link:hover {
            color: #0da9ef;
        }

        .comment.comment-reply {
            margin-top: 30px;
            margin-bottom: 0;
        }

        @media (max-width: 576px) {
            .comment {
                padding-left: 0;
            }

            .comment .comment-author-ava {
                display: none;
            }

            .comment .comment-body {
                padding: 15px;
            }

            .comment .comment-body::before, .comment .comment-body::after {
                display: none;
            }
        }
    </style>
@endsection
<div class="tab-pane show" id="comments" role="tabpanel">
@if(count($comments))
    @foreach($comments as $comment)
        <!-- COMMENT -->
            <div class="comment">
                <!-- USER AVATAR -->
                <div class="comment-author-ava"><img src="{{ optional($comment->comment_author)->picture_thumb }}"
                                                     alt="Review author"></div>
                <!-- /USER AVATAR -->
                <div class="comment-body">
                    <p class="comment-text">{{$comment->body}}</p>
                    <div class="comment-footer"><span
                                class="comment-meta">{{ $comment->author->full_name }} - {{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @if(count($comment->comments))
                    @foreach($comment->comments as $reply)
                        <div class="comment comment-reply">
                            <!-- USER AVATAR -->
                            <div class="comment-author-ava"><img src="{{ optional($reply->author)->picture_thumb }}"
                                                                 alt="Reply author"></div>
                            <div class="comment-body">
                                <p class="comment-meta">{{optional($reply->author)->full_name}}
                                    - {{ $comment->created_at->diffForHumans() }}</p>
                                <p>{{$reply->body}}</p>
                            </div>
                        </div>
                    @endforeach
                @endif
                @if(user() && user()->can('Utility::comment.reply') && ((optional($comment->comment_author)->id == user()->id) || ($item->created_by == user()->id ) )   )
                <!-- COMMENT REPLY -->
                    <div class="comment comment-reply">
                        <div class="comment-author-ava"><img src="{{ user()->picture_thumb }}"
                                                             alt=""></div>
                        <!-- /USER AVATAR -->
                        <form class="custom-form ajax-form comment-reply-form"
                              action="{{url('cms/'.$comment->hashed_id.'/create-reply' )}}"
                              method="POST"
                              data-page_action="site_reload">
                            <div class="form-group required-field">
                                <textarea name="body" class="form-control custom-radius" cols="10" rows="2"
                                          style="height: 80px"
                                          placeholder="@lang('CMS::labels.template.add_reply_text')"></textarea>
                            </div>
                            <button type="submit"
                                    class="btn btn-primary"
                                    style="margin: 0;">@lang('CMS::labels.template.add_reply')
                                <i class="fa fa-paper-plane-o"
                                   aria-hidden="true"></i></button>
                        </form>
                    </div>
                @endif
            </div>
        @endforeach
        <hr class="line-separator">
    @endif
    @if(user() && user()->can('Utility::comment.create'))
        <h3>Leave a Comment</h3>
        <div class="comment comment-reply">
            <div class="comment-author-ava"><img src="{{ user()->picture_thumb }}"
                                                 alt=""></div>
            <form class="custom-form ajax-form comment-reply-form"
                  action="{{url('cms/'.$item->hashed_id.'/create-comment' )}}"
                  method="POST"
                  data-page_action="site_reload">
                <div class="form-group required-field">
                                <textarea name="body" class="form-control custom-radius" cols="10" rows="2"
                                          style="height: 80px"
                                          placeholder="@lang('CMS::labels.template.add_comments')"></textarea>
                </div>
                <button type="submit"
                        class="btn btn-success "
                        style="margin: 0;">@lang('CMS::labels.template.add_comment')
                    <i class="fa fa-paper-plane-o"
                       aria-hidden="true"></i></button>
            </form>
        </div>
    @endif
</div>
