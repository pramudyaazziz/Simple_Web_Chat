<?php

namespace App\Repositories;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Events\ConversationEvent;

class ConversationRepository
{
    public function getChatHistory()
    {
        $chatHistory = Conversation::leftJoin('messages', function ($join) {
                $join->on('conversations.id', '=', 'messages.conversation_id')
                    ->whereRaw('messages.created_at = (
                        SELECT MAX(created_at) FROM messages WHERE messages.conversation_id = conversations.id
                    )');
            })
            ->where('user_one', Auth::user()->id)
            ->orWhere('user_two', Auth::user()->id)
            ->orderByRaw('
                (SELECT MAX(created_at) FROM messages WHERE messages.conversation_id = conversations.id) DESC
            ')
            ->get()
            ->map(function ($conversation) {
                return $this->formatChatHistory($conversation);
            });

        return $chatHistory;
    }

    public function getConversationById($id)
    {
        $conversation = Conversation::findOrFail($id);

        return $this->formatConversation($conversation);
    }

    // Private Function

    private function formatChatHistory($conversation)
    {
        $interlocutor_id = $this->getInterlocutorId($conversation);
        return [
            'id' => $conversation->conversation_id,
            'interlocutor' => $this->getInterlocutorById($interlocutor_id),
            'last_message' => $conversation->body,
            'unread_messages' => count(Conversation::find($conversation->conversation_id)->unreadMessages)
        ];
    }

    private function formatConversation($conversation)
    {
        $interlocutor_id = $this->getInterlocutorId($conversation);

        return [
            'id' => $conversation->id,
            'interlocutor' => $this->getInterlocutorById($interlocutor_id),
            'messages' => $this->formatMessage($conversation->messages)
        ];
    }

    private function getInterlocutorId($conversation)
    {
        return ($conversation->user_one == Auth::user()->id) ? $conversation->user_two : $conversation->user_one;
    }

    private function getInterlocutorById($id) {
        $user = User::findOrFail($id);

        $formated = (object) [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'avatar' => asset('avatars') .'/'. $user->avatar,
        ];

        return $formated;
    }

    private function formatMessage($messages)
    {
        $formated = [];

        foreach ($messages as $message) {
            $formated[] = (object) [
                'id' => $message->id,
                'from_id' => $message->from_id,
                'to_id' => $message->to_id,
                'body' => $message->body,
                'type' => $message->type,
                'time' => $this->formatDate($message->created_at)
            ];

            // Mark a message as read when the user retrieves the message
            $this->markAsRead($message);
        }

        return $formated;
    }

    private function formatDate($created_at)
    {
        if ($created_at->isToday()) {
            return 'Today '.$created_at->format('H:i');
        } elseif ($created_at->isYesterday()) {
            return 'Yesterday '.$created_at->format('H:i');
        } else {
            return $created_at->format('d/m/Y H:i');
        }
    }

    private function markAsRead($message)
    {
        if ($message->to_id == Auth::user()->id) {
            $msg = Message::find($message->id);

            $msg->update([
                'is_read' => true
            ]);
        }
    }



}
