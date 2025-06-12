@extends('layouts.admin')

@section('title', 'Thùng rác phương thức thanh toán')

@section('content')
    <h1>Thùng rác phương thức thanh toán</h1>
    <a href="{{ route('admin.paymentMethods.index') }}" class="btn btn-primary mb-3">← Quay lại danh sách</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên phương thức</th>
                <th>Mô tả</th>
                <th>Trạng thái</th>
                <th>Ngày xóa</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($paymentMethods as $index => $method)
                <tr>
                    <td>{{ ($paymentMethods->currentPage() - 1) * $paymentMethods->perPage() + $index + 1 }}</td>
                    <td>{{ $method->name }}</td>
                    <td>{!! Str::limit(strip_tags($method->description), 100) !!}</td>
                    <td>
                        <span class="badge {{ $method->status ? 'bg-success' : 'bg-secondary' }}">
                            {{ $method->status ? 'Kích hoạt' : 'Tạm tắt' }}
                        </span>
                    </td>
                    <td>{{ $method->deleted_at->format('d/m/Y') }}</td>
                    <td>
                        <form action="{{ route('admin.paymentMethods.restore', $method->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Khôi phục</button>
                        </form>
                        <form action="{{ route('admin.paymentMethods.forceDelete', $method->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Xóa vĩnh viễn phương thức này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Xóa vĩnh viễn</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">Không có phương thức nào trong thùng rác.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $paymentMethods->links('pagination::bootstrap-4') }}
    </div>
@endsection
