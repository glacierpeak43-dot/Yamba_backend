<?php

namespace App\Http\Controllers;

use App\Events\EveryOneEvent;
use App\Models\ForumCommentReply;
use App\Models\ForumComments;
use App\Models\ForumCommentss;
use App\Models\ForumCommentsVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumCommentVoteController extends Controller
{
    public function voteForumComments(Request $request, ForumComments $forumComment): \Illuminate\Http\JsonResponse
    {
        if ($forumComment->userVoted(Auth::user()->id)) {
            return response()->json(['success' => true, 'message' => 'You already voted on this comment'], 200);
        }
        $forumComment->votes()->create([
            'user_id' => Auth::user()->id,
            'forum_comment_id' => $forumComment->id

        ]);
        broadcast(new EveryOneEvent("Forum", "Forum vote"))->toOthers();
        return response()->json(['success' => true, 'message' => 'You have successfully voted on this comment'], 200);
    }

    public function voteForumCommentsReply(Request $request, ForumCommentReply $forumCommentReply): \Illuminate\Http\JsonResponse
    {
        if ($forumCommentReply->userVoted(Auth::user()->id)) {
            return response()->json(['success' => true, 'message' => 'You already voted on this reply'], 200);
        }
        $forumCommentReply->votes()->create([
            'user_id' => Auth::user()->id,
            'forum_comment_reply_id' => $forumCommentReply->id
        ]);
        broadcast(new EveryOneEvent("Forum", "Forum vote"))->toOthers();
        return response()->json(['success' => true, 'message' => 'You have successfully voted on this reply'], 200);
    }

    //create function to unvote on a comment
    public function unvoteForumComments(Request $request, ForumComments $forumComment): \Illuminate\Http\JsonResponse
    {
        $forumComment->votes()->where('user_id', Auth::user()->id)->delete();
        broadcast(new EveryOneEvent("Forum", "Forum vote"))->toOthers();
        return response()->json(['success' => true, 'message' => 'You have successfully unvoted on this comment'], 200);
    }

    //create function to unvote on a reply
    public function unvoteForumCommentsReply(Request $request, ForumCommentReply $forumCommentReply): \Illuminate\Http\JsonResponse
    {
        $forumCommentReply->votes()->where('user_id', Auth::user()->id)->delete();
        broadcast(new EveryOneEvent("Forum", "Forum vote"))->toOthers();
        return response()->json(['success' => true, 'message' => 'You have successfully unvoted on this reply'], 200);
    }
}
