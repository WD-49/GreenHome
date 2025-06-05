@extends('layouts.admin')

@section('title', 'Chi tiết sản phẩm - ' . $product->name)

@section('content')
    <div class="container product-container">
        <!-- Các phần chi tiết sản phẩm như bạn đã có -->

        <!-- Thêm phần tabs -->
        <ul class="nav nav-tabs" id="productTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button"
                    role="tab" aria-controls="details" aria-selected="true">Chi tiết</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="comments-tab" data-bs-toggle="tab" data-bs-target="#comments" type="button"
                    role="tab" aria-controls="comments" aria-selected="false">Bình luận
                    ({{ $product->comments->count() }})</button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="productTabContent">
            <!-- Tab Chi tiết -->
            <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                <!-- Nội dung chi tiết sản phẩm (giữ nguyên phần hiện tại của bạn) -->
                <div class="row">
                    <div class="col-md-6 product-image">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image" class="img-fluid">
                    </div>
                    <div class="col-md-6">
                        <h2 class="mb-3">{{ $product->name }}</h2>
                        <p class="text-muted">{{ $product->description ?? 'Sản phẩm chưa có mô tả' }}</p>
                        <div class="mb-3">
                            <span class="detail-label">Giá:</span>
                            <span class="price">{{ number_format($product->price, 0) }} đ</span>
                        </div>
                        @if ($product->promotional_price)
                            <div class="mb-3">
                                <span class="detail-label">Khuyến mãi:</span>
                                <span class="promotional-price">{{ number_format($product->promotional_price, 0) }} đ</span>
                            </div>
                        @endif
                        <div class="mb-3">
                            <span class="detail-label">Số lượng:</span> {{ $product->quantity }}
                        </div>
                        <div class="mb-3">
                            <span class="detail-label">Danh mục:</span> {{ $product->category->name ?? 'N/A' }}
                        </div>
                        <div class="mb-3">
                            <span class="detail-label">Thương hiệu:</span> {{ $product->brand->name ?? 'N/A' }}
                        </div>
                        <div class="mb-3">
                            <span class="detail-label">Ngày nhập:</span>
                            {{ $product->date_of_entry ? $product->date_of_entry->format('d/m/Y H:i') : 'N/A' }}
                        </div>
                        <div class="mb-3">
                            <span class="detail-label">Trạng thái:</span>
                            <span class="{{ $product->status ? 'status-active' : 'status-inactive' }}">
                                {{ $product->status ? 'Đang bán' : 'Dừng bán' }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <span class="detail-label">Lượt xem:</span> {{ $product->view }}
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.products.variants.index', $product) }}"
                                class="btn btn-info btn-sm">Xem biến thể</a>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Back to
                                Products</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="comments" role="tabpanel" aria-labelledby="comments-tab">
                <h4 class="mb-4">Bình luận sản phẩm</h4>

                <!-- Bộ lọc -->
<form method="GET" action="{{ route('admin.products.show', $product->id) }}#comments" class="row g-3 mb-3">
    <div class="col-md-2">
        <input type="text" name="username" class="form-control" placeholder="Tên người dùng"
            value="{{ request('username') }}">
    </div>
    <div class="col-md-2">
        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">-- Trạng thái --</option>
            <option value="chưa duyệt" {{ request('status') == 'chưa duyệt' ? 'selected' : '' }}>Chưa duyệt</option>
            <option value="hiển thị" {{ request('status') == 'hiển thị' ? 'selected' : '' }}>Hiển thị</option>
            <option value="ẩn" {{ request('status') == 'ẩn' ? 'selected' : '' }}>Ẩn</option>
        </select>
    </div>
    <div class="col-md-2 d-flex align-items-center">
        <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-filter"></i> Lọc
        </button>
    </div>
    <div class="col-md-2 d-flex align-items-center">
        <a href="{{ route('admin.products.show', $product->id) }}#comments" class="btn btn-secondary w-100">
            <i class="fas fa-sync-alt"></i> Làm mới
        </a>
    </div>
    <div class="col-md-2 text-end d-flex align-items-center justify-content-end">
        <a href="{{ route('admin.comments.trash') }}" class="btn btn-outline-danger">
            <i class="fas fa-trash"></i> Thùng rác
        </a>
    </div>
</form>


                @php
                    $filteredComments = $product
                        ->comments()
                        ->when(
                            request('username'),
                            fn($q) => $q->whereHas(
                                'user',
                                fn($qu) => $qu->where('name', 'like', '%' . request('username') . '%'),
                            ),
                        )
                        ->when(request('date'), fn($q) => $q->whereDate('created_at', request('date')))
                        ->when(request('status'), fn($q) => $q->where('status', request('status')))

                        ->get();
                @endphp

                @if ($filteredComments->isEmpty())
                    <p>Không có bình luận nào phù hợp.</p>
                @else
                    <ul class="list-group">
                        @foreach ($filteredComments as $comment)
                            <li class="list-group-item">
                                <div class="d-flex align-items-start justify-content-between">
                                    {{-- Avatar và thông tin user + nội dung --}}
                                    <div class="d-flex flex-grow-1 me-3">
                                        @if ($comment->user && $comment->user->profile && $comment->user->profile->user_image)
                                            <img src="{{ asset('storage/' . $comment->user->profile->user_image) }}"
                                                alt="avatar" class="rounded-circle me-3" width="50" height="50">
                                        @else
                                            <img src="{{ asset('images/default-avatar.png') }}" alt="avatar mặc định"
                                                class="rounded-circle me-3" width="50" height="50">
                                        @endif

                                        <div>
                                            <strong>
                                                <a href="{{ route('admin.account.detailAccUser', $comment->user_id) }}">
                                                    {{ $comment->user->name ?? 'Ẩn danh' }}
                                                </a>
                                            </strong>
                                            <span
                                                class="ms-2 text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</span>

                                            <p class="mb-1">{{ $comment->content }}</p>

                                            <span>
                                                <strong>Trạng thái:</strong>
                                                @switch($comment->status)
                                                    @case('chưa duyệt')
                                                        <span class="badge bg-warning text-dark">Chưa duyệt</span>
                                                    @break

                                                    @case('hiển thị')
                                                        <span class="badge bg-success">Hiển thị</span>
                                                    @break

                                                    @case('ẩn')
                                                        <span class="badge bg-secondary">Ẩn</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-info">{{ $comment->status }}</span>
                                                @endswitch
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Nút thao tác nằm bên phải --}}
                                    <div class="d-flex flex-column gap-2">
                                        <a href="{{ route('admin.comments.show', $comment->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>

                                        @if ($comment->status == 'chưa duyệt')
                                            <form method="POST" action="{{ route('admin.comments.approve') }}">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $comment->id }}">
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Duyệt
                                                </button>
                                            </form>
                                        @endif

                                        @if ($comment->status == 'hiển thị')
                                            <form method="POST" action="{{ route('admin.comments.hide') }}">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $comment->id }}">
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-eye-slash"></i> Ẩn
                                                </button>
                                            </form>
                                        @elseif($comment->status == 'ẩn')
                                            <form method="POST" action="{{ route('admin.comments.showAgain') }}">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $comment->id }}">
                                                <button type="submit" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Hiện
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.comments.destroy') }}">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="id" value="{{ $comment->id }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Bạn chắc chắn muốn xóa bình luận này?')">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                @endif
            </div>




            <!-- Thêm đoạn JS để Bootstrap tab hoạt động nếu chưa có -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Bắt sự kiện khi tab thay đổi
                    var triggerTabList = [].slice.call(document.querySelectorAll('button[data-bs-toggle="tab"]'))
                    triggerTabList.forEach(function(triggerEl) {
                        triggerEl.addEventListener('shown.bs.tab', function(event) {
                            // Lưu tab id (hoặc href) vào localStorage
                            localStorage.setItem('activeTab', event.target.getAttribute('data-bs-target'));
                        });
                    });

                    // Khi load trang, lấy tab đã lưu
                    var activeTab = localStorage.getItem('activeTab');
                    if (activeTab) {
                        var someTabTriggerEl = document.querySelector('button[data-bs-toggle="tab"][data-bs-target="' +
                            activeTab + '"]');
                        if (someTabTriggerEl) {
                            var tab = new bootstrap.Tab(someTabTriggerEl);
                            tab.show();
                        }
                    }
                });
            </script>


        @endsection
