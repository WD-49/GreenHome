@extends('layouts.admin')

@section('title', $title ?? 'Quản lý bình luận')

@section('content')
    <div class="container">
        <h3 class="text-center my-4">{{ $title ?? 'Quản lý bình luận' }}</h3>

        <!-- Form lọc -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.comments.index') }}" class="row g-3">
                    <!-- Tên sản phẩm -->
                    <div class="col-md-3">
                        <label class="form-label">Tên sản phẩm</label>
                        <input type="text" name="product_name" class="form-control" value="{{ request('product_name') }}"
                            placeholder="Nhập tên sản phẩm">
                    </div>

                    <!-- Tên người dùng -->
                    <div class="col-md-3">
                        <label class="form-label">Tên người dùng</label>
                        <input type="text" name="user_name" class="form-control" value="{{ request('user_name') }}"
                            placeholder="Nhập tên người dùng">
                    </div>

                    <!-- Trạng thái -->
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="">-- Tất cả --</option>
                            <option value="chưa duyệt" {{ request('status') == 'chưa duyệt' ? 'selected' : '' }}>Chưa duyệt
                            </option>
                            <option value="hiển thị" {{ request('status') == 'hiển thị' ? 'selected' : '' }}>Hiển thị
                            </option>
                            <option value="ẩn" {{ request('status') == 'ẩn' ? 'selected' : '' }}>Ẩn</option>
                        </select>
                    </div>

                    <!-- Ngày comment -->
                    <div class="col-md-3">
                        <label class="form-label">Ngày comment</label>
                        <input type="date" name="comment_date" class="form-control"
                            value="{{ request('comment_date') }}">
                    </div>

                    <!-- Nút -->
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Tìm kiếm
                        </button>
                        <a href="{{ route('admin.comments.index') }}" class="btn btn-warning">
                            <i class="fas fa-sync me-1"></i> Làm mới
                        </a>
                        <a href="{{ route('admin.comments.trash') }}" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Thùng rác
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bảng dữ liệu -->
        <div class="card shadow-sm">
            <div class="table-responsive p-3">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Sản phẩm</th>
                            <th>Người dùng</th>
                            <th>Nội dung</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comments as $comment)
                            <tr id="comment-row-{{ $comment->id }}">
                                <td>{{ $comment->id }}</td>
                                <td>{{ $comment->product->name ?? 'Không rõ' }}</td>
                                <td>{{ $comment->user->name ?? 'Ẩn danh' }}</td>
                                <td>{{ $comment->content }}</td>
                                <td>
                                    <span
                                        class="badge {{ $comment->status == 'hiển thị' ? 'bg-success' : ($comment->status == 'ẩn' ? 'bg-secondary' : 'bg-warning') }}">
                                        {{ $comment->status }}
                                    </span>
                                </td>
                                <td>{{ $comment->created_at }}</td>
                                <td class="d-flex justify-content-center gap-1">
                                    @if ($comment->status !== 'hiển thị')
                                        <button class="btn btn-success btn-sm btn-approve" data-id="{{ $comment->id }}"
                                            title="Duyệt">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif

                                    @if ($comment->status === 'hiển thị')
                                        <button class="btn btn-warning btn-sm btn-hide" data-id="{{ $comment->id }}"
                                            title="Ẩn">
                                            <i class="fas fa-eye-slash"></i>
                                        </button>
                                    @endif

                                    <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $comment->id }}"
                                        title="Xóa"
                                        data-confirm-message="Bạn có chắc chắn muốn bỏ bình luận này vào thùng rác không?">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">Không có bình luận nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $comments->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            // Duyệt
            $('.btn-approve').click(function() {
                let id = $(this).data('id');
                $.post('{{ route('admin.comments.approve') }}', {
                    _token: '{{ csrf_token() }}',
                    id
                }, function() {
                    location.reload();
                });
            });

            // Ẩn
            $('.btn-hide').click(function() {
                let id = $(this).data('id');
                $.post('{{ route('admin.comments.hide') }}', {
                    _token: '{{ csrf_token() }}',
                    id
                }, function() {
                    location.reload();
                });
            });

            // Xóa mềm có xác nhận
            $('.btn-delete').click(function() {
                let message = $(this).data('confirm-message') || 'Bạn có chắc chắn muốn xóa?';
                if (!confirm(message)) return;

                let id = $(this).data('id');
                $.ajax({
                    url: '{{ route('admin.comments.destroy') }}',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id
                    },
                    success: function() {
                        $('#comment-row-' + id).remove();
                    }
                });
            });
        });
    </script>
@endpush
