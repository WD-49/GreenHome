{{-- resources/views/emails/password_reset.blade.php --}}

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background-color: #f8f9fa;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-3">Xin chào,</h4>
                        <p class="card-text">
                            Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản liên kết với địa chỉ email:
                        </p>
                        <p><strong>{{ $email }}</strong></p>
                        <p class="mt-4">Vui lòng nhấp vào nút bên dưới để đặt lại mật khẩu của bạn:</p>

                        <div class="text-center my-4">
                            <a href="{{ $resetUrl }}" class="btn btn-primary btn-lg">Đặt lại mật khẩu</a>
                        </div>

                        <p class="text-muted">
                            Liên kết này sẽ hết hạn sau <strong>{{ config('auth.passwords.users.expire') }} phút</strong>.
                        </p>

                        <p class="mt-3">
                            Nếu bạn không yêu cầu đặt lại mật khẩu, bạn có thể bỏ qua email này một cách an toàn.
                        </p>

                        <p class="mt-4 mb-0">Trân trọng,<br><strong>{{ config('app.name') }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
