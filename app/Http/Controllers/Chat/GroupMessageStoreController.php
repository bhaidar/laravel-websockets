<?php

namespace App\Http\Controllers\Chat;

use App\Events\GroupMessageReceived;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\GroupMessageStoreRequest;


class GroupMessageStoreController extends Controller
{
    public function __invoke(GroupMessageStoreRequest $request)
    {
        $message = trim($request->input('message'));

        // Prepare data for group message
        $data = [
            'user_id' => auth()->id(),
            'message' => $message,
            'sender'  => auth()->user()->email,
            'time'    => now()->subSeconds(5)->diffForHumans(),
        ];

        // Broadcast group message to others only
        broadcast(new GroupMessageReceived($data))->toOthers();

        return response()->json($data, Response::HTTP_CREATED);
    }
}
