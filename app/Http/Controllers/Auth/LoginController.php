<?php



    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Mail\TwoFactorCodeMail;
    use App\Notifications\TwoFactorCodeNotification;
    use App\Providers\RouteServiceProvider;
    use Carbon\Carbon;
    use Illuminate\Foundation\Auth\AuthenticatesUsers;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Log;

    class LoginController extends Controller
    {
        use AuthenticatesUsers;

        protected $redirectTo = '/home';

        public function authenticated(Request $request, $user)
        {
            // Check if 2FA is enabled
            if ($user->two_factor_enabled) {
                // If the user hasn't verified the 2FA code or it's expired, generate a new one
                if (is_null($user->two_factor_code) || $user->two_factor_expires_at < Carbon::now()) {
                    $user->two_factor_code = rand(100000, 999999);
                    $user->two_factor_expires_at = Carbon::now()->addMinutes(10); // Code expires in 10 minutes
                    $user->save();

                    try {
                        Mail::to($user->email)->send(new TwoFactorCodeMail($user->two_factor_code, $user));

                        activity()
                            ->causedBy($user)
                            ->withProperties([
                                'email' => $user->email,
                            ])
                            ->log('Two-factor authentication code sent successfully.');
                    } catch (\Exception $e) {
                        Log::error('Failed to send two-factor authentication code', [
                            'user_id' => $user->id,
                            'email' => $user->email,
                            'error_message' => $e->getMessage(),
                        ]);

                        activity()
                        ->causedBy($user)
                        ->withProperties([
                            'email' => $user->email,
                            'error_message' => $e->getMessage(),
                        ])
                        ->log('Failed to send two-factor authentication code.');
                    }
                }

                // Redirect to the 2FA verification page
                return redirect()->route('2fa.verify');
            }

            // If 2FA is not enabled, proceed with the regular login
            return redirect()->intended($this->redirectPath());
        }

        public function show2faForm()
        {
            return view('auth.2fa_verify');
        }

        public function verifyTwoFactor(Request $request)
        {
            $request->validate([
                'two_factor_code' => 'required|numeric',
            ]);

            $user = auth()->user();

            // Check if the code matches and isn't expired
            if ($request->two_factor_code == $user->two_factor_code && $user->two_factor_expires_at > Carbon::now()) {
                // Mark 2FA as verified (optional, to prevent re-verification)
                $user->two_factor_code = null;
                $user->two_factor_expires_at = null;
                $user->save();

                // Redirect to the intended page after successful 2FA
                return redirect()->intended($this->redirectPath());
            }

            // If the code is invalid or expired, return an error
            return back()->withErrors(['two_factor_code' => 'The 2FA code is invalid or expired.']);
        }
    }
