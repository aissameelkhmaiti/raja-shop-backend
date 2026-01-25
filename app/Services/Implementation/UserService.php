<?php

namespace App\Services\Implementation;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\UserServiceInterface;

class UserService implements UserServiceInterface
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): User
    {
        return $this->userRepository->create($data);
    }

    public function login(array $data): ?array
    {
        if (!Auth::attempt($data)) {
            return null;
        }

        $user = $this->userRepository->findByEmail($data['email']);

        // Générer OTP
        $otp = rand(100000, 999999);

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // Log OTP for debugging
        Log::info('OTP généré pour ' . $user->email . ': ' . $otp);

        // Envoyer OTP par email
        // Mail::raw(
        //     "Votre code de connexion est : $otp (valide 5 minutes)",
        //     function ($message) use ($user) {
        //         $message->to($user->email)
        //                 ->subject('Code OTP de connexion');
        //     }
        // );

        return [
            'message' => 'OTP envoyé par email',
            'otp_required' => true,
            'email' => $user->email,
        ];
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}
