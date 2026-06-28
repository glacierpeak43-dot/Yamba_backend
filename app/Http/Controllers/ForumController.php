<?php

namespace App\Http\Controllers;

use App\Events\EveryOneEvent;
use App\Http\Resources\ForumCategoryResource;
use App\Http\Resources\ForumCommentResource;
use App\Http\Resources\ForumResource;
use App\Models\Forum;
use App\Models\ForumCategories;
use App\Models\ForumCommentReply;
use App\Models\ForumCommentReplyReport;
use App\Models\ForumCommentReport;
use App\Models\ForumComments;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index()
    {
        return $this->jsonSuccess(200, "Request Successful", ForumResource::collection(Forum::all()), "forums");
    }

    public function show(Forum $forum)
    {
        return $this->jsonSuccess(200, "Request Successful", new ForumResource($forum), "forum");
    }

    public function getForumCategories()
    {
        $categories = ForumCategories::all();
        return $this->jsonSuccess(200, "Request Successful", ForumCategoryResource::collection($categories), "categories");

    }

    public function getForumsByCategory($id)
    {
        $categories = Forum::where('forum_category_id', $id)->get();
        return $this->jsonSuccess(200, "Request Successful", ForumResource::collection($categories), "forums");
    }

    public function getForumForumCategories(ForumCategories $category)
    {
        return $this->jsonSuccess(200, "Request Successful", new ForumResource($category), "category");
    }


    public function commentForum(Request $request, Forum $forum)
    {

        $data = $request->validate([
            'comment' => 'string|required',
        ]);

        $user = Auth::user();

        $forum->comments()->create([
            'user_id' => $user->id,
            'comment' => $data['comment'],
        ]);

        broadcast(new EveryOneEvent("ForumComment", "Forum comment"))->toOthers();
        return response()->json(['success' => true, "message" => "Posted comment"]);

    }

    //create function to reply to comment
    public function forumCommentReply(Request $request, ForumComments $comment)
    {
        $data = $request->validate([
            'comment' => 'string|required',
        ]);
        $user = Auth::user();
        $comment->replies()->create([
            'user_id' => $user->id,
            'forum_comment_id' => $comment->id,
            'comment' => $data['comment'],
        ]);
        broadcast(new EveryOneEvent("ForumComment", "Forum comment"))->toOthers();

        return response()->json(['success' => true, "message" => "Posted comment"]);
    }

    public function getForumComments($id)
    {

        $comments = ForumComments::where('forum_id', $id)->orderBy('created_at', 'DESC')->paginate(50);

        $data = ForumCommentResource::collection($comments);

        return response(['success' => true, 'forum_id' => $id, 'comments' => $data]);
    }

    public function reportForumCommentReply(Request $request)
    {
        $request->validate([
            'comment_id' => 'required',
            'reason_for_report' => 'string|required'
        ]);

        $comment = ForumCommentReply::find($request->input('comment_id'));

        // check if comment owner has more than 1 reported comment

        $reports = ForumCommentReplyReport::where('user_id', $comment->user_id)->get();

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
            ForumCommentReplyReport::create([
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
            ForumCommentReplyReport::create([
                'user_id' => $comment->user_id,
                'reported_by' => Auth::user()->id,
                'reason_for_report' => $request->input('reason_for_report'),
                'comment_id' => $comment->id
            ]);
            return response(['success' => true, 'message' => 'Reported Successfully']);
        }
        broadcast(new EveryOneEvent("ForumComment", "Forum report"))->toOthers();

    }

    public function reportForumComment(Request $request)
    {
        $request->validate([
            'comment_id' => 'required',
            'reason_for_report' => 'string|required'
        ]);

        $comment = ForumComments::find($request->input('comment_id'));

        // check if comment owner has more than 1 reported comment

        $reports = ForumCommentReport::where('user_id', $comment->user_id)->get();

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
            ForumCommentReport::create([
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
            ForumCommentReport::create([
                'user_id' => $comment->user_id,
                'reported_by' => Auth::user()->id,
                'reason_for_report' => $request->input('reason_for_report'),
                'comment_id' => $comment->id
            ]);
            return response(['success' => true, 'message' => 'Reported Successfully']);
        }
        broadcast(new EveryOneEvent("ForumComment", "Forum report"))->toOthers();

    }

    public function checkForumConsent(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $user = Auth::user();
        if ($user->has_agreed_forum == 1) {
            return response(['success' => true, 'message' => 'you have accepted terms and conditions']);
        } else {
            return response(['success' => false, 'message' => 'you have not yet accepted terms and conditions',]);
        }
    }


}
