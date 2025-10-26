<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminMitraRegistrationNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Add Log facade
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:mitra,admin',
            'phone' => 'required|string|max:20', // Add phone validation
            'company' => 'nullable|string|max:255', // Add company validation
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone, // Add phone to user creation
            'company' => $request->company, // Add company to user creation
        ]);

        event(new Registered($user));

        // If the new user is a mitra, notify all admins
        if ($user->isMitra()) {
            // Get all admin users
            $admins = User::where('role', 'admin')->get();
            
            // Send notification to each admin
            foreach ($admins as $admin) {
                $admin->notify(new AdminMitraRegistrationNotification($user));
            }
            
            // Also send email notification to admins via Gmail service
            try {
                $gmail = new \App\Services\GmailService();
                
                // Send to each admin
                foreach ($admins as $admin) {
                    $gmail->sendEmail(
                        $admin->email,
                        'Pendaftaran Mitra Baru: ' . $user->name,
                        view('emails.admin.mitra-registration', ['mitra' => $user])->render()
                    );
                }
            } catch (\Exception $e) {
                Log::error('Gagal kirim notifikasi Gmail untuk mitra baru: '.$e->getMessage()); // Use Log facade
            }
        }

        Auth::login($user);

        // Redirect to appropriate dashboard based on role
        if ($user->isAdmin()) {
            return redirect(route('admin.dashboard', absolute: false));
        }

        return redirect(route('dashboard', absolute: false));
    }
}