<?php

namespace App\Http\Controllers;

use App\Events\EveryOneEvent;
use App\Http\Resources\ArticleCommentResource;
use App\Models\ArticleComment;
use App\Models\ArticleCommentReply;

use App\Models\ArticleCommentVote;
use App\Models\ArticleCommentReplyVote;
use Illuminate\Http\Request;
use App\Models\ArticlesVotes;
use App\Models\ArticlesReplyVotes;
use Illuminate\Support\Facades\Auth;


class ArticlesVotesController extends Controller
{
    public function voteArticleComment(Request $request, ArticleComment $articleComment): \Illuminate\Http\JsonResponse
    {
        if ($articleComment->userVoted(Auth::user()->id)) {
            return response()->json(['message' => 'You already voted on this comment'], 200);
        }
        $articleComment->votes()->create([
            'user_id' => Auth::user()->id
        ]);
        broadcast(new EveryOneEvent("Article", "Article vote"))->toOthers();
        return response()->json(['message' => 'You have successfully voted on this comment', "success" => true], 200);
    }

    public function voteArticleCommentReply(Request $request, ArticleCommentReply $articleCommentReply): \Illuminate\Http\JsonResponse
    {
        if ($articleCommentReply->userVoted(Auth::user()->id)) {
            return response()->json(['message' => 'You already voted on this reply', "success" => true], 200);
        }
        $articleCommentReply->votes()->create([
            'user_id' => Auth::user()->id
        ]);
        broadcast(new EveryOneEvent("Article", "Article vote"))->toOthers();
        return response()->json(['message' => 'You have successfully voted on this reply', "success" => true], 200);
    }



    //create function to unvote on a comment
    public function unvoteArticleComment(Request $request, ArticleComment $articleComment): \Illuminate\Http\JsonResponse
    {
        $articleComment->votes()->where('user_id', Auth::user()->id)->delete();
        broadcast(new EveryOneEvent("Article", "Article vote"))->toOthers();
        return response()->json(['message' => 'You have successfully unvoted on this comment', "success" => true], 200);
    }

    //create function to unvote on a reply
    public function unvoteArticleCommentReply(Request $request, ArticleCommentReply $articleCommentReply): \Illuminate\Http\JsonResponse
    {
        $articleCommentReply->votes()->where('user_id', Auth::user()->id)->delete();
        broadcast(new EveryOneEvent("Article", "Article vote"))->toOthers();
        return response()->json(['message' => 'You have successfully unvoted on this reply', "success" => true], 200);
    }
}
