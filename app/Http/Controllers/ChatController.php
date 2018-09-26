<?php

namespace App\Http\Controllers;

use App\Events\PrivateChatEvent;
use App\Http\Resources\ChatResource;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class ChatController extends Controller
{
    public function send(Session $session, Request $request){

        $message = $session->messages()->create(['content'=> $request->content]);
        $chat= $message->createForSend($session->id);
        $message->createForReceive($session->id, $request->to_user);
        broadcast(new PrivateChatEvent($message->content, $chat));
        return response($message, 200);
    }

    public function chats(Session $session){
        return ChatResource::collection($session->chats->where('user_id', auth()->id()));
    }

    public function clear(Session $session)
    {
        $session->deleteChats();
        $session->chats->count() == 0 ? $session->deleteMessages() : '';
        return response('cleared', 200);
    }
}
