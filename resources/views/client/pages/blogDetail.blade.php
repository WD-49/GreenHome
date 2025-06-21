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
                                {{-- sai --}}
                                <span><code>By Admin</code> / {{ $blog->created_at->format('d-m-Y') }}</span>
                            </div>

                            <div class="cr-banner">
                                <h2>{{ $blog->title }}</h2>
                            </div>
                            <p class="mb-15">{{ $blog->summary }}</p>
                            <p>{!! $blog->content !!}</p>
                        </div>
                        <!-- Optional: Static inner images & quote -->
                        {{-- <div class="row mt-30">
                            <div class="col-6">
                                <div class="cr-blog-inner-cols">
                                    <div class="blog-img">
                                        <img src="{{ asset('assets/img/blog/blog-2.jpg') }}" alt="blog-2">
                                    </div>
                                    <div class="cr-blog-inner-content">
                                        <p>Sample content.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="cr-blog-inner-cols">
                                    <div class="blog-img">
                                        <img src="{{ asset('assets/img/blog/blog-3.jpg') }}" alt="blog-3">
                                    </div>
                                    <div class="cr-blog-inner-content">
                                        <p>Sample content.</p>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <div class="cr-blog-details-message">
                            <p>{{ Str::limit(strip_tags($blog->content), 300) }}</p>
                            <h5 class="title"> Admin</h5>
                        </div>
                        <div class="cr-blog-details-paragrap">
                            <p>Đây là nội dung phụ hoặc thêm của bài viết.</p>

                        </div>

                        {{-- Tags + Social --}}
                        <div class="cr-blog-details-tags">
                            <div class="cr-details-tags">
                                <ul class="cr-tags blog">
                                    <li><a href="javascript:void(0)">Tag 1</a></li>
                                    <li><a href="javascript:void(0)">Tag 2</a></li>
                                </ul>
                                <div class="cr-logo">
                                    <a href="#"><i class="ri-facebook-line"></i></a>
                                    <a href="#"><i class="ri-twitter-x-line"></i></a>
                                    <a href="#"><i class="ri-instagram-line"></i></a>
                                    <a href="#"><i class="ri-linkedin-line"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Pagination nếu cần -->
            </div>
        </div>
    </section>
@endsection
