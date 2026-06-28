<?php

namespace App\Http\Controllers;

use App\Events\ActionEvent;
use App\Http\Resources\ChatResource;
use App\Http\Resources\FriendRequestResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserSearchResource;
use App\Models\Chat;
use App\Models\ChatReport;
use App\Models\DeviceToken;
use App\Models\FriendRequests;
use App\Models\Messages;
use App\Models\Notifications;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\NoReturn;

class ChatController extends Controller
{

    public function sendMessage(Request $request, Chat $chat)
    {

        $data = $request->validate([
            'message' => 'required|min:1',
            'to' => 'required|numeric'
        ]);

        $user = Auth::user();

        $chat->messages()->create([
            'message' => $data['message'],
            'from' => $user->id,
            'attachement_url' => $request->hasFile('attachement') ? $this->uploadFile($request) : null
        ]);
        $fire = $this->sendNotification($data['message'], "Message From $user->name", $this->tokens($request->input('to')), $chat->id, 'notification');
        //        return $firebase;
        broadcast(new ActionEvent("Message", User::find($data['to']), "New message from $user->name"))->toOthers();
        return $this->jsonSuccess(
            200,
            "Message sent successfully",
            ["chat" => new ChatResource(Chat::find($chat->id)), "firebase" => $fire],
            "chat"
        );
    }

    //create function to read message
    public function readUnreadedChatMessage(Request $request, Chat $chat)
    {
        $user = Auth::user();
        $chat->messages()->where('is_read', false)->update(['is_read' => true]);
        return $this->jsonSuccess(200, "Message read successfully", Chat::find($chat->id), "chats");
    }

    public function blockChat(Request $request, Chat $chat)
    {
        $user = Auth::user();
        if ($user->id == $chat->user_1) {
            $chat->block_at_by_user_1 = true;
        } else {
            $chat->block_at_by_user_2 = true;
        }
        $chat->save();
        return $this->jsonSuccess(200, "Chat Block successfully", new ChatResource(Chat::find($chat->id)), "chat");
    }


    public function unblockChat(Request $request, Chat $chat)
    {
        $user = Auth::user();
        if ($user->id == $chat->user_1) {
            $chat->block_at_by_user_1 = false;
        } else {
            $chat->block_at_by_user_2 = false;
        }
        $chat->save();
        return $this->jsonSuccess(200, "Chat Unblocked successfully", new ChatResource(Chat::find($chat->id)), "chat");
    }

    public function uploadFile(Request $request)
    {
        $filenameWithExt = $request->file('attachement')->getClientOriginalName();
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        $extension = $request->file('attachement')->getClientOriginalExtension();
        $fileNameToStore = $filename . '_' . time() . '.' . $extension;
        $path = $request->file('attachement')->storeAs('public/messages', $fileNameToStore);

        return $fileNameToStore;
    }

    public function getChats()
    {
        $user = Auth::user();
        $chats = Chat::where('user_1', '=', $user->id)->orWhere('user_2', '=', $user->id)->orderBy('created_at', 'desc')->get();

        return ['data' => ChatResource::collection($chats)];
    }

    public function chatInit($from_user_id, User $user, $friendRequestId)
    {
        // dd($user->id);
        $chat = Chat::where('user_1', '=', $user->id, 'and')->where('user_2', '=', $from_user_id)
            ->orWhere('user_2', '=', $user->id, 'and')->where('user_1', '=', $from_user_id)->get();

        // dd($chat);
        if ($chat->first() != null) {
            return $this->jsonSuccess(200, 'Chat Already Exists', new ChatResource($chat), 'chat');
        } else {
            $chat = Chat::create([
                'user_1' => $user->id,
                'user_2' => $from_user_id,
                'type' => $user->type,
                'friend_requests' => $friendRequestId
            ]);
            return $chat;
        }
    }

    public function searchUsers(Request $request)
    {
        $query = $request->input('query');
        //create if query has value
        if ($query) {
            $searchResult = User::where('name', 'LIKE', '%' . $query . '%')->where('id', '!=', Auth::user()->id)
                ->orWhere('email', 'LIKE', '%' . $query . '%')->where('id', '!=', Auth::user()->id)->get();
            return $this->jsonSuccess(200, 'Users found', UserSearchResource::collection($searchResult), 'users');
        } else {
            //get 50 random users
            $users = User::where('id', '!=', Auth::user()->id)->inRandomOrder()->limit(50)->get();

            return $this->jsonSuccess(200, 'Users found', UserSearchResource::collection($users), 'users');
        }
    }


    public function sendFriendRequest(Request $request)
    {
        $data = $request->validate([
            'to_user_id' => 'required|numeric'
        ]);
        $user = User::find(Auth::user()->id);

        //check if already sent request
        if ($user->friendRequestAreadySent($user->id, $data['to_user_id'])) {
            return $this->jsonError(400, 'You have already sent a friend request to this user');
        }

        //check if already friend
        if ($user->isFriend($user->id, $data['to_user_id'])) {
            return $this->jsonError(400, 'You are already friends with this user');
        }

        $friend = User::find($data['to_user_id']);
        $friendRequest = new FriendRequests();
        $friendRequest->from_user_id = $user->id;
        $friendRequest->to_user_id = $data['to_user_id'];
        $friendRequest->save();

        $this->sendNotification(
            "$friend->name send you a friend request",
            "Friend Request",
            $this->tokens($friend->id),
            $friendRequest->id,
            'friend request'
        );
        //create notification
        $notification = Notifications::create([
            'user_id' => $friend->id,
            'message' => "You have a new friend request from $user->name",
            'type' => 'friend request',
            'friend_request_id' => $friendRequest->id,
        ]);
        broadcast(new ActionEvent("FriendRequest", $friend, "Friend request sent"))->toOthers();
        return $this->jsonSuccess(200, 'Friend Request Send', $friendRequest, 'data');
    }

    //create function to get friend requests
    public function getFriendRequests(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $friendRequests = $user->getFriendRequests();
        $sendRequests = $user->getSentRequest();
        return $this->jsonSuccess(200, 'Friend Requests', ["friend_requests" => FriendRequestResource::collection($friendRequests), "sent_request" => FriendRequestResource::collection($sendRequests)], 'friend_requests');
    }

    //create function to accept friend request
    public function acceptFrientRequest(Request $request)
    {
        $data = $request->validate([
            'friend_request_id' => 'required|numeric'
        ]);
        $user = User::find(Auth::user()->id);

        //check if friend request accepted
        $friendRequest = FriendRequests::find($data['friend_request_id']);

        if ($friendRequest->accepted == true || $friendRequest->accepted == 1) {
            return $this->jsonError(400, 'Friend Request Already Accepted');
        }

        $acceptor = User::find($friendRequest->to_user_id);

        $friendRequest->accepted = true;
        $friendRequest->save();
        $friend = User::find($friendRequest->from_user_id)->first();
        $this->sendNotification('Friend Request', " $acceptor->name Accepted Friend Request", $this->tokens($friendRequest->from_user_id), $friendRequest->id, 'chat');
        $this->chatInit($friendRequest->from_user_id, $user, $request->input('friend_request_id'));
        broadcast(new ActionEvent("FriendRequest", $friend, "Friend request accepted"))->toOthers();
        return $this->jsonSuccess(200, 'Friend Request Accepted', $friendRequest, 'data');
    }

    public function rejectFriendRequest(Request $request)
    {
        $data = $request->validate([
            'friend_request_id' => 'required|numeric'
        ]);
        $user = User::find(Auth::user()->id);
        //check if friend request accepted
        $friendRequest = FriendRequests::find($data['friend_request_id']);
        if ($friendRequest->accepted == false || $friendRequest->accepted == 0) {
            return $this->jsonError(400, 'Friend Request Already Rejected');
        }
        $friendRequest->accepted = false;
        $friendRequest->save();
        $acceptor = User::find($friendRequest->to_user_id);
        $this->sendNotification('Friend Request', "$acceptor->name Declined Friend Request", $this->tokens($friendRequest->from_user_id), $friendRequest->id, 'chat');
        broadcast(new ActionEvent("FriendRequest", User::find($friendRequest->from_user_id), "Friend request rejected"))->toOthers();
        return $this->jsonSuccess(200, 'Friend Request Rejected', $friendRequest, 'data');
    }


    //create function to delete chat
    public function deleteChat(Request $request, Chat $chat)
    {
        if ($request->input('deleted_at_by_user_1')) {
            $chat->deleted_at_by_user_1 = Carbon::now();
        } else if ($request->input('deleted_at_by_user_2')) {
            $chat->deleted_at_by_user_2 = Carbon::now();
        }
        $chat->save();
        return $this->jsonSuccess(200, 'Chat deletion successfull', $chat, 'chat');
    }

    //create function to delete chat
    public function userChats(Request $request)
    {
        $chats = Chat::where('user_1', Auth::user()->id)->orWhere('user_2', Auth::user()->id)->get();
        return $this->jsonSuccess(200, 'Request successfull', ChatResource::collection($chats), 'chats');
    }

    public function singleChat(Request $request, Chat $chat)
    {

        return $this->jsonSuccess(200, 'Request successfull', new ChatResource($chat), 'chat');
    }

    //delete chat message
    public function deleteMessage(Request $request, Messages $message)
    {
        if (Auth::user()->id == $message->from) {
            $message->deleted_at_by_user_1 = true;
        } else {
            $message->deleted_at_by_user_2 = true;
        }
        $message->save();

        return $this->jsonSuccess(200, "Message deleted successfully", new ChatResource(Chat::find($message->chat_id)), "chat");
    }

    //create function to block friend request user
    public function blockFriendRequestUser(Request $request)
    {
        $data = $request->validate([
            'friend_request_id' => 'required|numeric'
        ]);
        $friendRequest = FriendRequests::find($request->input('friend_request_id'));
        if ($request->input('blocked_at_by_user_1') == 1) {
            $friendRequest->blocked_at_by_user_1 = Carbon::now();
        } else if ($request->input('blocked_at_by_user_2') == 1) {
            $friendRequest->blocked_at_by_user_2 = Carbon::now();
        }
        $friendRequest->save();
        broadcast(new ActionEvent("FriendRequest", User::find($friendRequest->from_user_id), "Friend request blocked"))->toOthers();

        return $this->jsonSuccess(200, 'Friend Request Blocked', $friendRequest, 'frindRequest');
    }

    public function withDrawFriendRequestUser(Request $request)
    {
        $data = $request->validate([
            'friend_request_id' => 'required|numeric'
        ]);
        $friendRequest = FriendRequests::find($request->input('friend_request_id'));

        $friendRequest->delete();
        broadcast(new ActionEvent("FriendRequest", User::find($friendRequest->from_user_id), "Friend request rejected"))->toOthers();

        return $this->jsonSuccess(200, 'Friend Request Cancelled', $friendRequest, 'frindRequest');
    }

    public function blockAndReportUser(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'chat_id' => 'required',
            'user_to_report' => 'required'
        ]);

        $chat = Messages::where('chat_id', '=', $request->input('chat_id'))->latest()->take(5)->get();


        $chatReport = new ChatReport();
        $chatReport->user_id = Auth::user()->id;
        $chatReport->reported_user = $request->input('user_to_report');
        $chatReport->reported_message = $request->input('message');
        $chatReport->chat_evidence = $chat;
        $chatReport->save();

        $chatToDelete = Chat::find($request->input('chat_id'));
        $friendRequest = FriendRequests::find($chatToDelete->friend_requests);
        $friendRequest->delete();

        $chatToDelete->delete();


        return response()->json([
            'success' => true,
            'message' => 'User has been reported',
        ]);
    }
}
