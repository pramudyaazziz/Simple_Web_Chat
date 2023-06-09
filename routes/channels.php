<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('message.{conversation}', function ($user,Conversation $conversation) {
    $conversationMember = [$conversation->user_one, $conversation->user_two];
    return in_array($user->id, $conversationMember);
});

Broadcast::channel('conversation', function ($user) {
    return (object) [
        'id' => $user->id,
        'name' => $user->name,
        'username' => $user->username
    ];
});
