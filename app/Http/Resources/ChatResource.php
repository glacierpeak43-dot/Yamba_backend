<?php

namespace App\Http\Resources;

use App\Models\Messages;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public static $wrap = 'data';

    public function toArray($request)
    {
        $messages = Messages::where('chat_id', '=', $this->id)->get();
        $lastMessage = Messages::where('chat_id', '=', $this->id)->latest()->first();
        $unreaded_count = Messages::where('chat_id', '=', $this->id)->where('is_read', false)->count();
        $friend = User::where('id', '=', $this->user_1 == Auth::user()->id ? $this->user_2 : $this->user_1)->first();
        $current = User::find(Auth::user()->id);
        return [
            'id' => $this->id,
            'friends' => true,
            'opponent' => $friend,
            'current_user' => $current,
            'type' => $this->type,
            'blocked_chat' => $this->blockedThatChat(Auth::user()->id),
            'is_blocked' => $this->blockedByUser($friend->id),
            'last_message' => new MessageResource($lastMessage),
            'unreaded_count' => $unreaded_count,
            'messages' => MessageResource::collection($messages),
            'created_at' => $this->created_at->diffForHumans(),
            'created_at_model' => $this->created_at
        ];
    }
}
