@extends('layouts.admin')

@section('title', 'Chi tiết bình luận')

@section('content')
    <h3 class="mb-4">Chi tiết bình luận</h3>

    <div class="card mb-4">
        <div class="card-body">
            <h5>
                Sản phẩm:
                @if ($comment->product)
                    <a href="{{ route('admin.products.show', $comment->product->id) }}">
                        {{ $comment->product->name }}
                    </a>
                    <small class="text-muted">
                        (Danh mục: {{ $comment->product->category->name ?? '[N/A]' }})
                    </small>
                    @if ($comment->product->brand)
                        <br>
                        <small>
                            Thương hiệu:
                            <a href="{{ route('admin.brands.show', $comment->product->brand->slug) }}">
                                {{ $comment->product->brand->name }}
                            </a>
                        </small>
                    @endif
                @else
                    [N/A]
                @endif
            </h5>

            <p>
                <strong>Người dùng:</strong>
                @if ($comment->user)
                    <a href="{{ route('admin.account.detailAccUser', $comment->user->id) }}">
                        {{ $comment->user->name }}
                    </a>
                @else
                    [N/A]
                @endif
            </p>

            <p>
                @if ($comment->user && $comment->user->profile && $comment->user->profile->user_image)
                    <img src="{{ asset('storage/' . $comment->user->profile->user_image) }}" alt="avatar" class="rounded-circle" width="60" height="60">
                @else
                    <img src="{{ asset('images/default-avatar.png') }}" alt="avatar mặc định" class="rounded-circle" width="60" height="60">
                @endif
            </p>

            <p><strong>Nội dung:</strong> {{ $comment->content }}</p>

            <p><strong>Trạng thái:</strong>
                @switch($comment->status)
                    @case('chưa duyệt') <span class="badge bg-warning text-dark">Chưa duyệt</span> @break
                    @case('hiển thị') <span class="badge bg-success">Hiển thị</span> @break
                    @case('ẩn') <span class="badge bg-secondary">Ẩn</span> @break
                    @default <span class="badge bg-info">{{ $comment->status }}</span>
                @endswitch
            </p>

            <p><strong>Ngày tạo:</strong> {{ $comment->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <a href="{{ route('admin.comments.index') }}" class="btn btn-primary mb-3">
        <i class="ti ti-arrow-left"></i> Quay lại danh sách
    </a>

    <h4 class="mb-3">Các bình luận khác cùng sản phẩm</h4>

    {{-- Bộ lọc --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="text" name="user_name" class="form-control" placeholder="Tên người dùng" value="{{ request('user_name') }}">
        </div>

        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">-- Trạng thái --</option>
                <option value="hiển thị" {{ request('status') == 'hiển thị' ? 'selected' : '' }}>Hiển thị</option>
                <option value="chưa duyệt" {{ request('status') == 'chưa duyệt' ? 'selected' : '' }}>Chưa duyệt</option>
                <option value="ẩn" {{ request('status') == 'ẩn' ? 'selected' : '' }}>Ẩn</option>
            </select>
        </div>

        <div class="col-md-2">
            <input type="date" name="min_date" class="form-control" value="{{ request('min_date') }}">
        </div>

        <div class="col-md-2">
            <input type="date" name="max_date" class="form-control" value="{{ request('max_date') }}">
        </div>

        <div class="col-md-2">
            <select name="brand_slug" class="form-select">
                <option value="">-- Thương hiệu --</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->slug }}" {{ request('brand_slug') == $brand->slug ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select name="category_id" class="form-select">
                <option value="">-- Danh mục --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">Lọc</button>
        </div>

        <div class="col-md-2 d-grid gap-2">
            <a href="{{ route('admin.comments.show', $comment->id) }}" class="btn btn-outline-dark">Làm mới</a>
        </div>
    </form>

    {{-- Danh sách bình luận liên quan --}}
    @if($relatedComments->isEmpty())
        <p class="text-muted">Không có bình luận nào khác cho sản phẩm này.</p>
    @else
        <ul class="list-group list-group-flush">
            @foreach($relatedComments as $related)
                <li class="list-group-item d-flex justify-content-between flex-column flex-md-row">
                    <div class="d-flex align-items-center mb-2 mb-md-0">
                        @if ($related->user && $related->user->profile && $related->user->profile->user_image)
                            <img src="{{ asset('storage/' . $related->user->profile->user_image) }}" alt="avatar" class="rounded-circle me-3" width="40" height="40">
                        @else
                            <img src="{{ asset('images/default-avatar.png') }}" alt="avatar mặc định" class="rounded-circle me-3" width="40" height="40">
                        @endif
                        <div>
                            <strong>
                                <a href="{{ route('admin.account.detailAccUser', $related->user->id ?? '#') }}">
                                    {{ $related->user->name ?? '[N/A]' }}
                                </a>:
                            </strong>
                            {{ $related->content }}
                            <br>
                            <small class="text-muted">Ngày: {{ $related->created_at->format('d/m/Y H:i') }}</small>
                            <br>
                            <small>
                                Trạng thái:
                                @switch($related->status)
                                    @case('chưa duyệt') <span class="badge bg-warning text-dark">Chưa duyệt</span> @break
                                    @case('hiển thị') <span class="badge bg-success">Hiển thị</span> @break
                                    @case('ẩn') <span class="badge bg-secondary">Ẩn</span> @break
                                    @default <span class="badge bg-info">{{ $related->status }}</span>
                                @endswitch
                            </small>
                        </div>
                    </div>

                    <div class="text-end">
                        @if ($related->product)
                            <div class="mb-2">
                                Sản phẩm:
                                <a href="{{ route('admin.products.show', $related->product->id) }}">
                                    {{ $related->product->name }}
                                </a>
                                <br>
                                <small class="text-muted">Danh mục: {{ $related->product->category->name ?? '[N/A]' }}</small>
                                <br>
                                @if ($related->product->brand)
                                    <small>
                                        Thương hiệu:
                                        <a href="{{ route('admin.brands.show', $related->product->brand->slug) }}">
                                            {{ $related->product->brand->name }}
                                        </a>
                                    </small>
                                @endif
                            </div>
                        @endif

                        <div>
                            @if ($related->status == 'chưa duyệt')
                                <form action="{{ route('admin.comments.approve') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $related->id }}">
                                    <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                                </form>
                            @elseif ($related->status == 'hiển thị')
                                <form action="{{ route('admin.comments.hide') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $related->id }}">
                                    <button type="submit" class="btn btn-sm btn-warning">Ẩn</button>
                                </form>
                            @elseif ($related->status == 'ẩn')
                                <form action="{{ route('admin.comments.showAgain') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $related->id }}">
                                    <button type="submit" class="btn btn-sm btn-info">Hiện lại</button>
                                </form>
                            @endif

                            <form action="{{ route('admin.comments.destroy') }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="id" value="{{ $related->id }}">
                                <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-3">
            {{ $relatedComments->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
