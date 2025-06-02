@extends('layouts.admin')

@section('title')
    Quản lý bình luận
@endsection

@section('content')
    <div class="row">
        <h3 class="text-center">Quản lý bình luận</h3>
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.comments.index') }}" class="row g-3">



                    <div class="col-md-3">
                        <label for="product_name" class="form-label">Tên sản phẩm</label>
                        <input type="text" name="product_name" id="product_name" class="form-control"
                            placeholder="Nhập tên sản phẩm" value="{{ request('product_name') }}">
                    </div>


                    <!-- Tên người dùng -->
                    <div class="col-md-3">
                        <label for="user_name" class="form-label">Người dùng</label>
                        <input type="text" name="user_name" id="user_name" class="form-control"
                            placeholder="Nhập tên người dùng" value="{{ request('user_name') }}">
                    </div>

                    <!-- Trạng thái -->
                    <div class="col-md-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="chưa duyệt" {{ request('status') == 'chưa duyệt' ? 'selected' : '' }}>Chưa duyệt
                            </option>
                            <option value="hiển thị" {{ request('status') == 'hiển thị' ? 'selected' : '' }}>Hiển thị
                            </option>
                            <option value="ẩn" {{ request('status') == 'ẩn' ? 'selected' : '' }}>Ẩn</option>
                        </select>
                    </div>

                    <!-- Ngày bình luận -->
                    <div class="col-md-3">
                        <label for="date" class="form-label">Ngày bình luận</label>
                        <input type="date" name="date" id="date" class="form-control"
                            value="{{ request('date') }}">
                    </div>

                    <!-- Nút -->
                    <div class="col-md-12 d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Lọc</button>
                        <a href="{{ route('admin.comments.index') }}" class="btn btn-warning"><i
                                class="fas fa-sync me-1"></i> Làm mới</a>
                        <a href="{{ route('admin.comments.trash') }}" class="btn btn-danger"><i
                                class="fas fa-trash-alt me-1"></i> Thùng rác</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4 shadow-sm">
            <div class="table-responsive py-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Người dùng</th>
                            <th>Sản phẩm</th>
                            <th>Nội dung</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($comments as $comment)
                            <tr>
                                <td>{{ $comment->id }}</td>
                                <td>{{ $comment->user->name ?? '[N/A]' }}</td>
                                <td>{{ $comment->product->name ?? '[N/A]' }}</td>
                                <td>{{ $comment->content }}</td>
                                <td>
                                    <span
                                        class="badge 
                                    {{ $comment->status == 'hiển thị' ? 'bg-success' : ($comment->status == 'ẩn' ? 'bg-secondary' : 'bg-warning') }}">
                                        {{ $comment->status }}
                                    </span>
                                </td>
                                <td>{{ $comment->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if ($comment->status == 'chưa duyệt')
                                        <form method="POST" action="{{ route('admin.comments.approve') }}"
                                            class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $comment->id }}">
                                            <button class="btn btn-success btn-sm" title="Duyệt"><i
                                                    class="fas fa-check"></i></button>
                                        </form>
                                    @endif

                                    @if ($comment->status != 'ẩn' && $comment->status != 'chưa duyệt')
                                        <form method="POST" action="{{ route('admin.comments.hide') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $comment->id }}">
                                            <button class="btn btn-secondary btn-sm" title="Ẩn"><i
                                                    class="fas fa-eye-slash"></i></button>
                                        </form>
                                    @endif

                                    @if ($comment->status === 'ẩn')
                                        <form action="{{ route('admin.comments.showAgain') }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $comment->id }}">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fa fa-eye"></i> 
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.comments.show', $comment->id) }}" class="btn btn-info btn-sm"
                                        title="Chi tiết">
                                        <i class="fas fa-info-circle"></i>
                                    </a>


                                    <form method="POST" action="{{ route('admin.comments.destroy') }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $comment->id }}">
                                        <button class="btn btn-danger btn-sm btn-confirm" title="Xoá mềm"><i
                                                class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $comments->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
