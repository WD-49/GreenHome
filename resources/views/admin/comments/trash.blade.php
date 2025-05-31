@extends('layouts.admin')

@section('title', 'Thùng rác Bình luận')

@section('content')
    <div class="container-fluid">
        <h2><i class="ti ti-trash"></i> Thùng rác Bình luận</h2>
        <a href="{{ route('admin.comments.index') }}" class="btn btn-primary mb-3">
            <i class="ti ti-arrow-left"></i> Quay lại danh sách
        </a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nội dung</th>
                    <th>Người viết</th>
                    <th>Ngày xóa</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comments as $comment)
                    <tr>
                        <td>{{ $comment->id }}</td>
                        <td>{{ $comment->content }}</td>
                        <td>{{ $comment->user->name ?? 'Khách' }}</td>
                        <td>{{ $comment->deleted_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <form action="{{ route('admin.comments.restore', $comment->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" title="Phục hồi">
                                    <i class="ti ti-reload"></i> Phục hồi
                                </button>
                            </form>


                            <!-- Xóa vĩnh viễn -->
                            <form action="{{ route('admin.comments.forceDelete') }}" method="POST"
                                style="display:inline-block;"
                                onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn bình luận này?');">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="id" value="{{ $comment->id }}">
                                <button type="submit" class="btn btn-danger btn-sm" title="Xóa vĩnh viễn">
                                    <i class="ti ti-trash-off"></i> Xóa vĩnh viễn
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Thùng rác trống</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $comments->links() }}
    </div>
@endsection
