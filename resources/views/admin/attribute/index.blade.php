@extends('layouts.admin')


@section('content')
    <h2 class="text-center">{{ $title }}</h2>


    <ul class="nav nav-pills mb-3">
        <li class="nav-item">

        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('admin.products.index', ['status' => 1]) }}">
                Đang hoạt động ({{ $attributes->count() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.attribute.trash') }}">
                Thùng rác ({{ $deleteCount }})
            </a>
        </li>
    </ul>


    <div class=" mt-4 bg-white shadow-sm rounded p-3 ">
        <div class="d-md-flex align-items-center">
            <div>
                <h4 class="card-title text-dark">Danh sách Thuộc tính</h4>
                <p class="card-subtitle">Quản lý các thuộc tính của sản phẩm</p>
            </div>
            <div class="ms-auto mt-3 mt-md-0">

                <a href="{{ route('admin.attribute.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-1"></i> Thêm mới
                </a>

            </div>
        </div>
        @if (count($attributes) <= 0)
            <div>
                <p class="text-center text-muted">Không có thuộc tính nào</p>
            </div>
        @endif
        @if (count($attributes) > 0)
            <table class="table table-bordered mt-4 table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 200px;">Tên thuộc tính</th>
                        <th>Giá trị hiện có</th>
                        <th style="width: 200px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attributes as $attribute)
                        <tr>
                            <td>{{ $attribute->id }}</td>
                            <td>{{ $attribute->name }}</td>
                            <td>{{ $attribute->attributeValues->count() ?? 0 }}</td>

                            {{-- Action --}}


                            <td class="px-0 text-end">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm me-2" type="button" id="dropdownMenuButton"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v mx-auto"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.attribute.show', $attribute->id) }}">
                                                Chi tiết
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.attribute.value.create', $attribute->id) }}">
                                                Thêm giá trị
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.attribute.edit', $attribute->id) }}">
                                                Chỉnh sửa
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.attribute.destroy', $attribute->id) }}"
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
                    @empty
                        <tr>
                            <td colspan="4">Không có thuộc tính nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>
@endsection
