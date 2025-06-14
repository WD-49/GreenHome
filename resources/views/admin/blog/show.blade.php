@extends('layouts.admin')

@section('content')
    <h2 class="text-center mb-4">Chi tiết bài viết</h2>

    {{-- Card: Thông tin cơ bản --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-bold">Thông tin chung</div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <label class="text-muted">Tiêu đề</label>
                    <div class="fw-bold">{{ $blog->title }}</div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="text-muted">Slug</label>
                    <div>{{ $blog->slug }}</div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="text-muted">Tóm tắt</label>
                    <div>{{ $blog->summary }}</div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="text-muted">Trạng thái</label>
                    <div>
                        @if ($blog->status == 1)
                            <span class="badge bg-success">Hiển thị</span>
                        @else
                            <span class="badge bg-secondary">Ẩn</span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="text-muted">Thể loại</label>
                    <div>{{ $blog->category->name ?? 'Chưa phân loại' }}</div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="text-muted">Tác giả</label>
                    <div>{{ $blog->author->name ?? 'Không rõ' }}</div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="text-muted">Ảnh bìa</label>
                    <div>
                        @if ($blog->thumbnail)
                            <img src="{{ asset('storage/' . $blog->thumbnail) }}" class="img-thumbnail"
                                style="max-height: 100px;" alt="Thumbnail">
                        @else
                            <span class="text-muted">Không có ảnh</span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="text-muted">Ngày tạo</label>
                    <div>{{ $blog->created_at->format('d/m/Y H:i') }}</div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="text-muted">Ngày cập nhật</label>
                    <div>{{ $blog->updated_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card: Nội dung bài viết --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light fw-bold">Nội dung bài viết</div>
        <div class="card-body">
            <div class="p-3 border rounded bg-white">
                {!! $blog->content !!}
            </div>
        </div>
    </div>

    {{-- bai viet lien quan --}}
    @if ($relatedBlogs->count()>0)
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-light fw-bold">Bài viết cùng chuyên mục</div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach ($relatedBlogs as $related)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('admin.blogs.show', $related->id) }}" class="fw-semibold">
                                    {{ $related->title }}
                                </a>
                                <div class="text-muted small">
                                    {{ $related->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                            <span class="badge bg-secondary">
                                {{ $related->status ? 'Hiển thị' : 'Ẩn' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif


    {{-- Nút quay lại --}}
    <div class="mt-4">
        <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>
@endsection