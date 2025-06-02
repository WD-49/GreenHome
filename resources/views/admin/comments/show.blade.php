@extends('layouts.admin')

@section('title', 'Chi tiết bình luận')

@section('content')
    <h3 class="mb-4">Chi tiết bình luận</h3>

    <div class="card mb-4">
        <div class="card-body">
            <h5>
                Sản phẩm:
                {{ $comment->product->name ?? '[N/A]' }}
                @if ($comment->product)
                    <a href="{{ route('admin.products.show', $comment->product->id) }}" class="btn btn-sm btn-outline-primary ms-2">
                        Xem chi tiết sản phẩm
                    </a>
                @endif
            </h5>

            <p>
                <strong>Người dùng:</strong>
                {{ $comment->user->name ?? '[N/A]' }}
                @if ($comment->user)
                    <a href="{{ route('admin.account.detailAccUser', $comment->user->id) }}" class="btn btn-sm btn-outline-info ms-2">
                        Xem chi tiết người dùng
                    </a>
                @endif
            </p>

            <p><strong>Nội dung:</strong> {{ $comment->content }}</p>
            <p><strong>Trạng thái:</strong>
                @if($comment->status == 1)
                    <span class="badge bg-success">Hiển thị</span>
                @else
                    <span class="badge bg-secondary">Ẩn</span>
                @endif
            </p>
            <p><strong>Ngày tạo:</strong> {{ $comment->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <h4 class="mb-3">Các bình luận khác cùng sản phẩm</h4>
    @if($relatedComments->isEmpty())
        <p class="text-muted">Không có bình luận nào khác cho sản phẩm này.</p>
    @else
       <ul class="list-group list-group-flush">
    @foreach($relatedComments as $related)
        <li class="list-group-item d-flex justify-content-between align-items-start">
            <div>
                <strong>{{ $related->user->name ?? '[N/A]' }}:</strong> {{ $related->content }}
                <br>
                <small class="text-muted">Ngày: {{ $related->created_at->format('d/m/Y H:i') }}</small>
            </div>

            <div class="btn-group">
                @if ($related->product)
                    <a href="{{ route('admin.products.show', $related->product->id) }}" class="btn btn-sm btn-outline-primary">
                        Xem sản phẩm
                    </a>
                @endif

                @if ($related->user)
                    <a href="{{ route('admin.account.detailAccUser', $related->user->id) }}" class="btn btn-sm btn-outline-info">
                        Xem người dùng
                    </a>
                @endif
            </div>
        </li>
    @endforeach
</ul>


    @endif
@endsection
