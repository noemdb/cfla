<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Mail\EmailMessage;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function contact(Request $request){
        $name = $request->name;
        $message = $request->message;

        $for = "noemdb@gmail.com";
        Mail::to($for)->send(new EmailMessage($name, $message));

        return redirect()->back();
    }
}
