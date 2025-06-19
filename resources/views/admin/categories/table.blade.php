<table class="table table-bordered mb-0">
    <thead>
        <tr>
            <th>STT</th>
            <th>Tên danh mục</th>
            <th>Slug</th>
            <th>Mô tả</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th class="text-center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($categories as $index => $category)
            <tr>
                <td>{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->index + 1 }}</td>
                <td>{{ $category->name }}</td>
                <td>{{ $category->slug }}</td>
                <td>{!! $category->description !!}</td>
                <td>
                    <span class="badge {{ $category->deleted_at ? 'bg-secondary' : 'bg-success' }}">
                        {{ $category->deleted_at ? 'Đã xóa' : 'Đang hoạt động' }}
                    </span>
                </td>
                <td>{{ $category->created_at?->format('d/m/Y') ?? '—' }}</td>
                <td class="text-center">
                    <a href="{{ route('admin.categories.edit', $category->slug) }}" class="btn btn-sm btn-warning">Sửa</a>
                    <form action="{{ route('admin.categories.destroy', $category->slug) }}" method="POST"
                          class="d-inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-danger">Không có danh mục nào.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3 d-flex justify-content-center">
    {{ $categories->appends(request()->query())->links('pagination::bootstrap-4') }}
</div>
