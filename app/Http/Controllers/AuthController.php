<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\Request;
use App\Models\User;
use App\Events\AppNotificationEvent;
use App\Enums\NotificationType;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    protected UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    // ----------- REGISTER -----------
    public function register(RegisterRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $user = $this->userService->register($validatedData);
            $token = $user->createToken('auth_token')->plainTextToken;

            event(new AppNotificationEvent([
                'user_id' => $user->id,
                'type'    => NotificationType::REGISTER->value,
                'title'   => 'Inscription réussie',
                'message' => 'Bienvenue ! Votre compte a été créé avec succès.',
                'ip'      => request()->ip(),
            ]));

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred during registration.',
            ], 500);
        }
    }

    // ----------- LOGIN -----------
    public function login(LoginRequest $request)
    {
        $result = $this->userService->login(
            $request->only(['email', 'password'])
        );

        if (!$result) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json($result);
    }

    // ----------- VERIFY OTP -----------
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->otp_code !== $request->otp || $user->otp_expires_at < now()) {
            return response()->json(['message' => 'OTP invalide ou expiré'], 401);
        }

        $user->update(['otp_code' => null, 'otp_expires_at' => null]);
        $token = $user->createToken('auth_token')->plainTextToken;

        event(new AppNotificationEvent([
            'user_id' => $user->id,
            'type'    => NotificationType::LOGIN->value,
            'title'   => 'Connexion réussie',
            'message' => 'Vous êtes connecté avec succès',
            'ip'      => request()->ip(),
        ]));

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // ----------- FETCH AUTH USER -----------
    public function fetchUser(Request $request)
    {
        return response()->json($request->user());
    }

    // ----------- UPDATE USER -----------
  

public function updateUser(Request $request)
{
    $user = $request->user();

    $validatedData = $request->validate([
        'name'     => 'sometimes|string|max:255',
        'email'    => 'sometimes|email|unique:users,email,' . $user->id,
        'password' => 'sometimes|min:8|confirmed',
        'phone'    => 'sometimes|string|max:20',
        'address'  => 'sometimes|string|max:255',
        'role'     => 'sometimes|in:customer,freelancer,admin',
        'avatar'   => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if (isset($validatedData['password'])) {
        $validatedData['password'] = bcrypt($validatedData['password']);
    }

    //  Avatar en local
    if ($request->hasFile('avatar')) {

        // Supprimer l'ancien avatar
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Stocker le nouveau
        $path = $request->file('avatar')->store(
            'avatars',
            'public'
        );

        $validatedData['avatar'] = $path;
    }

    // 🔒 Sécurité du rôle
    if ($request->has('role') && $user->role !== 'admin') {
        unset($validatedData['role']);
    }

    $user->update($validatedData);

    event(new AppNotificationEvent([
        'user_id' => $user->id,
        'type'    => NotificationType::UPDATE_PROFILE->value,
        'title'   => 'Profil mis à jour',
        'message' => 'Votre profil a été mis à jour avec succès.',
        'ip'      => request()->ip(),
    ]));

    return response()->json([
        'message' => 'Profil mis à jour avec succès',
        'user'    => $user,
    ]);
}


    // ----------- LOGOUT -----------
    public function logout(Request $request)
    {
        $this->userService->logout($request->user());

        return response()->json(['message' => 'Logged out successfully']);
    }
}
