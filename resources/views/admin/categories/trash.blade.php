@extends('layouts.admin')

@section('title', 'Thùng rác danh mục')

@section('content')
    <h1>Thùng rác danh mục</h1>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-primary mb-3">← Quay lại danh sách</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th>Ngày xóa</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $index => $category)
                <tr>
                    <td>{{ ($categories->currentPage() - 1) * $categories->perPage() + $index + 1 }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{!! Str::limit(strip_tags($category->description), 100) !!}</td>
                    <td>{{ $category->deleted_at->format('d/m/Y') }}</td>
                    <td>
                        <form action="{{ route('admin.categories.restore', $category->slug) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button class="btn btn-sm btn-success" type="submit">Khôi phục</button>
                        </form>
                        <form action="{{ route('admin.categories.forceDelete', $category->slug) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Xóa vĩnh viễn danh mục này?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit">Xóa vĩnh viễn</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">Không có danh mục nào trong thùng rác.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $categories->links('pagination::bootstrap-4') }}
    </div>
@endsection
