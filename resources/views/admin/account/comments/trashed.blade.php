@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Thùng rác bình luận</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($trashedComments->isEmpty())
        <p>Không có bình luận nào trong thùng rác.</p>
    @else
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nội dung</th>
                    <th>Ngày tạo</th>
                    <th>Ngày xóa</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($trashedComments as $comment)
                    <tr>
                        <td>{{ $comment->id }}</td>
                        <td>{{ Str::limit($comment->content, 50) }}</td>
                        <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $comment->deleted_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <form action="{{ route('admin.account.comment.restore', $comment->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Bạn có chắc muốn khôi phục bình luận này?')">Khôi phục</button>
                            </form>

                            <form action="{{ route('admin.account.comment.forceDelete', $comment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Xóa vĩnh viễn</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div>
            {{ $trashedComments->links() }}
        </div>
    @endif

    <a href="{{ $userId ? route('admin.account.detailAccUser', $userId) : route('admin.account.listUsers') }}" class="btn btn-secondary mt-3">
        Quay lại chi tiết tài khoản
    </a>
</div>
@endsection
