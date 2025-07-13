<!-- Footer -->
<footer class="footer padding-t-100 bg-off-white">
    <div class="container">
        <div class="row footer-top padding-b-100">
            <div class="col-xl-4 col-lg-6 col-sm-12 col-12 cr-footer-border">
                <div class="cr-footer-logo">
                    <div class="image">
                        <h2 style="font-weight: bold; font-size: 24px;">
                            {{ $footerWebInfo['web_name'] ?? 'Tên website' }}
                        </h2>
                    </div>
                    {!! $footerWebInfo['sortDes'] ?? '<em>Chưa có mô tả</em>' !!}
                </div>
                <div class="cr-footer">
                    <h4 class="cr-sub-title cr-title-hidden">Contact us <span class="cr-heading-res"></span></h4>
                    <ul class="cr-footer-links cr-footer-dropdown">
                        <li class="location-icon">{{ $footerWebInfo['address'] ?? 'Địa chỉ đang cập nhật' }}</li>
                        <li class="mail-icon">
                            <a href="mailto:{{ $footerWebInfo['email'] ?? '#' }}">
                                {{ $footerWebInfo['email'] ?? 'Email đang cập nhật' }}
                            </a>
                        </li>
                        <li class="phone-icon">
                            <a href="tel:{{ $footerWebInfo['phone'] ?? '#' }}">
                                {{ $footerWebInfo['phone'] ?? 'SĐT đang cập nhật' }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-xl-2 col-lg-3 col-sm-12 col-12 cr-footer-border">
                <div class="cr-footer">
                    <h4 class="cr-sub-title">Company <span class="cr-heading-res"></span></h4>
                    <ul class="cr-footer-links cr-footer-dropdown">
                        <li><a href="about.html">About Us</a></li>
                        <li><a href="track-order.html">Delivery Information</a></li>
                        <li><a href="policy.html">Privacy Policy</a></li>
                        <li><a href="terms.html">Terms & Conditions</a></li>
                        <li><a href="contact-us.html">Contact Us</a></li>
                        <li><a href="faq.html">Support Center</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-xl-2 col-lg-3 col-sm-12 col-12 cr-footer-border">
                <div class="cr-footer">
                    <h4 class="cr-sub-title">Category <span class="cr-heading-res"></span></h4>
                    <ul class="cr-footer-links cr-footer-dropdown">
                        @foreach($footerCategories as $category)
                            <li><a href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="col-xl-4 col-lg-12 col-sm-12 col-12 cr-footer-border">
                <div class="cr-footer cr-newsletter">
                    <h4 class="cr-sub-title">Subscribe Our Newsletter <span class="cr-heading-res"></span></h4>
                    <div class="cr-footer-links cr-footer-dropdown">
                        <form class="cr-search-footer">
                            <input class="search-input" type="text" placeholder="Search here...">
                            <a href="javascript:void(0)" class="search-btn">
                                <i class="ri-send-plane-fill"></i>
                            </a>
                        </form>
                    </div>
                    <div class="cr-social-media">
                        <span><a href="#"><i class="ri-facebook-line"></i></a></span>
                        <span><a href="#"><i class="ri-twitter-x-line"></i></a></span>
                        <span><a href="#"><i class="ri-dribbble-line"></i></a></span>
                        <span><a href="#"><i class="ri-instagram-line"></i></a></span>
                    </div>
                    <div class="cr-payment">
                        <div class="cr-insta-slider swiper-container">
                            <div class="swiper-wrapper">
                                @for ($i = 1; $i <= 8; $i++)
                                    <div class="swiper-slide">
                                        <a href="#" class="cr-payment-image">
                                            <img src="{{ asset('assets_client/assets/img/insta/' . $i . '.jpg') }}" alt="insta">
                                            <div class="payment-overlay"></div>
                                        </a>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="cr-last-footer">
            <p>&copy; <span id="copyright_year"></span>
                <a href="index.html">Carrot</a>, All rights reserved.</p>
        </div>
    </div>
</footer>
