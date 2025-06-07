@extends('layouts.admin')

@section('title')
    {{ $title ?? 'Thùng rác danh mục' }}
@endsection

@section('content')
<div class="row">
    <h2 class="text-center mb-4">{{ $title ?? 'Thùng rác danh mục' }}</h2>

    {{-- Tabs chuyển đổi trạng thái --}}
    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.blog_categories.index') }}">
                Tất cả ({{ $categoryAll->count() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.blog_categories.index', ['status' => 'active']) }}">
                Đang hoạt động ({{ $categoryActive->count() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('admin.blog_categories.trash') }}">
                Thùng rác ({{ $categoryTrashed->count() }})
            </a>
        </li>
    </ul>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-3">Danh mục đã xóa</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tên danh mục</th>
                            <th>Slug</th>
                            <th>Mô tả</th>
                            <th>Trạng thái</th>
                            <th>Ngày xóa</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->slug }}</td>
                                <td>{!! $category->description !!}</td>
                                <td>
                                    <span class="badge bg-danger">Đã xóa</span>
                                </td>
                                <td>{{ $category->deleted_at ? $category->deleted_at->format('d/m/Y H:i') : '' }}</td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <form action="{{ route('admin.blog_categories.restore', $category->slug) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fa-solid fa-rotate-left me-1"></i> Khôi phục
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.blog_categories.forceDelete', $category->slug) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn danh mục này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fa-solid fa-trash me-1"></i> Xóa vĩnh viễn
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Không có danh mục nào trong thùng rác.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($categories->lastPage() > 1)
                <nav class="mt-4" aria-label="Pagination">
                    <ul class="pagination justify-content-end">
                        <li class="page-item {{ $categories->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $categories->previousPageUrl() }}">«</a>
                        </li>
                        @for ($i = 1; $i <= $categories->lastPage(); $i++)
                            <li class="page-item {{ $i == $categories->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $categories->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor
                        <li class="page-item {{ !$categories->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $categories->nextPageUrl() }}">»</a>
                        </li>
                    </ul>
                </nav>
            @endif
        </div>
    </div>
</div>
@endsection
