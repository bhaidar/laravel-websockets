<?php

namespace App\Http\Controllers\Chat;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatIndexController extends Controller
{
    public function __invoke(Request $request)
    {
        return inertia()->render('Chat/Index');
    }
}
