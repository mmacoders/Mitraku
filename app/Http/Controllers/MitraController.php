<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MitraController extends Controller
{
    /**
     * Display a listing of the mitra.
     */
    public function index()
    {
        // Get all users with role 'mitra'
        $mitra = User::where('role', 'mitra')
            ->withCount('pksSubmissions')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'company' => $user->company ?? null,
                    'pks_count' => $user->pks_submissions_count,
                    'created_at' => $user->created_at->toISOString(),
                ];
            });

        return Inertia::render('admin/kelola-mitra/Index', [
            'mitra' => $mitra,
        ]);
    }

    /**
     * Store a newly created mitra in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'company' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $mitra = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'company' => $request->company,
            'password' => bcrypt($request->password),
            'role' => 'mitra',
        ]);

        return redirect()->back()->with('success', 'Mitra created successfully');
    }

    /**
     * Update the specified mitra in storage.
     */
    public function update(Request $request, User $mitra)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $mitra->id,
            'company' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $mitra->update([
            'name' => $request->name,
            'email' => $request->email,
            'company' => $request->company,
            'password' => $request->password ? bcrypt($request->password) : $mitra->password,
        ]);

        return redirect()->back()->with('success', 'Mitra updated successfully');
    }

    /**
     * Remove the specified mitra from storage.
     */
    public function destroy(User $mitra)
    {
        $mitra->delete();

        return redirect()->back()->with('success', 'Mitra deleted successfully');
    }
}