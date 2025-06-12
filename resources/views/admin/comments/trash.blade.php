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

    <table class="table table-bordered align-middle">
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
                    <td class="text-center">
    <div class="dropdown">
        <button class="btn btn-link text-dark p-0" type="button" id="dropdownTrash{{ $comment->id }}" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical fs-5"></i>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownTrash{{ $comment->id }}">
            <!-- Phục hồi -->
            <li>
                <form action="{{ route('admin.comments.restore', $comment->id) }}" method="POST" onsubmit="return confirm('Khôi phục bình luận này?');">
                    @csrf
                    <button type="submit" class="dropdown-item text-success">
                        <i class="ti ti-history me-1"></i> Phục hồi
                    </button>
                </form>
            </li>

            <!-- Xóa vĩnh viễn -->
            <li>
                <form action="{{ route('admin.comments.forceDelete') }}" method="POST" onsubmit="return confirm('Xóa vĩnh viễn bình luận này?');">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $comment->id }}">
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="ti ti-trash-x me-1"></i> Xóa vĩnh viễn
                    </button>
                </form>
            </li>
        </ul>
    </div>
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

@section('scripts')
    {{-- Nạp Bootstrap JS để dropdown hoạt động --}}
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
@endsection
