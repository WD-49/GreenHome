@extends('layouts.app')

@section('content')
    <!-- Breadcrumb -->
    <section class="section-breadcrumb">
        <div class="cr-breadcrumb-image">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="cr-breadcrumb-title">
                            <h2>Bài viết</h2>
                            <span><a href="index.html">Home</a> - Blog Classic</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Classic -->
    <section class="section-blog-Classic padding-tb-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-12 md-30">
                    <div class="cr-blog-sideview" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                        <div class="cr-serch-box">
                            <form class="cr-search">
                                <input class="search-input" type="text" placeholder="Tìm kiếm tại đây...">
                                <a href="javascript:void(0)" class="search-btn">
                                    <i class="ri-search-line"></i>
                                </a>
                            </form>
                        </div>
                        <div class="cr-blog-categories">
                            <div class="blog-heading">
                                <h4>Danh mục</h4>
                            </div>
                            <div class="cr-blog-categories-content">
                                <ul>
                                    @foreach($blogCategories as $blogCategory)
                                        <li>
                                            <a href="{{ route('blog.index', $blogCategory->slug) }}">
                                                {{ $blogCategory->name }} <span>({{ $blogCategory->blogs_count ?? 0 }})</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="cr-blog-recent">
                            <div class="blog-heading">
                                <h4>Bài viết mới</h4>
                            </div>
                            <div class="cr-blog-recent-post">
                                <div class="cr-blog-recent-image">
                                    <img src="{{ asset('storage/' . $newBlog->thumbnail) }}" alt="blog-1">
                                </div>
                                <div class="cr-blog-recent-content">
                                    <span>Sep 09, 2024</span>
                                    <h4>{{$newBlog->title}}</h4>
                                    <p>{{$newBlog->summary}}</p>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="cr-blog-tags">
                            <div class="blog-heading">
                                <h4>Popular Tags</h4>
                            </div>
                            <div class="cr-blog-tags-inner">
                                <ul class="cr-tags">
                                    <li><a href="javascript:void(0)">Vegetables</a></li>
                                    <li><a href="javascript:void(0)">juice</a></li>
                                    <li><a href="javascript:void(0)">Meat Food</a></li>
                                    <li><a href="javascript:void(0)">Cabbage</a></li>
                                    <li><a href="javascript:void(0)">Organic food</a></li>
                                    <li><a href="javascript:void(0)">juice</a></li>
                                </ul>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="col-lg-9 col-12 md-30">
                    <div class="container">
                        <div class="row">
                            @foreach ($blogs as $blog)
                                <div class="col-12 mb-4">
                                    <div
                                        class="blog-card d-flex shadow-sm rounded overflow-hidden bg-white border hover-shadow">

                                        {{-- Ảnh bên trái --}}
                                        <div class="blog-card-image" style="width: 35%; max-height: 220px; overflow: hidden;">
                                            <a href="{{ route('blog.show', $blog->slug) }}">
                                                <img src="{{ asset('storage/' . $blog->thumbnail) }}" alt="{{ $blog->slug }}"
                                                    class="w-100 h-100 object-fit-cover" style="transition: 0.4s ease;">
                                            </a>
                                        </div>

                                        {{-- Gạch dọc xám --}}
                                        <div class="d-none d-md-block" style="width: 1px; background-color: #d3d3d3;"></div>

                                        {{-- Nội dung bên phải --}}
                                        <div class="p-3 d-flex flex-column justify-content-between" style="width: 64%;">
                                            <div>
                                                <h5 class="fw-bold mb-2">
                                                    <a href="{{ route('blog.show', $blog->slug) }}"
                                                        class="text-dark text-decoration-none">
                                                        {{ Str::limit($blog->title, 100) }}
                                                    </a>
                                                </h5>
                                                <p class="text-muted small mb-3">
                                                    {{ $blog->created_at->format('d/m/Y') }} —
                                                    {{ $blog->author->name ?? 'Admin' }}
                                                </p>
                                                <p class="mb-3">{{ Str::limit($blog->summary, 150) }}</p>
                                            </div>
                                            <a href="{{ route('blog.show', $blog->slug) }}" class="">
                                                Xem chi tiết
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>





{{-- <!-- Phân trang -->
<div class="mt-4">
    {{ $blogs->links() }}
</div> --}}
                 @if ($blogs->lastPage() > 1)
<nav class="cr-pagination mt-4" aria-label="Page navigation">
    <ul class="pagination justify-content-center">

        {{-- Previous Page Link --}}
        <li class="page-item {{ $blogs->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $blogs->previousPageUrl() ?? '#' }}">Previous</a>
        </li>

        {{-- Pagination Elements --}}
        @for ($i = 1; $i <= $blogs->lastPage(); $i++)
            <li class="page-item {{ $blogs->currentPage() == $i ? 'active' : '' }}">
                <a class="page-link" href="{{ $blogs->url($i) }}">{{ $i }}</a>
            </li>
        @endfor

        {{-- Next Page Link --}}
        <li class="page-item {{ $blogs->currentPage() == $blogs->lastPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $blogs->nextPageUrl() ?? '#' }}">Next</a>
        </li>

    </ul>
</nav>
@endif

                </div>
            </div>
        </div>
    </section>
@endsection