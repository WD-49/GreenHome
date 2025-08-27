@extends('layouts.app')

@section('content')
    <!-- Breadcrumb -->
    <section class="section-breadcrumb">
        <div class="cr-breadcrumb-image">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="cr-breadcrumb-title">
                            <h2>Trung tâm trợ giúp</h2>
                            <span><a href="{{ route('home') }}">Trang chủ</a> - FAQ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
 <section class="section-faq py-5 bg-light">
    <div class="container">
        <h3 class="text-center mb-5 fw-bold">Câu hỏi thường gặp</h3>

        <div class="row justify-content-center">
            <div class="col-lg-10 col-12">
                <div class="accordion" id="faqAccordion">
                    @forelse($faqs as $faq)
                        <div class="accordion-item mb-3 shadow-sm rounded">
                            <h2 class="accordion-header" id="heading{{ $faq->id }}">
                                <button class="accordion-button collapsed fw-medium" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $faq->id }}" aria-expanded="false"
                                        aria-controls="collapse{{ $faq->id }}">
                                    {{ $faq->question }}
                                </button>
                            </h2>
                            <div id="collapse{{ $faq->id }}" class="accordion-collapse collapse"
                                 aria-labelledby="heading{{ $faq->id }}" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    {!! $faq->answer !!}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Hiện chưa có câu hỏi nào.</p>
                    @endforelse
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $faqs->links() }}
                </div>
            </div>
        </div>

        <!-- Form hỗ trợ -->
        <div class="row justify-content-center mt-5">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-3">Bạn cần thêm hỗ trợ?</h4>
                        <p class="text-muted mb-4">Hãy gửi câu hỏi của bạn, chúng tôi sẽ phản hồi sớm nhất.</p>
                        <form action="" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Họ và tên</label>
                                <input type="text" name="name" class="form-control" placeholder="Nhập họ và tên" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email liên hệ</label>
                                <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Nội dung cần hỗ trợ</label>
                                <textarea name="message" rows="5" class="form-control" placeholder="Mô tả vấn đề của bạn..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary px-4">Gửi hỗ trợ</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
