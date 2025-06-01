@extends('layouts.admin')

@section('title', $title ?? 'Chi tiết bình luận')

@section('content')
    <div class="container my-4">
        <h3 class="mb-4 text-center">{{ $title ?? 'Chi tiết bình luận' }}</h3>

        <div class="card shadow-sm">
            <div class="card-body">
                <p><strong>ID:</strong> {{ $comment->id }}</p>
                <p><strong>Sản phẩm:</strong> {{ $comment->product->name ?? 'Không rõ' }}</p>
                <p><strong>Người dùng:</strong> {{ $comment->user->name ?? 'Ẩn danh' }}</p>
                <p><strong>Nội dung:</strong></p>
                <div class="border p-3 bg-light rounded">
                    {{ $comment->content }}
                </div>
                <p class="mt-3"><strong>Trạng thái:</strong> 
                    <span class="badge {{ $comment->status == 'hiển thị' ? 'bg-success' : ($comment->status == 'ẩn' ? 'bg-secondary' : 'bg-warning') }}">
                        {{ $comment->status }}
                    </span>
                </p>
                <p><strong>Ngày tạo:</strong> {{ $comment->created_at }}</p>

                <a href="{{ route('admin.comments.index') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
@endsection
