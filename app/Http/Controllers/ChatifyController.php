<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class ChatifyController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function canReceiveMessage(Request $request)
    {

        $this->validate($request,[
            'seller_id'=>'required'
        ]);

        $sellerId=getReceiverIdFromConversation($request->get('seller_id'));
        $canMessage=canReceiveMessage($sellerId);

        return Response::json([
            'can_message' => $canMessage,
        ], 200);
    }

}
