@extends('layouts.app')

@section('content')
    <!-- Breadcrumb -->
    <section class="section-breadcrumb">
        <div class="cr-breadcrumb-image">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="cr-breadcrumb-title">
                            <h2>{{ $blog->title }}</h2>
                            <span><a href="{{ route('home') }}">Trang chủ</a> - {{ $blog->title }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog-details -->
    <section class="blog-details padding-tb-100">
        <div class="container">
            <div class="row" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                <div class="col-lg-12">
                    <div class="cr-blog-details">
                        {{-- Ảnh bìa --}}
                        <div class="cr-blog-details-image">
                            <img src="{{ asset('storage/' . $blog->thumbnail) }}" alt="{{ $blog->slug }}">
                        </div>

                        {{-- Thông tin bài viết --}}
                        <div class="cr-blog-details-content">
                            <div class="cr-admin-date">
                                <span>
                                    <code>By {{ $blog->author->name ?? 'Admin' }}</code> /
                                    {{ $blog->comments_count ?? 0 }} Comment /
                                    {{ $blog->created_at->format('d/m/Y') }}
                                </span>
                            </div>

                            <div class="cr-banner">
                                <h2>{{ $blog->title }}</h2>
                            </div>

                            <p class="mb-15">{{ $blog->summary }}</p>

                            <div>{!! $blog->content !!}</div>
                        </div>

                        {{-- Gợi ý nội dung khác: tạm thời bạn có thể giữ layout này --}}

                        {{-- Đoạn cuối --}}
                        <div class="cr-blog-details-paragrap">
                            <p>Cảm ơn bạn đã đọc bài viết. Hãy để lại bình luận hoặc chia sẻ nếu bạn thấy hữu ích.</p>
                        </div>

                        {{-- Tags + Social --}}
                        <div class="cr-blog-details-tags">
                            @if ($relatedBlogs->count())
    <div class="related-blogs mt-5">
        <h4 class="mb-4">Bài viết cùng chuyên mục</h4>
        <div class="row">
            @foreach ($relatedBlogs as $item)
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="{{ asset('storage/' . $item->thumbnail) }}" class="card-img-top" alt="{{ $item->title }}" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">{{ Str::limit($item->title, 80) }}</h5>
                            <p class="card-text text-muted small mb-2">
                                {{ $item->created_at->format('d/m/Y') }} — {{ $item->author->name ?? 'Admin' }}
                            </p>
                            <p class="card-text">{{ Str::limit($item->summary, 100) }}</p>
                            <a href="{{ route('blog.show', $item->slug) }}" class="btn btn-sm btn-outline-primary mt-2">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
                        </div>

                    </div>
                </div>

                {{-- Pagination giả định (nếu bạn dùng bài kế tiếp / trước đó) --}}

            </div>
        </div>
    </section>
@endsection
