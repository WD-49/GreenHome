@extends('layouts.admin')

@section('title', 'Chi tiết danh mục - ' . $category->name)

@section('content')
<style>
    body {
        background-color: #f8f9fa;
    }

    .category-container {
        max-width: 1000px;
        margin: 50px auto;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }

    .detail-label {
        font-weight: 600;
        color: #495057;
    }

    .status-active {
        color: #28a745;
        font-weight: bold;
    }

    .status-inactive {
        color: #dc3545;
        font-weight: bold;
    }

    .blog-table th, .blog-table td {
        vertical-align: middle;
    }
</style>

<div class="container category-container">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.blog_categories.index') }}">Danh mục blog</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    <h2 class="mb-4">Chi tiết danh mục: {{ $category->name }}</h2>

    <div class="row">
        <div class="col-md-12">
            {{-- Thông tin danh mục --}}
            <div class="mb-3"><span class="detail-label">Tên danh mục:</span> {{ $category->name }}</div>
            <div class="mb-3"><span class="detail-label">Mô tả:</span> {!! $category->description ?? '<em>Không có mô tả</em>' !!}</div>
            <div class="mb-3"><span class="detail-label">Slug:</span> {{ $category->slug }}</div>
            <div class="mb-3">
                <span class="detail-label">Trạng thái:</span>
                <span class="{{ $category->status ? 'status-active' : 'status-inactive' }}">
                    {{ $category->status ? 'Hiển thị' : 'Ẩn' }}
                </span>
            </div>
            <div class="mb-3"><span class="detail-label">Ngày tạo:</span> {{ $category->created_at->format('d/m/Y H:i') }}</div>
            <div class="mb-3"><span class="detail-label">Ngày cập nhật:</span> {{ $category->updated_at->format('d/m/Y H:i') }}</div>
            <div class="mb-3"><span class="detail-label">Ngày xóa:</span> {{ $category->deleted_at ? $category->deleted_at->format('d/m/Y H:i') : 'Chưa xóa' }}</div>
            <div class="mb-4"><span class="detail-label">Tổng số bài viết:</span> {{ $blogCount }}</div>

            {{-- Danh sách sản phẩm --}}
            <div class="mb-4">
    <h5>Bài viết thuộc danh mục</h5>
    @if ($blogs->isEmpty())
        <p><em>Không có bài viết nào trong danh mục này.</em></p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered blog-table">
                <thead class="thead-light">
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
                                @if ($blog->status === 'published')
                                    <span class="badge bg-success">Đã đăng</span>
                                @else
                                    <span class="badge bg-secondary">Nháp</span>
                                @endif
                            </td>
                            <td>{{ $blog->created_at ? $blog->created_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>


            {{-- Hành động --}}
            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('admin.blog_categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
                </a>

                @if ($category->deleted_at)
                    <form method="POST" action="{{ route('admin.blog_categories.restore', $category->slug) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success" onclick="return confirm('Khôi phục danh mục này?')">
                            Khôi phục
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.blog_categories.force_delete', $category->slug) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Xóa vĩnh viễn danh mục này?')">
                            Xóa vĩnh viễn
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
