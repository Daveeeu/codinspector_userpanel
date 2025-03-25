<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Providers\RouteServiceProvider;
    use App\Models\User;
    use Illuminate\Foundation\Auth\RegistersUsers;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;

    class RegisterController extends Controller
    {
        use RegistersUsers;

        protected $redirectTo = RouteServiceProvider::HOME;

        public function __construct()
        {
            $this->middleware('guest');
        }

        protected function validator(array $data)
        {
            return Validator::make($data, [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone_number' => ['required', 'string', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'accepted_privacy_policy' => ['required', 'accepted'],
                'accepted_terms_of_service' => ['required', 'accepted'],
            ], [
                'accepted_privacy_policy.accepted' => 'Az Adatvédelmi Szabályzat elfogadása kötelező.',
                'accepted_terms_of_service.accepted' => 'Az ÁSZF elfogadása kötelező.',
            ]);
        }

        protected function create(array $data)
        {
            return User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'],
                'password' => Hash::make($data['password']),
                'accepted_privacy_policy' => isset($data['accepted_privacy_policy']),
                'accepted_terms_of_service' => isset($data['accepted_terms_of_service']),
                'referral_code' => null,
            ]);
        }
    }
