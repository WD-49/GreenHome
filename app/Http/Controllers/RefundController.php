<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\RefundTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RefundStatusNotification;

class RefundController extends Controller
{
    public function store(Request $request)
    {
        // Validate request với thông báo tiếng Việt
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'refund_reason' => 'required|string',
            'refund_image' => 'required|image|max:2048', // Giới hạn 2MB
        ], [
            'order_id.required' => 'Vui lòng cung cấp mã đơn hàng.',
            'order_id.exists' => 'Mã đơn hàng không tồn tại.',
            'refund_reason.required' => 'Vui lòng nhập lý do hoàn trả.',
            'refund_reason.string' => 'Lý do hoàn trả phải là chuỗi ký tự.',
            'refund_image.required' => 'Vui lòng tải lên ảnh minh chứng.',
            'refund_image.image' => 'Tệp tải lên phải là hình ảnh.',
            'refund_image.max' => 'Kích thước ảnh không được vượt quá 2MB.',
        ]);

        $orderId = $request->input('order_id');
        $order = Order::findOrFail($orderId);

        // Kiểm tra điều kiện với thông báo lỗi cụ thể hơn
        if ($order->order_status !== 'Đã nhận hàng') {
            return redirect()->back()->with('error', 'Đơn hàng phải có trạng thái "Đã nhận hàng" để yêu cầu hoàn trả.');
        }
        if ($order->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'Đơn hàng phải được thanh toán thành công để yêu cầu hoàn trả.');
        }
        if (now()->diffInDays($order->delivery_at ?? $order->created_at) >= 3) {
            return redirect()->back()->with('error', 'Yêu cầu hoàn trả đã hết thời hạn (3 ngày) kể từ ngày giao hàng.');
        }

        // Kiểm tra xem đã có yêu cầu refund nào cho order_id này chưa 
        $existingRefund = RefundTransaction::where('order_id', $orderId)->first();
        if ($existingRefund) {
            if ($existingRefund->refund_status === 'rejected') {
                return redirect()->back()->with('error', 'Bạn đã từng yêu cầu hoàn trả cho đơn hàng này và đã bị từ chối.');
            }
            return redirect()->back()->with('error', 'Bạn đã có yêu cầu hoàn trả đang xử lý.');
        }

        // Xử lý upload ảnh, lưu vào storage/public/images/refunds
        $refundImagePath = null;
        if ($request->hasFile('refund_image')) {
            $refundImagePath = $request->file('refund_image')->store('images/refunds', 'public');
        }

        // Lưu record vào refund_transactions
        $refund = RefundTransaction::create([
            'order_id' => $orderId,
            'refund_reason' => $request->input('refund_reason'),
            'refund_image' => $refundImagePath,
            'refund_status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Trả về thông báo thành công
        return redirect()->back()->with('success', 'Yêu cầu hoàn trả đã được gửi thành công. Vui lòng chờ admin xử lý.');
    }

    public function index(Request $request)
    {
        // Query cơ bản
        $baseQuery = RefundTransaction::query()->with('order');

        // Áp dụng bộ lọc
        if ($request->filled('order_sku')) {
            $baseQuery->whereHas('order', function ($q) use ($request) {
                $q->where('sku', 'like', '%' . $request->order_sku . '%');
            });
        }
        if ($request->filled('status')) {
            $baseQuery->where('refund_status', $request->status);
        }
        if ($request->filled('min_date')) {
            $baseQuery->whereDate('created_at', '>=', $request->min_date);
        }
        if ($request->filled('max_date')) {
            $baseQuery->whereDate('created_at', '<=', $request->max_date);
        }

        // Lấy tất cả yêu cầu
        $refunds = $baseQuery->orderByDesc('id')->paginate($request->input('per_page', 10));

        return view('admin.refunds.index', compact('refunds'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'refund_id' => 'required|exists:refund_transactions,id',
            'status' => 'required|in:pending,approved,refunded,rejected,account_invalid', // Thêm account_invalid
            'admin_note' => 'nullable|string|max:1000',
            'refund_proof_image' => 'nullable|image|max:2048',
        ]);

        $refund = RefundTransaction::findOrFail($request->refund_id);
        $newStatus = $request->status;

        // Ngăn cập nhật nếu trạng thái hiện tại là refunded
        if ($refund->refund_status === 'refunded') {
            return redirect()->back()->with('error', 'Không thể cập nhật trạng thái hoặc thông tin của yêu cầu đã hoàn tiền.');
        }

        // Xác định trạng thái hợp lệ tiếp theo
        $allowedTransitions = [
            'pending' => ['approved', 'rejected'],
            'approved' => ['rejected'], // refund_pending được cập nhật tự động, không cho admin chọn
            'refund_pending' => ['refunded', 'rejected', 'account_invalid'], // Thêm account_invalid từ refund_pending
            'rejected' => [],
            'account_invalid' => ['rejected', 'refund_pending'], // Cho phép quay lại refund_pending nếu khách cập nhật lại
            'refunded' => [],
        ];

        // Kiểm tra trạng thái mới có hợp lệ không
        if (!in_array($newStatus, $allowedTransitions[$refund->refund_status] ?? [])) {
            return redirect()->back()->with('error', 'Chuyển trạng thái không hợp lệ.');
        }

        $data = ['refund_status' => $newStatus];

        // Cập nhật trạng thái đơn hàng khi refund_status = refunded
        if ($newStatus === 'refunded') {
            $request->validate(['refund_proof_image' => 'required|image|max:2048']);
            $data['refund_proof_image'] = $request->file('refund_proof_image')->store('images/refunds', 'public');
            $data['refund_date'] = now();

            // Cập nhật order
            $refund->order->update([
                'order_status' => 'Đã hoàn hàng',
                'payment_status' => 'refunded',
            ]);
        }

        // Gán admin_note
        if ($request->filled('admin_note')) {
            $data['admin_note'] = $request->admin_note;
        } else {
            $defaultNotes = [
                'pending' => 'Yêu cầu đang chờ xử lý',
                'approved' => 'Yêu cầu hoàn hàng đã được phê duyệt',
                'refund_pending' => 'Yêu cầu hoàn tiền đang chờ xử lý',
                'refunded' => 'Hoàn tiền đã được thực hiện',
                'rejected' => 'Yêu cầu hoàn hàng bị từ chối',
                'account_invalid' => 'Tài khoản ngân hàng không hợp lệ, vui lòng cung cấp lại.',
            ];
            $data['admin_note'] = $defaultNotes[$newStatus] ?? '';
        }

        $refund->update($data);

        $user = $refund->order->user;
        if ($user) {
            Notification::send($user, new RefundStatusNotification($refund, $newStatus));
        }

        return redirect()->back()->with('success', 'Trạng thái đã được cập nhật thành công.');
    }

    public function updateBankInfo(Request $request)
    {
        $request->validate([
            'refund_id' => 'required|exists:refund_transactions,id',
            'refund_account_name' => 'required|string|max:255',
            'refund_account_number' => 'required|string|max:50',
            'refund_account_bank' => 'required|string|max:255',
            'refund_qr_image' => 'nullable|image|max:2048',
        ]);

        $refund = RefundTransaction::findOrFail($request->refund_id);

        // Kiểm tra quyền sở hữu
        if ($refund->order->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền cập nhật thông tin này.');
        }

        // Kiểm tra trạng thái
        if (!in_array($refund->refund_status, ['approved', 'account_invalid'])) {
            return redirect()->back()->with('error', 'Yêu cầu hoàn hàng không ở trạng thái cho phép cập nhật tài khoản.');
        }

        $data = [
            'refund_account_name' => $request->refund_account_name,
            'refund_account_number' => $request->refund_account_number,
            'refund_account_bank' => $request->refund_account_bank,
            'refund_status' => 'refund_pending',
        ];

        // Gán refund_cost nếu trạng thái là approved
        if ($refund->refund_status === 'approved') {
            $data['refund_cost'] = $refund->order->total_amount;
            $data['admin_note'] = 'Thông tin tài khoản đã được gửi, vui lòng chờ xử lý hoàn tiền.';
        } else {
            $data['admin_note'] = 'Đã cung cấp lại thông tin tài khoản sau khi tài khoản không hợp lệ.';
        }

        if ($request->hasFile('refund_qr_image')) {
            $data['refund_account_qr'] = $request->file('refund_qr_image')->store('images/refunds', 'public');
        }

        $refund->update($data);

        return redirect()->back()->with('success', 'Thông tin tài khoản đã được gửi thành công. Vui lòng chờ xử lý hoàn tiền.');
    }
}
