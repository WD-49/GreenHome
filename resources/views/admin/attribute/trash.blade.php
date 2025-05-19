@extends('layouts.admin')

@section('content')
<div class="container my-4">
    <h3 class="mb-4">Thùng rác - Các thuộc tính đã xóa</h3>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($attributes->isEmpty())
        <div class="alert alert-info">Không có thuộc tính nào trong thùng rác.</div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="thead-dark text-center">
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th>Tên Thuộc Tính</th>
                    <th style="width: 180px;">Ngày Xóa</th>
                    <th style="width: 200px;">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attributes as $attribute)
                <tr>
                    <td class="text-center">{{ $attribute->id }}</td>
                    <td>{{ $attribute->name }}</td>
                    <td class="text-center">{{ $attribute->deleted_at}}</td>
                    <td class="text-center">
                        <form action="{{route('admin.attribute.restore', $id = $attribute->id)}}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-sm" title="Phục hồi">
                                <i class="fas fa-recycle"></i> Phục hồi
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection

{{-- Nhớ import Font Awesome trong layout chính để có icon --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
