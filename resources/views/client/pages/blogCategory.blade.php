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
                            <form method="GET" action="{{route('blog.index')}}" class="cr-search">
                                <input class="search" name="search" type="text" placeholder="Tìm kiếm tại đây...">
                                <a href="" type="submit" class="search-btn">
                                    <i class="ri-search-line"></i>
                                </a>
                            </form>
                        </div>

                        <div class="cr-blog-sorting">
                            <div class="blog-heading">
                                <h4>Lọc bài viết</h4>
                            </div>
                            <div class="cr-blog-categories-content">
        <form method="GET" action="{{ route('blog.index') }}">
            {{-- Preserve search term if it exists --}}
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            <select name="sort" onchange="this.form.submit()" class="form-select w-100">
                <option value="">Sắp xếp theo</option>
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
            </select>
        </form>
                            </div>
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
                        @if (count($blogs) > 0)
                            <div class="cr-blog-recent">
                            <div class="blog-heading">
                                <h4>Bài viết mới</h4>
                            </div>
                           <a href="{{route('blog.show', $slug = $newBlog->slug)}}">
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
                           </a>
                        </div>
                        @endif

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
                @if (count($blogs) > 0)
                    <div class="col-lg-9 col-12 md-30">
                    @foreach ($blogs as $blog)
    <div class="cr-blog-classic" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
        <div class="cr-blog-classic-content">
            <div class="cr-comment">
                <span>
                    By {{ $blog->author->name ?? 'Admin' }}
                </span>
            </div>
            <h4>{{ $blog->title }}</h4>
            <p>{{ $blog->summary }}</p>
            <a href="{{ route('blog.show', $blog->slug) }}">xem thêm</a>
        </div>
        <div class="cr-blog-image">
            <img src="{{ asset('storage/' . $blog->thumbnail) }}" alt="{{ $blog->slug }}">
        </div>
    </div>
@endforeach
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
                @endif
                @if (count($blogs) == 0)
                    <p class="text-center">Hiện không có bài viết nào</p>
                @endif
            </div>
        </div>
    </section>
@endsection