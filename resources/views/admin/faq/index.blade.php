@extends('layouts.admin')

@section('title')
    Danh sách câu hỏi thường gặp
@endsection

@section('content')
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-center flex-sm-row flex-column mb-3">
            <div class="flex-grow-1 d-flex align-items-center gap-2">
                <i class="mdi mdi-comment-question-outline fs-3 text-primary"></i>
                <h4 class="fs-20 fw-bold m-0">Quản lý FAQ</h4>
            </div>
        </div>

        <div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Thông tin liên hệ</h5>
    </div>
    <div class="card-body">
        <form action="" method="POST">
            @csrf
            {{-- Nếu bạn muốn cập nhật, dùng thêm: @method('PUT') --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email liên hệ</label>
                    <input type="email" name="email" id="email" class="form-control"
                        value="{{ old('email', $contact->email ?? '') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" id="phone" class="form-control"
                        value="{{ old('phone', $contact->phone ?? '') }}" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Lưu thông tin</button>
        </form>
    </div>
</div>


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách câu hỏi</h5>
                        <div>
                            <a href="{{route('admin.faqs.create')}}" class="btn btn-success shadow-sm">
                                + Thêm câu hỏi
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($faqs->isEmpty())
                            <p class="text-center text-muted">Chưa có câu hỏi nào.</p>
                        @else
                            <table class="table table-bordered mt-3">
                                <thead class="table">
                                    <tr>
                                        <th>#</th>
                                        <th>Câu hỏi</th>
                                        <th>Câu trả lờ</th>
                                        <th>Ngày tạo</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($faqs as $faq)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ Str::limit($faq->question, 60) }}</td>
                                            <td>
                                                {{ Str::limit(strip_tags($faq->answer), 100) }}
                                            </td>
                                            <td>{{ $faq->created_at->format('d/m/Y') }}</td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{route('admin.faqs.edit', $id = $faq->id)}}">
                                                                Chi tiết
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form action="{{route('admin.faqs.destroy')}}" method="POST"
                                                                onsubmit="return confirm('Bạn có chắc muốn xóa câu hỏi này?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <input type="hidden" name="id" value="{{$faq->id}}">
                                                                <button class="dropdown-item text-danger" type="submit">
                                                                    Xóa
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
                            <div class="d-flex justify-content-end mt-3">
                                {{ $faqs->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection