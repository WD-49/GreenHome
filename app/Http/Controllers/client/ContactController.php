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
    ], [
        'name.required'    => 'Vui lòng nhập họ tên.',
        'name.string'      => 'Họ tên phải là chuỗi ký tự.',
        'name.max'         => 'Họ tên không được vượt quá 255 ký tự.',

        'email.required'   => 'Vui lòng nhập email.',
        'email.email'      => 'Email không hợp lệ.',

        'phone.string'     => 'Số điện thoại phải là chuỗi ký tự.',
        'phone.max'        => 'Số điện thoại không được vượt quá 20 ký tự.',

        'message.required' => 'Vui lòng nhập nội dung tin nhắn.',
        'message.string'   => 'Nội dung tin nhắn phải là chuỗi ký tự.',
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

