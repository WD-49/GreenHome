@extends('layouts.admin')

@section('title', 'Chi tiết danh mục - ' . $category->name)

@section('content')
<style>
    .category-wrapper {
        max-width: 1000px;
        margin: 30px auto;
    }

    .card-detail .card-body > div {
        margin-bottom: 12px;
    }

    .badge-status {
        font-size: 0.9rem;
    }

    .table-blog td, .table-blog th {
        vertical-align: middle;
    }
</style>

<div class="category-wrapper">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-white shadow-sm px-3 py-2 rounded">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.blog_categories.index') }}">Danh mục blog</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    {{-- Tiêu đề --}}
    <h3 class="mb-4 text-primary">Chi tiết danh mục: {{ $category->name }}</h3>

    {{-- Thông tin chi tiết --}}
    <div class="card mb-4 shadow-sm card-detail">
        <div class="card-header bg-light fw-bold">
            Thông tin chung
        </div>
        <div class="card-body">
            <div><strong>Tên danh mục:</strong> {{ $category->name }}</div>
            <div><strong>Mô tả:</strong> {!! $category->description ?? '<em>Không có mô tả</em>' !!}</div>
            <div><strong>Slug:</strong> {{ $category->slug }}</div>
            <div>
                <strong>Trạng thái:</strong>
                <span class="badge-status badge {{ $category->status ? 'bg-success' : 'bg-danger' }}">
                    {{ $category->status ? 'Hiển thị' : 'Ẩn' }}
                </span>
            </div>
            <div><strong>Ngày tạo:</strong> {{ $category->created_at->format('d/m/Y H:i') }}</div>
            <div><strong>Ngày cập nhật:</strong> {{ $category->updated_at->format('d/m/Y H:i') }}</div>
            <div><strong>Ngày xóa:</strong> {{ $category->deleted_at ? $category->deleted_at->format('d/m/Y H:i') : 'Chưa xóa' }}</div>
            <div><strong>Tổng bài viết:</strong> {{ $blogCount }}</div>
        </div>
    </div>

    {{-- Danh sách bài viết --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-bold">
            Bài viết trong danh mục
        </div>
        <div class="card-body">
            @if ($blogs->isEmpty())
                <p class="text-muted"><em>Không có bài viết nào trong danh mục này.</em></p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-blog">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tiêu đề</th>
                                <th>Tóm tắt</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($blogs as $index => $blog)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $blog->title }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit(strip_tags($blog->summary), 50) }}</td>
                                    <td>
                                        <span class="badge {{ $blog->status === 'published' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $blog->status === 'published' ? 'Đã đăng' : 'Nháp' }}
                                        </span>
                                    </td>
                                    <td>{{ $blog->created_at ? $blog->created_at->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Hành động --}}
    <div class="d-flex gap-2">
        <a href="{{ route('admin.blog_categories.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>

        @if ($category->deleted_at)
            <form method="POST" action="{{ route('admin.blog_categories.restore', $category->slug) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success" onclick="return confirm('Khôi phục danh mục này?')">
                    <i class="fa-solid fa-rotate-left"></i> Khôi phục
                </button>
            </form>

            <form method="POST" action="{{ route('admin.blog_categories.force_delete', $category->slug) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Xóa vĩnh viễn danh mục này?')">
                    <i class="fa-solid fa-trash"></i> Xóa vĩnh viễn
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
