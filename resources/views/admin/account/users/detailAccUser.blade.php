@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h2>Chi tiết tài khoản</h2>

        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                Thông tin người dùng
            </div>
            <div class="card-body">
                <p><strong>Tên:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Role:</strong> {{ $user->role }}</p>
                <p><strong>Trạng thái:</strong> {{ $user->status == 1 ? 'Hoạt động' : 'Ngừng hoạt động' }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-success text-white">
                Thông tin hồ sơ
            </div>

            @if ($user->profile)
                <div class="card-body">
                    <p><strong>Số điện thoại:</strong> {{ $user->profile->phone }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $user->profile->address }}</p>
                    <p><strong>Giới tính:</strong>
                        {{ $user->profile->gender == 'nam' ? 'Nam' : ($user->profile->gender == 'nu' ? 'Nữ' : 'Khác') }}</p>

                    @if ($user->profile->user_image)
                        <p><strong>Ảnh đại diện:</strong></p>
                        <img src="{{ asset('storage/' . $user->profile->user_image) }}" alt="Ảnh đại diện"
                            class="img-thumbnail" style="max-width: 200px;">
                    @endif
                </div>
            @else
                <div class="card-body text-danger">
                    <p>Không có thông tin hồ sơ người dùng.</p>
                </div>
            @endif
        </div>
        <h3 class="mt-4">Danh sách bình luận của người dùng</h3>
        <div class="mb-3">
            <a href="{{ route('admin.account.comment.trashed') }}" class="btn btn-danger">
                <i class="fa fa-trash"></i> Thùng rác bình luận
            </a>
        </div>



        @if ($user->comments->isEmpty())
            <p>Người dùng này chưa có bình luận nào.</p>
        @else
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nội dung</th>
                        <th>Ngày tạo</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($user->comments()->withTrashed()->get() as $comment)
                        <tr>
                            <td>{{ $comment->id }}</td>
                            <td>{{ $comment->content }}</td>
                            <td>{{ $comment->created_at }}</td>
                            <td>
                                @if ($comment->deleted_at)
                                    <span class="badge bg-danger">Đã ẩn</span>
                                @elseif ($comment->status == 1)
                                    <span class="badge bg-success">Hiển thị</span>
                                @else
                                    <span class="badge bg-warning text-dark">Bị ẩn</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.account.comment.toggleStatus', $comment->id) }}"
                                        method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-info">Chuyển trạng thái</button>
                                    </form>

                                    @if (!$comment->deleted_at)
                                        <form action="{{ route('admin.account.comment.softDelete', $comment->id) }}"
                                            method="POST" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning">Xóa </button>
                                        </form>
                                    @else
                                        <!-- Bỏ nút Xóa vĩnh viễn trong bảng này -->
                                        <span class="text-muted fst-italic">Đã ẩn</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif


        <a href="{{ route('admin.account.listUsers') }}" class="btn btn-secondary mt-3">Quay lại danh sách</a>
    </div>

@endsection
