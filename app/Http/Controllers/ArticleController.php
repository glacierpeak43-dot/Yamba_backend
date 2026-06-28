<?php

namespace App\Http\Controllers;

use App\Events\ActionEvent;
use App\Events\EveryOneEvent;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleCommentResource;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\SingleArticleResource;
use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleCommentReply;
use App\Models\ArticleCommentReplyReport;
use App\Models\ArticleCommentReport;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{

    public function index()
    {
        //get 20 random  articles
        $results = Article::inRandomOrder()
            ->take(15)->get();
        return $this->jsonSuccess(200, "Request Successful", SingleArticleResource::collection($results), "articles");
    }

    public function show(Article $article)
    {
        return $this->jsonSuccess(200, "Request Successful", new SingleArticleResource($article), "article");
    }

    public function comments(Article $article)
    {
        return $this->jsonSuccess(200, "Request Successful", ArticleCommentResource::collection($article->comments), "comments");
    }

    public function getArtcleCategories()
    {
        $categories = Category::all();
        return $this->jsonSuccess(200, "Request Successful", ArticleResource::collection($categories), "categories");
    }

    public function getArticleCategory(Category $category)
    {
        return $this->jsonSuccess(200, "Request Successful", SingleArticleResource::collection($category->articles), "articles");
    }
    public function getCategory(Category $category)
    {
        return $this->jsonSuccess(200, "Request Successful", new ArticleResource($category), "categories");
    }


    public function commentArticle(Request $request, Article $article)
    {

        $data = $request->validate([
            'comment' => 'string|required',
        ]);

        $user = Auth::user();

        $article->comments()->create([
            'user_id' => $user->id,
            'comment' => $data['comment'],
        ]);

        broadcast(new EveryOneEvent("ArticleComment", "New comment"))->toOthers();

        return $this->jsonSuccess(200, "Request Successful", ArticleCommentResource::collection(Article::find($article->id)->comments), "comment");
    }


    public function articleCommentReply(Request $request, ArticleComment $articleComment)
    {

        $data = $request->validate([
            'comment' => 'string|required',
        ]);
        // dd($articleComment);
        $user = Auth::user();
        $articleComment->replies()->create([
            'user_id' => $user->id,
            'comment' => $data['comment'],
        ]);
        broadcast(new EveryOneEvent("ArticleComment", "New comment"))->toOthers();

        return $this->jsonSuccess(200, "Request Successful", ArticleCommentResource::collection($articleComment->replies), "comment");
    }

    //create function to reply to comment


    public function getArticleComments(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        $comments = ArticleComment::where('article_id', $request->id)->orderBy('created_at', 'DESC')->paginate(50);

        $data = ArticleCommentResource::collection($comments);

        return response(['success' => true, 'article_id' => $request->id, 'comments' => $data]);
    }

    public function reportArticleComment(Request $request)
    {
        $request->validate([
            'comment_id' => 'required',
            'reason_for_report' => 'string|required'
        ]);

        $comment = ArticleComment::find($request->input('comment_id'));

        if ($comment == null) {
            return response(['success' => false, 'message' => 'cant find comment']);
        }
        // check if comment owner has more than 1 reported comment

        $reports = ArticleCommentReport::where('user_id', $comment->user_id)->get();

        foreach ($reports as $report) {
            if ($report->comment_id == $comment->id && $report->reported_by == Auth::user()->id) {
                //this user already reported this comment
                return response(['success' => false, 'message' => 'You already reported this comment']);
            }
        }

        if ($reports->count() >= 2) {
            //block user lol
            $user = User::find($comment->user_id);
            $user->update([
                'is_blocked_from_system' => true
            ]);
            // make comment unvisible
            $comment->update([
                'can_be_viewed' => false
            ]);
            //create report
            ArticleCommentReport::create([
                'user_id' => $comment->user_id,
                'reported_by' => Auth::user()->id,
                'reason_for_report' => $request->input('reason_for_report'),
                'comment_id' => $comment->id
            ]);
            return response(['success' => true, 'message' => 'Reported Successfully']);
        } else {
            // make comment unvisible
            $comment->update([
                'can_be_viewed' => false
            ]);
            //create report
            ArticleCommentReport::create([
                'user_id' => $comment->user_id,
                'reported_by' => Auth::user()->id,
                'reason_for_report' => $request->input('reason_for_report'),
                'comment_id' => $comment->id
            ]);
            return response(['success' => true, 'message' => 'Reported Successfully']);
        }
        broadcast(new EveryOneEvent("ArticleComment", "Comment report"))->toOthers();
    }

    public function reportArticleCommentReply(Request $request)
    {
        $request->validate([
            'comment_id' => 'required',
            'reason_for_report' => 'string|required'
        ]);

        $comment = ArticleCommentReply::find($request->input('comment_id'));

        // check if comment owner has more than 1 reported comment
        if ($comment == null) {
            return response(['success' => false, 'message' => 'cant find comment']);
        }

        $reports = ArticleCommentReplyReport::where('user_id', $comment->user_id)->get();

        foreach ($reports as $report) {
            if ($report->comment_id == $comment->id && $report->reported_by == Auth::user()->id) {
                //this user already reported this comment
                return response(['success' => false, 'message' => 'You already reported this comment']);
            }
        }

        if ($reports->count() >= 2) {
            //block user lol
            $user = User::find($comment->user_id);
            $user->update([
                'is_blocked_from_system' => true
            ]);
            // make comment unvisible
            $comment->update([
                'can_be_viewed' => false
            ]);
            //create report
            ArticleCommentReplyReport::create([
                'user_id' => $comment->user_id,
                'reported_by' => Auth::user()->id,
                'reason_for_report' => $request->input('reason_for_report'),
                'comment_id' => $comment->id
            ]);
            return response(['success' => true, 'message' => 'Reported Successfully']);
        } else {
            // make comment unvisible
            $comment->update([
                'can_be_viewed' => false
            ]);
            //create report
            ArticleCommentReplyReport::create([
                'user_id' => $comment->user_id,
                'reported_by' => Auth::user()->id,
                'reason_for_report' => $request->input('reason_for_report'),
                'comment_id' => $comment->id
            ]);
            return response(['success' => true, 'message' => 'Reported Successfully']);
        }
        broadcast(new EveryOneEvent("ArticleComment", "Comment report"))->toOthers();
    }
}
