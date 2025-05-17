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



    {{-- <a href="{{ route('admin.discount.trash') }}" class="btn btn-secondary mb-3">Thùng rác</a> --}}

    {{-- <a href="{{ route('admin.discount.create') }}" class="btn btn-success mb-3">Tạo mã giảm giá </a> --}}

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Stt</th>
                <th>Title</th>
                <th>Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th> {{-- Thêm cột Action --}}
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
                    <td>{{ $discount->created_at->format('d/m/Y') }}</td>
                    <td>
                        {{-- Các nút hành động --}}
                        {{-- <a href="{{ route('admin.discounts.show', $discount->id) }}" class="btn btn-sm btn-primary">Xem</a>
                        <a href="{{ route('admin.discounts.edit', $discount->id) }}" class="btn btn-sm btn-warning">Sửa</a> --}}
                        <a href="{{ route('admin.discount.show', $discount->id) }}" class="btn btn-sm btn-primary">Xem</a>
                        <a href="{{ route('admin.discount.edit', $discount->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                        {{-- <form action="{{ route('admin.discounts.destroy', $discount->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa không?')">Xóa</button>
                        </form> --}}
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
  {{-- <div class="d-flex justify-content-center">
    {{ $discounts->links('pagination::bootstrap-4') }}
    
</div> --}}
<div class="d-flex justify-content-center">
    {{ $discounts->appends(request()->query())->links('pagination::bootstrap-4') }}
</div>



@endsection
