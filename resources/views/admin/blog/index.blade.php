@extends('layouts.admin')

@section('title')
    Danh sách bài viết
@endsection

@section('content')
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-center flex-sm-row flex-column mb-3">
            <div class="flex-grow-1 d-flex align-items-center gap-2">
                <i class="mdi mdi-post-outline fs-3 text-primary"></i>
                <h4 class="fs-20 fw-bold m-0">Quản lý bài viết</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách bài viết</h5>
                        <div>
                            <a href="{{ route('admin.blogs.create') }}" class="btn btn-success shadow-sm">
                                + Tạo bài viết
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($blogs->isEmpty())
                            <p class="text-center text-muted">Chưa có bài viết nào.</p>
                        @else
                            <table class="table table-bordered mt-3">
                                <thead class="table">
                                    <tr>
                                        <th>#</th>
                                        <th>Tiêu đề</th>
                                        <th>Trạng thái</th>
                                        <th>Thể loại</th>
                                        <th>Ngày tạo</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($blogs as $key => $blog)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $blog->title }}</td>
                                            <td>
                                                @if ($blog->status == 1)
                                                    <span class="badge bg-success">Đang hiển thị</span>
                                                @else
                                                    <span class="badge bg-secondary">Bản nháp</span>
                                                @endif
                                            </td>
                                            <td>{{ $blog->category->name ?? 'Chưa phân loại' }}</td>
                                            <td>{{ $blog->created_at->format('d/m/Y') }}</td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.blogs.show', $blog->id) }}">
                                                                Chi tiết
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.blogs.edit', $blog->id) }}">
                                                                Chỉnh sửa
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('admin.blogs.destroy') }}" method="POST"
                                                                onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn?')">
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
                            <div class="d-flex justify-content-end mt-3">
                                {{ $blogs->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
