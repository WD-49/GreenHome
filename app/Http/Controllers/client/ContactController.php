<?php

namespace App\Http\Controllers\client;
use App\Http\Controllers\Controller;
use App\Models\WebInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
class ContactController extends Controller
{
    public function index()
    {
        $webInfo = WebInfo::pluck('value', 'key')->toArray();
        return view('client.pages.contact', compact('webInfo'));
    }

    public function sendMail(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        $webInfo = WebInfo::pluck('value', 'key')->toArray();
        $toEmail = $webInfo['email'] ?? config('mail.from.address');

        Mail::send('emails.contact', [
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'body'    => $request->message,
        ], function ($message) use ($request, $toEmail) {
            $message->to($toEmail)
                    ->subject('Liên hệ mới từ ' . $request->name);
        });

        return back()->with('success', 'Tin nhắn của bạn đã được gửi thành công!');
    }
}

