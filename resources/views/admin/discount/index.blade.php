@extends('layouts.admin')

@section('content')
    <h1>Mã giảm giá</h1>
 <form method="GET" action="{{ route('admin.discount.index') }}" class="mb-4 row g-3">
    <div class="col-md-3">
        <label for="type" class="form-label">Loại giảm giá</label>
        <select name="type" id="type" class="form-select">
            <option value="">-- Tất cả --</option>
            <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>Giảm theo %</option>
            <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Giảm theo tiền</option>
        </select>
    </div>

    <div class="col-md-3">
        <label for="status" class="form-label">Trạng thái</label>
        <select name="status" id="status" class="form-select">
            <option value="">-- Tất cả --</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
        </select>
    </div>

    <div class="col-md-3">
        <label for="created_from" class="form-label">Từ ngày tạo</label>
        <input type="date" name="created_from" class="form-control" value="{{ request('created_from') }}">
    </div>

    <div class="col-md-3">
        <label for="created_to" class="form-label">Đến ngày tạo</label>
        <input type="date" name="created_to" class="form-control" value="{{ request('created_to') }}">
    </div>

    <div class="col-md-12 text-end">
        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        <a href="{{ route('admin.discount.index') }}" class="btn btn-secondary">Reset</a>
    </div>
</form>

    <div class="row mb-3">
    <div class="col">
        <a href="{{ route('admin.discount.trash') }}" class="btn btn-outline-secondary w-100">🗑️ Thùng rác</a>
    </div>
    <div class="col ">
        <a href="{{ route('admin.discount.history') }}" class="btn btn-outline-secondary w-100">📜 Lịch sử dùng mã</a>

    </div>
    <div class="col text-end">
        <a href="{{ route('admin.discount.create') }}" class="btn btn-success">➕ Tạo mã giảm giá</a>
    </div>
</div>



    <table class="table table-bordered">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tiêu đề</th>
                <th>Mã code</th>
                <th>Loại giảm giá</th>
                <th>Giá trị giảm</th>
                <th>Trạng thái</th>
               <th>Ngày bắt đầu</th>
               <th>Ngày kết thúc</th>
                <th>Hoạt động</th> 
            </tr>
        </thead>
        <tbody>
            @if ($notFound)
    <tr>
        <td colspan="8" class="text-center text-danger">
            Không tìm thấy mã giảm giá nào phù hợp.
        </td>
    </tr>
@endif

            @foreach ($discounts as $index => $discount)
                <tr>
                    <td>{{ ($discounts->currentPage() - 1) * $discounts->perPage() + $index + 1 }}</td>

                    <td>{{ $discount->title }}</td>
                    <td>{{ $discount->code }}</td>
                    <td>{{ $discount->discount_type }}</td>
                    <td>{{ $discount->discount_value }}</td>
                    <td>{{ $discount->status }}</td>
                    <td>{{ $discount->start_date ? \Carbon\Carbon::parse($discount->start_date)->format('d/m/Y') : 'Không có' }}</td>
                    <td>{{ $discount->end_date ? \Carbon\Carbon::parse($discount->end_date)->format('d/m/Y') : 'Không có' }}</td>

                    <td>
                        <a href="{{ route('admin.discount.show', $discount->id) }}" class="btn btn-sm btn-primary">Xem</a>
                        <a href="{{ route('admin.discount.edit', $discount->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="{{ route('admin.discount.delete', $discount->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Bạn có muốn xóa voucher này không')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
<div class="d-flex justify-content-center">
    {{ $discounts->appends(request()->query())->links('pagination::bootstrap-4') }}
</div>



@endsection
