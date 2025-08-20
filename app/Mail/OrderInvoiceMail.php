<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $user;

    public function __construct($order, $user)
    {
        $this->order = $order;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        if (!$this->user || !isset($this->user->email)) {
            Log::warning('Không thể gửi email hóa đơn do thiếu thông tin người dùng', [
                'order_id' => $this->order->id,
                'sku' => $this->order->sku,
                'user_id' => $this->order->user_id ?? 'không có',
            ]);
            // Có thể throw exception hoặc trả về envelope mặc định với email fallback
            return new Envelope(
                subject: 'Hóa Đơn Đơn Hàng ' . $this->order->sku,
                to: [config('mail.from.address')] // Fallback email nếu cần
            );
        }

        return new Envelope(
            subject: 'Hóa Đơn Đơn Hàng ' . $this->order->sku,
            to: [$this->user->email]
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.invoice',
            with: [
                'order' => $this->order,
                'user' => $this->user,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
