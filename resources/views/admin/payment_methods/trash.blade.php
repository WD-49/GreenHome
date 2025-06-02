@extends('layouts.admin')

@section('title')
    {{ $title ?? 'Thùng rác phương thức thanh toán' }}
@endsection

@section('content')
    <div class="row">
        <h2 class="text-center mb-4">{{ $title ?? 'Thùng rác phương thức thanh toán' }}</h2>

        <ul class="nav nav-pills mb-3">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.paymentMethods.index') }}">
                    Tất cả ({{ $methodAll->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.paymentMethods.index', ['status' => 'active']) }}">
                    Đang hoạt động ({{ $methodActive->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.paymentMethods.trash') }}">
                    Thùng rác ({{ $methodTrashed->count() }})
                </a>
            </li>
        </ul>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Phương thức đã xóa</h4>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên phương thức</th>
                                <th>Mô tả</th>
                                <th>Trạng thái</th>
                                <th>Ngày xóa</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($paymentMethods as $method)
                                <tr>
                                    <td>{{ $method->id }}</td>
                                    <td>{{ $method->name }}</td>
                                    <td>{!! $method->description !!}</td>
                                    <td>
                                        <span class="badge {{ $method->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $method->status ? 'Kích hoạt' : 'Tạm tắt' }}
                                        </span>
                                    </td>
                                    <td>{{ $method->deleted_at ? $method->deleted_at->format('d/m/Y H:i') : '' }}</td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <form action="{{ route('admin.paymentMethods.restore', $method->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fa-solid fa-rotate-left me-1"></i> Khôi phục
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.paymentMethods.forceDelete', $method->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fa-solid fa-trash me-1"></i> Xóa vĩnh viễn
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Không có phương thức nào trong thùng rác.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($paymentMethods->lastPage() > 1)
                    <nav class="mt-4" aria-label="Page navigation">
                        <ul class="pagination justify-content-end">
                            <li class="page-item {{ $paymentMethods->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $paymentMethods->previousPageUrl() }}">Previous</a>
                            </li>
                            @for ($i = 1; $i <= $paymentMethods->lastPage(); $i++)
                                <li class="page-item {{ $i == $paymentMethods->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $paymentMethods->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor
                            <li class="page-item {{ !$paymentMethods->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $paymentMethods->nextPageUrl() }}">Next</a>
                            </li>
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </div>
@endsection
