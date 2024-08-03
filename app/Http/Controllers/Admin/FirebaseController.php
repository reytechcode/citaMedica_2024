<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    public function sendAll(Request $request) {
        $recipients = User::whereNotNull('device_token')
            ->pluck('device_token')->toArray();

        fcm()
        ->to($recipients) // $recipients must an array
        ->priority('high')
        ->timeToLive(0)
        ->notification([
            'title' => $request->input('title'),
            'body' => $request->input('body'),
        ])
        ->send();

        $notification = 'La notificación se envió a todos los pacientes con exito.';
        return back()->with(compact('notification'));
    }
}
