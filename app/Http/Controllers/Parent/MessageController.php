<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $parent = Auth::user();

        $messages = Message::where('receiver_id', $parent->id)
            ->orWhere('sender_id', $parent->id)
            ->with(['sender:id,name,role', 'receiver:id,name,role'])
            ->latest()
            ->paginate(20);

        $unreadCount = Message::where('receiver_id', $parent->id)->where('is_read', false)->count();

        // Mark all as read when viewing inbox
        Message::where('receiver_id', $parent->id)->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        // Get teachers to message (from the parent's children's class subjects)
        $children   = $parent->children()->pluck('users.id');
        $teacherIds = \App\Models\ClassSubject::whereHas('classroom.enrollments', fn($q) =>
                $q->whereIn('student_id', $children)->where('status','active'))
            ->pluck('teacher_id')
            ->unique();

        $teachers = User::whereIn('id', $teacherIds)->select('id','name')->get();

        return view('parent.messages.index', compact('messages', 'unreadCount', 'teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'subject'     => ['nullable', 'string', 'max:255'],
            'body'        => ['required', 'string', 'max:2000'],
            'student_id'  => ['nullable', 'exists:users,id'],
        ]);

        Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'student_id'  => $request->student_id,
            'subject'     => $request->subject,
            'body'        => $request->body,
            'is_read'     => false,
        ]);

        return back()->with('success', 'Message sent successfully.');
    }
}
