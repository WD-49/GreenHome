@extends('layouts.app')

@section('content')
    <style>
        img {
            width: 800px;
            height: auto;
            display: block;
            margin: 10px auto;
        }
    </style>
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
                                <span><code>Ngày Đăng:</code>{{ $blog->created_at->format('d-m-Y') }}</span>
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


                    </div>
                </div>
                <!-- Pagination nếu cần -->
            </div>
        </div>
    </section>
@endsection
