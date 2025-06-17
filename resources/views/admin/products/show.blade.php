@extends('layouts.admin')

@section('title', 'Chi tiết sản phẩm - ' . $product->name)

@section('content')

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Chi tiết sản phẩm</h4>
        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-body">
                    <div class="card-body font-tapeli">
                        <div class="d-flex align-items-center mb-4">
                            <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded" alt="Thumbnail"
                                style="width:220px; height:auto; object-fit:cover; margin-right:3rem;">
                            <div class="ms-5">
                                <h4 class="mb-2 text-dark" style="font-size:2.2rem;">{{ $product->name }}</h4>
                                <p class="text-muted mb-1" style="font-size:1.15rem;">Slug:
                                    <strong>{{ $product->slug }}</strong>
                                </p>
                                <p class="text-muted mb-1" style="font-size:1.15rem;">Danh mục:
                                    <strong>{{ $product->category->name ?? 'Chưa có danh mục' }}</strong>
                                </p>
                                <p class="text-muted mb-1" style="font-size:1.15rem;">Thương hiệu:
                                    <strong>{{ $product->brand->name ?? 'Chưa có thương hiệu' }}</strong>
                                </p>
                                <p class="text-muted mb-1" style="font-size:1.15rem;">Lượt xem:
                                    <strong>{{ $product->view ?? '0' }}</strong>
                                </p>
                                <p class="text-muted mb-1" style="font-size:1.15rem;">Ngày nhập:
                                    <strong>{{ $product->date_of_entry ? \Carbon\Carbon::parse($product->date_of_entry)->format('d/m/Y') : 'Chưa có' }}</strong>
                                </p>
                                <p class="text-muted mb-1" style="font-size:1.15rem;">Số lượng:
                                    <strong>{{ $product->quantity ?? 0 }}</strong>
                                </p>
                                <p class="text-muted mb-1" style="font-size:1.15rem;">Trạng thái:
                                    @if ($product->status)
                                        <span class="badge bg-success">Đang bán</span>
                                    @else
                                        <span class="badge bg-danger">Dừng bán</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        {{-- ...phần còn lại giữ nguyên... --}}
                    </div>

                    <ul class="nav nav-underline border-bottom pt-2" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active p-2" id="description_tab" data-bs-toggle="tab" href="#description"
                                role="tab">
                                <span class="d-none d-sm-block">Mô tả</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link p-2" id="comment_tab" data-bs-toggle="tab" href="#comment" role="tab">
                                <span class="d-none d-sm-block">Bình luận</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link p-2" id="review_tab" data-bs-toggle="tab" href="#review" role="tab">
                                <span class="d-none d-sm-block">Đánh giá</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link p-2" id="variant_tab" data-bs-toggle="tab" href="#variant" role="tab">
                                <span class="d-none d-sm-block">Biến thể</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content text-muted bg-white">
                        <div class="tab-pane active show pt-4" id="description" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <p>{!! $product->description !!}</p>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane pt-4" id="comment" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div id="ajax-comments">
                                        <div id="comment-table-content">
                                            @include('admin.products.partials.comment-table', [
                                                'comments' => $comments,
                                            ])
                                            <x-ajax-pagination :paginator="$comments->appends(['tab' => 'comments'])" />

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="tab-pane pt-4" id="review" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    {{-- Đánh giá --}}
                                    <div id="ajax-reviews">
                                        <div id="review-table-content">
                                            @include('admin.products.partials.review-table', [
                                                'reviews' => $reviews,
                                            ])
                                            <x-ajax-pagination :paginator="$reviews->appends(['tab' => 'reviews'])" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane pt-4" id="variant" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    {{-- Biến thể --}}
                                    <div id="ajax-variants">
                                        <div id="variant-table-content">
                                            @include('admin.products.partials.variant-table', [
                                                'variants' => $variants,
                                            ])
                                            <x-ajax-pagination :paginator="$variants->appends(['tab' => 'variants'])" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> <!-- Tab panes -->
                </div>
            </div>
        </div>
    </div>

    {{-- @vite('resources/js/app.js') --}}

@endsection
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab');

            if (activeTab) {
                const tabId = {
                    'comments': '#comment',
                    'reviews': '#review',
                    'variants': '#variant'
                } [activeTab];

                if (tabId) {
                    const tabTrigger = document.querySelector(`a[href="${tabId}"]`);
                    if (tabTrigger) {
                        new bootstrap.Tab(tabTrigger).show();
                    }
                }
            }
        });
    </script>
@endpush
