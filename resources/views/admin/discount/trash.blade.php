@extends('layouts.admin')

@section('content')
    <h1>Thùng rác</h1>
    <a href="{{ route('admin.discount.index') }}" class="btn btn-primary mb-3">← Quay lại danh sách</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tiêu đề</th>
                <th>Mã</th>
                <th>Loại</th>
                <th>Giá trị</th>
                <th>Ngày xóa</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($discounts as $index => $discount)
                <tr>
                    <td>{{ ($discounts->currentPage() - 1) * $discounts->perPage() + $index + 1 }}</td>
                    <td>{{ $discount->title }}</td>
                    <td>{{ $discount->code }}</td>
                    <td>{{ $discount->discount_type }}</td>
                    <td>{{ $discount->discount_value }}</td>
                    <td>{{ $discount->deleted_at->format('d/m/Y') }}</td>
                    <td>

                        <form action="{{ route('admin.discount.restore', $discount->id) }}" method="POST" style="display:inline-block;">
                            
                            @csrf
                            <button class="btn btn-sm btn-success" type="submit">Khôi phục</button>
                        </form>
                        <form action="{{ route('admin.discount.forceDelete', $discount->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit" onclick="return confirm('Xóa vĩnh viễn?')">Xóa vĩnh viễn</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">Không có bản ghi nào trong thùng rác.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $discounts->links('pagination::bootstrap-4') }}
    </div>
@endsection
