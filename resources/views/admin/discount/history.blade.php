@extends('layouts.admin')

@section('content')
<h1>Lịch sử sử dụng mã giảm giá</h1>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>STT</th>
            <th>Mã giảm giá</th>
            <th>Người sử dụng</th>
            <th>Ngày sử dụng</th>
            <th>Created At</th>
            <th>Deleted At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($usages as $index => $usage)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $usage->discount->code ?? 'N/A' }}</td>
            <td>{{ $usage->user->name ?? 'N/A' }}</td>
            <td>{{ $usage->used_at }}</td>
            <td>{{ $usage->created_at }}</td>
            <td>{{ $usage->deleted_at ?? 'Chưa xóa' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
