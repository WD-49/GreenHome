@extends('layouts.app')

@section('content')
    <!-- Breadcrumb -->
    <section class="section-breadcrumb">
        <div class="cr-breadcrumb-image">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="cr-breadcrumb-title">
                            <h2>Blog Classic</h2>
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
                                <input class="search-input" type="text" placeholder="Search here...">
                                <a href="javascript:void(0)" class="search-btn">
                                    <i class="ri-search-line"></i>
                                </a>
                            </form>
                        </div>
                        <div class="cr-blog-categories">
                            <div class="blog-heading">
                                <h4>Category</h4>
                            </div>
                            <div class="cr-blog-categories-content">
                                <ul>
                                    <li>Milks & Dairies <span>( 12 )</span></li>
                                    <li>Sea Food <span>( 09 )</span></li>
                                    <li>Fresh Fruit <span>( 65 )</span></li>
                                    <li>Pet Food <span>( 45 )</span></li>
                                    <li>Meat Food <span>( 34 )</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="cr-blog-recent">
                            <div class="blog-heading">
                                <h4>Recent Post</h4>
                            </div>
                            <div class="cr-blog-recent-post">
                                <div class="cr-blog-recent-image">
                                    <img src="assets/img/blog/1.jpg" alt="blog-1">
                                </div>
                                <div class="cr-blog-recent-content">
                                    <span>Sep 09, 2024</span>
                                    <h4>10 Tasty Organic Fruit choose</h4>
                                    <p>Lorem ipsum dolor consectetur adipisicing elit.</p>
                                </div>
                            </div>
                        </div>
                        <div class="cr-blog-gallery">
                            <div class="blog-heading">
                                <h4>Latest Gallery</h4>
                            </div>
                            <div class="cr-blog-instagram-image">
                                <div class="cr-blog-instagram-image-inner">
                                    <a href="#">
                                        <img src="assets/img/insta/1.jpg" alt="1">
                                    </a>
                                    <a href="#">
                                        <img src="assets/img/insta/2.jpg" alt="2">
                                    </a>
                                    <a href="#">
                                        <img src="assets/img/insta/3.jpg" alt="3">
                                    </a>
                                    <a href="#">
                                        <img src="assets/img/insta/4.jpg" alt="4">
                                    </a>
                                    <a href="#">
                                        <img src="assets/img/insta/5.jpg" alt="5">
                                    </a>
                                    <a href="#">
                                        <img src="assets/img/insta/6.jpg" alt="6">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="cr-blog-tags">
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
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-12 md-30">
                    <div class="cr-blog-classic" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                        <div class="cr-blog-classic-content">
                            <div class="cr-comment">
                                <span>By Admin <code> / 07 commen</code></span>
                            </div>
                            <h4>Best guide to Shopping for Organic ingredients.</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Iste alias odit cupiditate harum
                                magnam tenetur inventore dicta voluptatum perferendis voluptate!</p>
                            <a href="javascript:void(0)">read more</a>
                        </div>
                        <div class="cr-blog-image">
                            <img src="assets/img/blog/blog-1.jpg" alt="blog-1">
                        </div>
                    </div>
                    <div class="cr-blog-classic" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                        <div class="cr-blog-classic-content">
                            <div class="cr-comment">
                                <span>By Admin <code> / 07 commen</code></span>
                            </div>
                            <h4>Best guide to Shopping for Organic ingredients.</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Iste alias odit cupiditate harum
                                magnam tenetur inventore dicta voluptatum perferendis voluptate!</p>
                            <a href="javascript:void(0)">read more</a>
                        </div>
                        <div class="cr-blog-image">
                            <img src="assets/img/blog/blog-2.jpg" alt="blog-2">
                        </div>
                    </div>
                    <div class="cr-blog-classic" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                        <div class="cr-blog-classic-content">
                            <div class="cr-comment">
                                <span>By Admin <code> / 07 commen</code></span>
                            </div>
                            <h4>Best guide to Shopping for Organic ingredients.</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Iste alias odit cupiditate harum
                                magnam tenetur inventore dicta voluptatum perferendis voluptate!</p>
                            <a href="javascript:void(0)">read more</a>
                        </div>
                        <div class="cr-blog-image">
                            <img src="assets/img/blog/blog-3.jpg" alt="blog-3">
                        </div>
                    </div>

                    <nav aria-label="..." class="cr-pagination">
                        <ul class="pagination">
                            <li class="page-item disabled">
                                <span class="page-link">Previous</span>
                            </li>
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">1</span>
                            </li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </section>
@endsection
