@extends('layouts.admin')

@section('title', 'Chỉnh sửa FAQ')

@section('content')
<div class="container mt-4">
    <h2>Chi tiết Câu Hỏi Thường Gặp (FAQ)</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Đã xảy ra lỗi!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="question">Câu hỏi</label>
            <input type="text" name="question" class="form-control" value="{{ old('question', $faq->question) }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="answer">Câu trả lời</label>
            <textarea name="answer" id="editor" style="min-height: 300px;" class="form-control" rows="6">{{ old('answer', $faq->answer) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection

@section('scripts')
    {{-- CKEditor CDN --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#editor'))
            .catch(error => {
                console.error(error);
            });
    </script>
@endsection
