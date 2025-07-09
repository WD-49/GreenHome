<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User; // Import User model
use Illuminate\Http\JsonResponse; // Import cho JsonResponse
use Illuminate\Http\Request; // Import cho Request
use Illuminate\Support\Facades\Auth; // Import cho Auth::guard()
use Illuminate\Support\Facades\Hash; // Import cho Hash::make()
use Illuminate\Support\Facades\Password; // Import cho Password::broker()
use Illuminate\Support\Str; // Import cho Str::random()
use Illuminate\Validation\ValidationException; // Import cho ValidationException
use Illuminate\Validation\Rules; // Import cho Rules\Password
use Illuminate\Auth\Events\PasswordReset; // Import cho PasswordReset event
use App\Jobs\SendResetPasswordMailJob; // Import Job của bạn (nếu bạn vẫn muốn dùng nó ở đây)


class ResetPasswordController extends Controller
{
    // KHÔNG SỬ DỤNG trait ResetsPasswords nữa

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/'; // Đường dẫn chuyển hướng sau khi đặt lại mật khẩu thành công

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request)
    {
        $token = $request->route()->parameter('token');

        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET
                            ? $this->sendResetResponse($request, $response)
                            : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return []; // Tùy chỉnh thông báo lỗi nếu cần
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
    }

    /**
     * Reset the given user's password.
     *
     * Đây là phương thức bạn đã ghi đè trước đó.
     * Lưu ý về dòng dispatch Job.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        event(new PasswordReset($user));

        // LƯU Ý QUAN TRỌNG:
        // Dòng này đang gửi email với MẬT KHẨU MỚI mà người dùng vừa nhập.
        // Điều này KHÔNG AN TOÀN và KHÔNG NÊN làm.
        // Nếu bạn muốn thông báo, hãy gửi một email khác (ví dụ: "Mật khẩu của bạn đã được thay đổi thành công").
        // Tốt nhất là BÌNH LUẬN HOẶC XÓA DÒNG NÀY:
        // dispatch(new SendResetPasswordMailJob($user->email, $password)); // <-- XÓA HOẶC BÌNH LUẬN NẾU KHÔNG CẦN THIẾT

        $this->guard()->login($user);
    }

    /**
     * Set the user's password.
     * Phương thức này thường được gọi bởi trait.
     * Do bạn đã ghi đè resetPassword, phương thức này có thể không được sử dụng
     * nếu bạn không gọi nó trong resetPassword().
     * Nếu bạn không cần nó, có thể xóa. Nếu bạn muốn tách logic đặt pass, hãy giữ lại.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function setUserPassword($user, $password)
    {
        $user->password = Hash::make($password);
    }


    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            return new JsonResponse(['message' => trans($response)], 200);
        }

        return redirect($this->redirectPath())
                            ->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }

        return redirect()->back()
                            ->withInput($request->only('email'))
                            ->withErrors(['email' => trans($response)]);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * Get the redirect path if a redirect path is not provided.
     * (Đây là phương thức từ trait RedirectsUsers, cần thiết cho $this->redirectPath())
     *
     * @return string
     */
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }
}