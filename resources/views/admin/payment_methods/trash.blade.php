@extends('layouts.admin')

@section('title', 'Thùng rác phương thức thanh toán')

@section('content')
    <h1>Thùng rác phương thức thanh toán</h1>
    <a href="{{ route('admin.paymentMethods.index') }}" class="btn btn-primary mb-3">← Quay lại danh sách</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center">STT</th>
                <th class="text-center">Tên phương thức</th>
                <th class="text-center">Mô tả</th>
                <th class="text-center">Trạng thái</th>
                <th class="text-center">Ngày xóa</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($paymentMethods as $index => $method)
                <tr>
                    <td class="text-center">{{ ($paymentMethods->currentPage() - 1) * $paymentMethods->perPage() + $index + 1 }}</td>
                    <td class="text-center">{{ $method->name }}</td>
                    <td class="text-center">{!! Str::limit(strip_tags($method->description), 100) !!}</td>
                    <td class="text-center">
                        <span class="badge {{ $method->status ? 'bg-success' : 'bg-secondary' }}">
                            {{ $method->status ? 'Kích hoạt' : 'Tạm tắt' }}
                        </span>
                    </td>
                    <td class="text-center">{{ $method->deleted_at->format('d/m/Y') }}</td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-light border dropdown-toggle" type="button" id="dropdownMenuButton{{ $index }}" data-bs-toggle="dropdown" aria-expanded="false">
                                ...
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $index }}">
                                <li>
                                    <form action="{{ route('admin.paymentMethods.restore', $method->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Khôi phục</button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('admin.paymentMethods.forceDelete', $method->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phương thức  này vĩnh viễn không?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">Xóa vĩnh viễn</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
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
