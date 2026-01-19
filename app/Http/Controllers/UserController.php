<?php

namespace App\Http\Controllers;

use App\Mail\UserNotification;
use App\Models\Hotel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Start query with role and hotel eager loading
        $query = User::with(['role', 'hotel']);

        // Filter by role if selected
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'archived') {
                $query->where('is_active', false);
            }
        }

        // Global search (name, email, phone)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Pagination (10 per page)
        $users = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        // For filter dropdowns
        $roles = Role::orderBy('name')->get();
        $statuses = ['active' => 'Active', 'archived' => 'Archived'];

        return view('pages.erp.users.index', compact('users', 'roles', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $hotels = Hotel::orderBy('name')->get();

        return view('pages.erp.users.create', compact('roles', 'hotels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'role_id' => 'required|exists:roles,id',
            'hotel_id' => 'required|exists:hotels,id',
            'password' => 'required|string|confirmed|min:6',
            'is_active' => 'required|boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'hotel_id' => $request->hotel_id,
            'is_active' => $request->is_active,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' created successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    public function archive(User $user)
    {
        $user->update(['is_active' => false]);

        return redirect()->route('users.index')
            ->with('success', "User {$user->name} archived successfully.");
    }

    public function restore(User $user)
    {
        $user->update(['is_active' => true]);

        return redirect()->route('users.index')
            ->with('success', "User {$user->name} restored successfully.");
    }

    public function sendmail()
    {
        $users = User::all();
        foreach ($users as $key => $user) {

            Mail::to($user->email)->queue(new UserNotification($user));
        }
        return "mail send succecefully";
    }
}
