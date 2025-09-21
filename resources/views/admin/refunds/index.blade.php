@extends('layouts.admin')

@section('title', 'Quản lý hoàn hàng & hoàn tiền')

@section('content')
    <div class="py-3 d-flex align-items-center flex-sm-row flex-column mb-3">
        <div class="flex-grow-1 d-flex align-items-center gap-2">
            <i class="mdi mdi-format-list-bulleted-type fs-3 text-primary"></i>
            <h4 class="fs-20 fw-bold m-0">Quản lý hoàn hàng & hoàn tiền</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Danh sách yêu cầu</h5>
                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                        <i class="mdi mdi-filter-outline me-1"></i> Bộ lọc
                    </button>
                </div>

                <div class="card-body">
                    <!-- Bộ lọc -->
                    <div class="collapse mb-3" id="filterCollapse">
                        <div class="card card-body">
                            <form method="GET" action="{{ route('admin.refunds.index') }}">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Mã đơn hàng</label>
                                        <input type="text" name="order_sku" class="form-control"
                                            value="{{ request('order_sku') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Trạng thái</label>
                                        <select name="status" class="form-select">
                                            <option value="">Tất cả</option>
                                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                                Chờ xử lý</option>
                                            <option value="approved"
                                                {{ request('status') === 'approved' ? 'selected' : '' }}>
                                                Đã phê duyệt</option>
                                            <option value="refund_pending"
                                                {{ request('status') === 'refund_pending' ? 'selected' : '' }}>
                                                Chờ hoàn tiền</option>
                                            <option value="refunded"
                                                {{ request('status') === 'refunded' ? 'selected' : '' }}>
                                                Đã hoàn tiền</option>
                                            <option value="rejected"
                                                {{ request('status') === 'rejected' ? 'selected' : '' }}>
                                                Bị từ chối</option>
                                            <option value="account_invalid"
                                                {{ request('status') === 'account_invalid' ? 'selected' : '' }}>
                                                Tài khoản không hợp lệ</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Ngày từ</label>
                                        <input type="date" name="min_date" class="form-control"
                                            value="{{ request('min_date') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Ngày đến</label>
                                        <input type="date" name="max_date" class="form-control"
                                            value="{{ request('max_date') }}">
                                    </div>
                                    <div class="col-md-12 text-end">
                                        <button type="submit" class="btn btn-outline-primary">Lọc</button>
                                        <a href="{{ route('admin.refunds.index') }}"
                                            class="btn btn-outline-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Bảng yêu cầu -->
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Đơn hàng</th>
                                    <th class="text-center">Lý do</th>
                                    <th class="text-center">Ảnh minh chứng</th>
                                    <th class="text-center">Trạng thái</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($refunds as $index => $refund)
                                    @php
                                        $statusMap = [
                                            'pending' => ['Chờ xử lý', 'bg-warning', 'Yêu cầu đang chờ xử lý'],
                                            'approved' => [
                                                'Phê duyệt',
                                                'bg-success',
                                                'Yêu cầu hoàn hàng đã được phê duyệt, vui lòng cung cấp thông tin tài khoản để chúng tôi tiến hành hoàn tiền',
                                            ],
                                            'refund_pending' => [
                                                'Chờ hoàn tiền',
                                                'bg-warning',
                                                'Yêu cầu hoàn tiền đang chờ xử lý',
                                            ],
                                            'refunded' => [
                                                'Đã hoàn tiền',
                                                'bg-success',
                                                'Hoàn tiền đã hoàn tất, vui lòng kiểm tra tài khoản của bạn',
                                            ],
                                            'rejected' => [
                                                'Bị từ chối',
                                                'bg-danger',
                                                'Yêu cầu hoàn hàng bị từ chối vì không đủ điều kiện',
                                            ],
                                            'account_invalid' => [
                                                'Tài khoản không hợp lệ',
                                                'bg-danger',
                                                'Tài khoản ngân hàng không hợp lệ, vui lòng cung cấp lại.',
                                            ],
                                        ];
                                        [$displayStatus, $statusClass, $defaultNote] = $statusMap[
                                            $refund->refund_status
                                        ] ?? [$refund->refund_status, 'bg-secondary', ''];

                                        // Định nghĩa trạng thái hợp lệ tiếp theo
                                        $allowedTransitions = [
                                            'pending' => ['approved', 'rejected'],
                                            'approved' => ['rejected'],
                                            'refund_pending' => ['refunded', 'rejected', 'account_invalid'], // Thêm account_invalid từ refund_pending
                                            'rejected' => [],
                                            'account_invalid' => ['rejected'], // Cho phép quay lại refund_pending nếu khách cập nhật lại, nhưng admin không chọn refund_pending
                                            'refunded' => [],
                                        ];
                                        $allowedStatuses = $allowedTransitions[$refund->refund_status] ?? [];
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center">{{ $refund->order->sku }}</td>
                                        <td class="text-center">{{ Str::limit($refund->refund_reason, 50) }}</td>
                                        <td class="text-center">
                                            @if ($refund->refund_image)
                                                <img src="{{ asset('storage/' . $refund->refund_image) }}" alt="Minh chứng"
                                                    style="width: 50px; height: auto; border: 1px solid #ddd; border-radius: 5px;">
                                            @else
                                                Không có ảnh
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $statusClass }}">{{ $displayStatus }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#refundDetailModal{{ $refund->id }}">
                                                Xem chi tiết
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal Chi tiết -->
                                    <div class="modal fade" id="refundDetailModal{{ $refund->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Chi tiết yêu cầu: {{ $refund->order->sku }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <!-- Thông tin hoàn hàng -->
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold mb-3">Thông tin hoàn hàng</h6>
                                                            <p><strong>Mã đơn hàng:</strong> {{ $refund->order->sku }}</p>
                                                            <p><strong>Lý do:</strong> {{ $refund->refund_reason }}</p>
                                                            <p><strong>Ảnh minh chứng:</strong><br>
                                                                @if ($refund->refund_image)
                                                                    <img src="{{ asset('storage/' . $refund->refund_image) }}"
                                                                        alt="Minh chứng"
                                                                        style="width: 150px; height: auto; border: 1px solid #ddd; border-radius: 5px;">
                                                                @else
                                                                    Không có ảnh
                                                                @endif
                                                            </p>
                                                            <p><strong>Ngày tạo:</strong>
                                                                {{ $refund->created_at->format('d/m/Y H:i') }}</p>
                                                            <p><strong>Trạng thái:</strong> <span
                                                                    class="badge {{ $statusClass }}">{{ $displayStatus }}</span>
                                                            </p>
                                                            <p><strong>Ghi chú admin:</strong>
                                                                {{ $refund->admin_note ?? $defaultNote }}</p>
                                                        </div>
                                                        <!-- Thông tin hoàn tiền -->
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold mb-3">Thông tin hoàn tiền</h6>
                                                            <p><strong>Số tiền:</strong>
                                                                {{ number_format($refund->refund_cost ?? 0, 0, ',', '.') }}
                                                                đ</p>
                                                            <p><strong>Tài khoản ngân hàng:</strong><br>
                                                                {{ $refund->refund_account_name ?? 'N/A' }} -
                                                                {{ $refund->refund_account_bank ?? 'N/A' }}<br>
                                                                Số TK: {{ $refund->refund_account_number ?? 'N/A' }}
                                                            </p>
                                                            <p><strong>Ảnh QR Code:</strong><br>
                                                                @if ($refund->refund_account_qr)
                                                                    <img src="{{ asset('storage/' . $refund->refund_account_qr) }}"
                                                                        alt="QR Code"
                                                                        style="width: 150px; height: auto; border: 1px solid #ddd; border-radius: 5px;">
                                                                @else
                                                                    Không có ảnh
                                                                @endif
                                                            </p>
                                                            <p><strong>Ngày yêu cầu hoàn tiền:</strong>
                                                                {{ $refund->updated_at->format('d/m/Y H:i') }}</p>
                                                            <p><strong>Ảnh minh chứng chuyển khoản:</strong><br>
                                                                @if ($refund->refund_proof_image)
                                                                    <img src="{{ asset('storage/' . $refund->refund_proof_image) }}"
                                                                        alt="Minh chứng chuyển khoản"
                                                                        style="width: 150px; height: auto; border: 1px solid #ddd; border-radius: 5px;">
                                                                @else
                                                                    Không có ảnh
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <!-- Form cập nhật trạng thái -->
                                                    <hr>
                                                    <h6 class="fw-bold mb-3">Cập nhật trạng thái</h6>
                                                    <form action="{{ route('admin.refunds.update-status') }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="refund_id"
                                                            value="{{ $refund->id }}">
                                                        <div class="mb-3">
                                                            <label class="form-label">Trạng thái</label>
                                                            <select name="status" class="form-select" required
                                                                onchange="toggleProofImage(this, 'proofImage_{{ $refund->id }}'); updateAdminNote(this, {{ json_encode($statusMap) }}, 'admin_note_{{ $refund->id }}')"
                                                                {{ $refund->refund_status === 'refunded' ? 'disabled' : '' }}>
                                                                <option value="{{ $refund->refund_status }}" selected>
                                                                    {{ $displayStatus }}</option>
                                                                @foreach ($allowedStatuses as $status)
                                                                    <option value="{{ $status }}">
                                                                        {{ $statusMap[$status][0] }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3" id="proofImage_{{ $refund->id }}"
                                                            style="display: {{ $refund->refund_status === 'refunded' ? 'block' : 'none' }}">
                                                            <label class="form-label">Ảnh minh chứng chuyển khoản (bắt buộc
                                                                khi hoàn tiền)</label>
                                                            <input type="file" name="refund_proof_image"
                                                                class="form-control" accept="image/*"
                                                                onchange="previewProofImage(this, 'proofImagePreview_{{ $refund->id }}')"
                                                                {{ $refund->refund_status === 'refunded' ? 'disabled' : '' }}>
                                                            <div id="proofImagePreview_{{ $refund->id }}"
                                                                class="mt-2">
                                                                @if ($refund->refund_status === 'refunded' && $refund->refund_proof_image)
                                                                    <img src="{{ asset('storage/' . $refund->refund_proof_image) }}"
                                                                        alt="Minh chứng chuyển khoản"
                                                                        style="width: 150px; height: auto; border: 1px solid #ddd; border-radius: 5px;">
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Ghi chú admin</label>
                                                            <textarea name="admin_note" id="admin_note_{{ $refund->id }}" class="form-control" placeholder="Ghi chú"
                                                                {{ $refund->refund_status === 'refunded' ? 'disabled' : '' }}>{{ $refund->admin_note ?? $defaultNote }}</textarea>
                                                        </div>
                                                        <button type="submit" class="btn btn-success"
                                                            {{ $refund->refund_status === 'refunded' ? 'disabled' : '' }}>Cập
                                                            nhật</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Không có yêu cầu nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $refunds->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/app.js')

    <script>
        function updateAdminNote(selectElement, statusMap, textareaId) {
            const selectedStatus = selectElement.value;
            const note = statusMap[selectedStatus] ? statusMap[selectedStatus][2] : '';
            const textarea = document.getElementById(textareaId);
            if (!textarea.disabled) {
                textarea.value = note;
            }
        }

        function toggleProofImage(selectElement, proofImageId) {
            const proofImageDiv = document.getElementById(proofImageId);
            const input = proofImageDiv.querySelector('input[name="refund_proof_image"]');
            if (!input.disabled) {
                proofImageDiv.style.display = selectElement.value === 'refunded' ? 'block' : 'none';
                input.required = selectElement.value === 'refunded';
            }
        }

        function previewProofImage(input, previewId) {
            if (input.disabled) return;
            const container = document.getElementById(previewId);
            container.innerHTML = '';
            if (input.files.length > 1) {
                alert('Bạn chỉ được tải tối đa 1 ảnh.');
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '150px';
                img.style.height = 'auto';
                img.style.border = '1px solid #ddd';
                img.style.borderRadius = '5px';
                container.appendChild(img);
            };
            if (input.files[0]) reader.readAsDataURL(input.files[0]);
        }
    </script>
@endsection
