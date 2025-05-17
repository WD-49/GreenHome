@extends('layouts.admin')

@section('content')
    {{-- <h1>{{ $title }}</h1> --}}
    <a href="{{ route('admin.discount.create') }}" class="btn btn-success mb-3">Add Voucher</a>

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
  <div class="d-flex justify-content-center">
    {{ $discounts->links('pagination::bootstrap-4') }}
</div>


@endsection
