    {{-- <!-- Side-tool -->
    <div class="cr-tool-overlay"></div>
    <div class="cr-tool">
        <div class="cr-tool-btn">
            <a href="javascript:void(0)" class="btn-cr-tool result-placeholder">
                <i class="ri-settings-line"></i>
            </a>
            <div class="color-variant">
                <div class="cr-bar-title">
                    <h6>Tools</h6>
                    <a href="javascript:void(0)" class="close-tools">
                        <i class="ri-close-line"></i>
                    </a>
                </div>
                <div class="cr-tools-detail">
                    <div class="heading">
                        <h2>Select Color</h2>
                    </div>
                    <ul class="cr-color">
                        <li class="colors c1 active-colors"></li>
                        <li class="colors c2"></li>
                        <li class="colors c3"></li>
                        <li class="colors c4"></li>
                        <li class="colors c5"></li>
                        <li class="colors c6"></li>
                        <li class="colors c7"></li>
                        <li class="colors c8"></li>
                        <li class="colors c9"></li>
                        <li class="colors c10"></li>
                    </ul>
                </div>
                <div class="cr-tools-detail">
                    <div class="heading">
                        <h2>Dark mode</h2>
                    </div>
                    <ul class="dark-mode">
                        <li class="dark"></li>
                        <li class="white active-dark-mode"></li>
                    </ul>
                </div>
                <div class="cr-tools-detail">
                    <div class="heading">
                        <h2>RTL mode</h2>
                    </div>
                    <ul class="rtl-mode">
                        <li class="rtl">
                            <img src="{{ asset('assets_client/assets/img/tool/rtl.png') }}" alt="rtl">
                        </li>
                        <li class="ltr active-rtl-mode">
                            <img src="{{ asset('assets_client/assets/img/tool/ltr.png') }}" alt="ltr">
                        </li>
                    </ul>
                </div>
                <div class="cr-tools-detail">
                    <div class="heading">
                        <h2>Backgrounds</h2>
                    </div>
                    <ul class="bg-panel">
                        @for ($i = 1; $i <= 6; $i++)
                            <li class="bg-{{ $i }} {{ $i == 6 ? 'active-bg-panel' : '' }}">
                                <img src="{{ asset('assets_client/assets/img/shape/bg-shape-' . $i . '.png') }}"
                                    alt="bg-shape-{{ $i }}">
                            </li>
                        @endfor
                    </ul>
                </div>
            </div>
        </div>
    </div> --}}
