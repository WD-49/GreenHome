@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h2 class="mb-4">Danh sách yêu thích</h2>

        @if ($wishlists->isEmpty())
            <p>Bạn chưa có sản phẩm nào trong danh sách yêu thích.</p>
        @else
            <div class="row col-100 mb-minus-24">
                @foreach ($wishlists as $item)
                    @php
                        $product = $item->product;
                        $prices = optional($product->productVariants)->pluck('price')->filter();
                        $avg = $product->reviews_avg_rating ?? 0;
                        $count = $product->reviews_count ?? 0;
                        $fullStars = floor($avg);
                        $halfStar = $avg - $fullStars >= 0.5;
                        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                    @endphp

                    <div class="col-xxl-3 col-xl-4 col-6 cr-product-box mb-24">
                        <div class="cr-product-card">
                            <div class="cr-product-image">
                                <div class="cr-image-inner zoom-image-hover">
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                </div>
                                <div class="cr-side-view d-flex flex-column align-items-end gap-2"
                                    style="position: absolute; top: 10px; right: 10px;">
                                    <div class="cr-side-view d-flex flex-column align-items-end gap-2"
                                        style="position: absolute; top: 10px; right: 10px;">
                                        {{-- Toggle wishlist --}}
                                        <a href="javascript:void(0);"
                                            class="wishlist-button d-flex align-items-center justify-content-center rounded-circle bg-white shadow"
                                            data-product-id="{{ $product->id }}" style="width: 40px; height: 40px;">
                                            <i class="ri-heart-fill text-danger fs-5"></i>
                                        </a>

                                        {{-- Toggle notify_on_sale --}}
                                        <button type="button"
                                            class="toggle-notify d-flex align-items-center justify-content-center rounded-circle bg-white shadow border-0"
                                            data-product-id="{{ $product->id }}"
                                            data-current="{{ $item->notify_on_sale ? '1' : '0' }}"
                                            style="width: 40px; height: 40px;">
                                            @if ($item->notify_on_sale)
                                                <i class="ri-notification-3-fill text-success fs-5"></i>
                                            @else
                                                <i class="ri-notification-off-line text-muted fs-5"></i>
                                            @endif
                                        </button>
                                        {{-- Select priority --}}
                                        <select class="form-select form-select-sm select-priority mt-1"
                                            data-product-id="{{ $product->id }}" style="width: 80px; font-size: 13px;">
                                            @foreach (['Low', 'Medium', 'High'] as $level)
                                                <option value="{{ $level }}"
                                                    {{ $item->priority === $level ? 'selected' : '' }}>
                                                    {{ $level }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>



                                </div>

                                <a class="cr-shopping-bag" href="#"><i class="ri-shopping-bag-line"></i></a>
                            </div>

                            <div class="cr-product-details">
                                <div class="cr-brand">
                                    <a href="#">{{ $product->brand->name ?? 'Không có thương hiệu' }}</a>
                                    <div class="cr-star">
                                        @for ($i = 0; $i < $fullStars; $i++)
                                            <i class="ri-star-fill"></i>
                                        @endfor
                                        @if ($halfStar)
                                            <i class="ri-star-half-line"></i>
                                        @endif
                                        @for ($i = 0; $i < $emptyStars; $i++)
                                            <i class="ri-star-line"></i>
                                        @endfor
                                        <p>({{ $avg }} / {{ $count }} đánh giá)</p>
                                    </div>
                                </div>

                                <a href="{{ route('productDetail', $product->slug) }}" class="title">
                                    {{ $product->name }}
                                </a>

                                <p class="text">Sản phẩm yêu thích của bạn</p>

                                <ul class="list">
                                    <li><label>Brand :</label> {{ $product->brand->name ?? 'Không rõ' }}</li>
                                </ul>

                                @if ($prices->isNotEmpty())
                                    <p class="cr-price">
                                        <span class="new-price">
                                            @php
                                                $min = $prices->min();
                                                $max = $prices->max();
                                            @endphp
                                            {{ $min === $max ? number_format($min, 0, ',', '.') . ' đ' : number_format($min, 0, ',', '.') . ' đ - ' . number_format($max, 0, ',', '.') . ' đ' }}
                                        </span>
                                    </p>
                                @else
                                    <p class="cr-price"><span class="new-price">Chưa có giá</span></p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
   


            {{-- Phân trang --}}
            <div class="mt-4">
                {{ $wishlists->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            xhrFields: {
                withCredentials: true
            }
        });

        // Toggle wishlist
        $(document).on('click', '.wishlist-button', function (e) {
            e.preventDefault();
            const button = $(this);
            const productId = button.data('product-id');

            $.post('{{ route('wishlist.toggle') }}', {
                product_id: productId
            }, function (res) {
                if (!res.added) {
                    button.closest('.cr-product-box').fadeOut();
                }
            }).fail(function (xhr) {
                alert(xhr.status === 401 ? 'Bạn cần đăng nhập.' : 'Lỗi khi cập nhật wishlist.');
            });
        });

        // Toggle notify_on_sale
        $(document).on('click', '.toggle-notify', function () {
            const button = $(this);
            const productId = button.data('product-id');
            const current = button.data('current') == '1';

            $.post('{{ route('wishlist.updateOptions') }}', {
                product_id: productId,
                field: 'notify_on_sale',
                value: current ? 0 : 1
            }, function () {
                button.data('current', current ? 0 : 1);
                button.find('i').toggleClass(
                    'ri-notification-off-line ri-notification-3-fill text-success'
                );
            });
        });

        // Cập nhật priority và tự động sắp xếp lại danh sách
        $(document).on('change', '.select-priority', function () {
            const select = $(this);
            const productId = select.data('product-id');
            const value = select.val();

            $.post('{{ route('wishlist.updateOptions') }}', {
                product_id: productId,
                field: 'priority',
                value: value
            }, function () {
                // Sắp xếp lại sau khi cập nhật
                sortWishlistByPriority();
            });
        });

        // Hàm sắp xếp danh sách wishlist theo priority
        function sortWishlistByPriority() {
            const priorityOrder = {
                'High': 1,
                'Medium': 2,
                'Low': 3
            };

            const items = $('.cr-product-box').get();

            items.sort(function (a, b) {
                const priorityA = $(a).find('.select-priority').val();
                const priorityB = $(b).find('.select-priority').val();

                return priorityOrder[priorityA] - priorityOrder[priorityB];
            });

            $('.row.col-100.mb-minus-24').empty().append(items);
        }
    </script>
@endpush

