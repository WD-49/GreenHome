@extends('layouts.admin')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Quản lý sản phẩm</h4>
            </div>

            {{-- <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                    <li class="breadcrumb-item active">Data Tables</li>
                </ol>
            </div> --}}
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Danh sách sản phẩm</h5>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <table id="scroll-horizontal-datatable" class="table table-striped w-100 nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th>Trạng thái</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $index => $product)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><img src="{{ asset('storage/' . $product->image) }}" width="60"
                                                class="rounded" alt="Hình ảnh sản phẩm"></td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category->name }}</td>
                                        <td> <span class="badge {{ $product->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $product->status == 1 ? 'Đang bán' : 'Dừng bán' }}
                                            </span></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-light btn-sm" type="button"
                                                    id="dropdownMenuButton{{ $product->id }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="dropdownMenuButton{{ $product->id }}">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.products.variants.index', $product) }}">
                                                            Xem biến thể
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.products.show', $product->id) }}">
                                                            Xem chi tiết
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.products.edit', $product->id) }}">
                                                            Chỉnh sửa
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.products.destroy', $product->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Bạn có chắc chắn muốn bỏ sản phẩm này vào thùng rác không?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="dropdown-item text-danger" type="submit">
                                                                Xóa sản phẩm
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#product-datatable').DataTable({
                    language: {
                        search: "Tìm kiếm:",
                        lengthMenu: "Hiển thị _MENU_ bản ghi",
                        info: "Hiển thị _START_ đến _END_ trong _TOTAL_ bản ghi",
                        paginate: {
                            previous: "Trước",
                            next: "Tiếp"
                        },
                        zeroRecords: "Không tìm thấy bản ghi phù hợp",
                    },
                    pageLength: 10,
                    responsive: true,
                });
            });
        </script>
    @endpush
@endsection
