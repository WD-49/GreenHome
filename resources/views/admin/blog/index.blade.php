@extends('layouts.admin')

@section('content')
    <h2 class="text-center">Danh sách bài viết</h2>

    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <a class="nav-link active" href="">
                Đang hoạt động
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="">
                Thùng rác
            </a>
        </li>
    </ul>

    <div class="mt-4 bg-white shadow-sm rounded p-3">
        <div class="d-md-flex align-items-center">
            <div>
                <h4 class="card-title text-dark">Danh sách bài viết</h4>
                <p class="card-subtitle">Quản lý các bài viết trên blog</p>
            </div>
            <div class="ms-auto mt-3 mt-md-0">
                <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-1"></i> Thêm mới
                </a>
            </div>
        </div>

        @if ($blogs->isEmpty())
            <div>
                <p class="text-center text-muted">Chưa có bài viết nào.</p>
            </div>
        @else
            <table class="table table-bordered mt-4">
                <thead class="thead-dark">
                    <tr>
                        <th>STT</th>
                        <th>Tiêu đề</th>
                        <th>Slug</th>
                        <th>Trạng thái</th>
                        <th>Thể loại</th>
                        <th>Thumbnail</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($blogs as $key => $blog)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $blog->title }}</td>
                            <td>{{ $blog->slug }}</td>
                            <td>
                                @if ($blog->status == 1)
                                    <span class="badge bg-success">Hiển thị</span>
                                @else
                                    <span class="badge bg-secondary">Ẩn</span>
                                @endif
                            </td>
                            <td>{{ $blog->category->name ?? 'Chưa phân loại' }}</td>
                            <td>
                                @if ($blog->thumbnail)
                                    <img src="{{ asset('storage/' . $blog->thumbnail) }}" alt="thumbnail" width="60">
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </td>
                            <td>{{ $blog->created_at->format('d/m/Y') }}</td>
                            <td class="d-flex">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm me-2" type="button" id="dropdownMenuButton"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v mx-auto"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.blogs.show', $id = $blog->id) }}">
                                                Chi tiết
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.blogs.edit', $id = $blog->id) }}">
                                                Chỉnh sửa
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.blogs.destroy') }}" method="POST"
                                                onsubmit="return confirm('Bạn có chắc chắn xóa vĩnh viễn bài viết này?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $blog->id }}">
                                                <button class="dropdown-item text-danger" type="submit">
                                                    Xóa bài viết
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
