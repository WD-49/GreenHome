@extends('layouts.admin')

@section('title', 'Quản lý bình luận')

@section('content')
<div class="row">
    <h3 class="text-center mb-4">Quản lý bình luận</h3>

    {{-- Bộ lọc --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.comments.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="product_name" value="{{ request('product_name') }}" class="form-control" placeholder="Tên sản phẩm">
                </div>
                <div class="col-md-3">
                    <input type="text" name="username" value="{{ request('username') }}" class="form-control" placeholder="Tên người dùng">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Trạng thái --</option>
                        <option value="hiển thị" {{ request('status') === 'hiển thị' ? 'selected' : '' }}>Hiển thị</option>
                        <option value="ẩn" {{ request('status') === 'ẩn' ? 'selected' : '' }}>Ẩn</option>
                        <option value="chưa duyệt" {{ request('status') === 'chưa duyệt' ? 'selected' : '' }}>Chưa duyệt</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control" placeholder="Từ ngày">
                </div>
                <div class="col-md-2">
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control" placeholder="Đến ngày">
                </div>

                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-filter me-1"></i> Lọc
                    </button>
                    <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary">
                        <i class="ti ti-x me-1"></i> Xoá lọc
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Nút Thùng rác --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.comments.trash') }}" class="btn btn-danger">
            <i class="ti ti-trash me-1"></i> Thùng rác
        </a>
    </div>

    {{-- Danh sách bình luận --}}
    <div class="card shadow-sm">
        <div class="table-responsive py-3 px-2">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Người dùng</th>
                        <th>Sản phẩm</th>
                        <th>Nội dung</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($comments as $comment)
                        <tr>
                            <td>{{ $comment->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if ($comment->user && $comment->user->profile && $comment->user->profile->user_image)
                    <img src="{{ asset('storage/' . $comment->user->profile->user_image) }}" alt="avatar" class="rounded-circle" width="60" height="60">
                @else
                    <img src="{{ asset('images/default-avatar.png') }}" alt="N/A" class="rounded-circle" width="60" height="60">
                @endif
                                    <div>
                                        <div class="fw-bold">{{ $comment->user->name ?? '[N/A]' }}</div>
                                        <div class="text-muted small">{{ $comment->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $comment->product->name ?? '[N/A]' }}</td>
                            <td>{{ Str::limit($comment->content, 60) }}</td>
                            <td>
                                @php
                                    $statusClass = match($comment->status) {
                                        'hiển thị' => 'bg-success-subtle text-success',
                                        'ẩn' => 'bg-secondary-subtle text-secondary',
                                        'chưa duyệt' => 'bg-warning-subtle text-warning',
                                        default => 'bg-light'
                                    };
                                    $icon = match($comment->status) {
                                        'hiển thị' => 'ti-check',
                                        'ẩn' => 'ti-eye-off',
                                        'chưa duyệt' => 'ti-alert-circle',
                                        default => ''
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} d-inline-flex align-items-center gap-1">
                                    <i class="ti {{ $icon }} fs-5"></i> {{ ucfirst($comment->status) }}
                                </span>
                            </td>
                            <td>{{ $comment->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-link text-dark p-0" type="button" id="dropdownMenu{{ $comment->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical fs-5"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu{{ $comment->id }}">
                                        @if ($comment->status === 'chưa duyệt')
                                            <li>
                                                <form method="POST" action="{{ route('admin.comments.approve') }}">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $comment->id }}">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="ti ti-check me-1"></i> Duyệt
                                                    </button>
                                                </form>
                                            </li>
                                        @elseif ($comment->status === 'hiển thị')
                                            <li>
                                                <form method="POST" action="{{ route('admin.comments.hide') }}">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $comment->id }}">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="ti ti-eye-off me-1"></i> Ẩn
                                                    </button>
                                                </form>
                                            </li>
                                        @elseif ($comment->status === 'ẩn')
                                            <li>
                                                <form method="POST" action="{{ route('admin.comments.showAgain') }}">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $comment->id }}">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="ti ti-eye me-1"></i> Hiện lại
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        <li>
                                            <a href="{{ route('admin.comments.show', $comment->id) }}" class="dropdown-item">
                                                <i class="ti ti-info-circle me-1"></i> Chi tiết
                                            </a>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.comments.destroy') }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $comment->id }}">
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="ti ti-trash me-1"></i> Xoá mềm
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Không có bình luận nào phù hợp.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Phân trang --}}
            <div class="mt-3">
                {{ $comments->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Thêm Bootstrap nếu không được chỉnh layout --}}
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
@endsection
