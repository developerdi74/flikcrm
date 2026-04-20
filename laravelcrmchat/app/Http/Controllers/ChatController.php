<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $rooms = $user->chatRooms()->with(['users', 'lastMessage'])->get();
        $users = User::where('id', '!=', $user->id)->get();
        
        return view('chat.index', compact('rooms', 'users'));
    }

    public function createRoom(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:private,group',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $room = ChatRoom::create([
            'name' => $request->name,
            'type' => $request->type,
            'created_by' => Auth::id(),
        ]);

        $room->users()->attach(Auth::id());
        
        if ($request->has('user_ids') && !empty($request->user_ids)) {
            $room->users()->attach($request->user_ids);
        }

        return redirect()->route('chat.room', $room->id);
    }

    public function showRoom(ChatRoom $room)
    {
        if (!$room->users->contains(Auth::id())) {
            abort(403, 'У вас нет доступа к этой комнате');
        }

        $messages = $room->messages()->with('user')->latest()->take(50)->reverse()->get();
        $users = User::where('id', '!=', Auth::id())->get();
        
        return view('chat.room', compact('room', 'messages', 'users'));
    }

    public function sendMessage(Request $request, ChatRoom $room)
    {
        $request->validate([
            'message' => 'required|string|max:10000',
        ]);

        $message = ChatMessage::create([
            'room_id' => $room->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return back()->with('success', 'Сообщение отправлено');
    }

    public function markAsRead(ChatRoom $room)
    {
        $room->users()->updateExistingPivot(Auth::id(), ['read_at' => now()]);
        return response()->json(['success' => true]);
    }
}
