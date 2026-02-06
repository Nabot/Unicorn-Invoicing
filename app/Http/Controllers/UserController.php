<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    const MAX_USERS_PER_COMPANY = 2;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $companyId = $request->user()->company_id;

        $query = User::where('company_id', $companyId)
            ->withCount(['createdInvoices as total_invoices'])
            ->withSum(['createdInvoices as total_revenue' => function($q) {
                $q->where('status', \App\Enums\InvoiceStatus::PAID);
            }], 'total');

        // Sorting
        $sortBy = $request->get('sort_by', 'name_asc');
        switch ($sortBy) {
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'email_asc':
                $query->orderBy('email', 'asc');
                break;
            case 'email_desc':
                $query->orderBy('email', 'desc');
                break;
            case 'recent':
                $query->orderBy('created_at', 'desc');
                break;
            case 'name_asc':
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage)->withQueryString();

        // Get current user count
        $currentUserCount = User::where('company_id', $companyId)->count();
        $canAddMore = $currentUserCount < self::MAX_USERS_PER_COMPANY;
        $maxUsers = self::MAX_USERS_PER_COMPANY;

        return view('users.index', compact('users', 'sortBy', 'currentUserCount', 'canAddMore', 'maxUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $companyId = $request->user()->company_id;
        $currentUserCount = User::where('company_id', $companyId)->count();

        if ($currentUserCount >= self::MAX_USERS_PER_COMPANY) {
            return redirect()->route('users.index')
                ->with('error', 'Maximum user limit reached. You can only have ' . self::MAX_USERS_PER_COMPANY . ' users per company.');
        }

        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $companyId = $request->user()->company_id;
        $currentUserCount = User::where('company_id', $companyId)->count();

        // Check user limit
        if ($currentUserCount >= self::MAX_USERS_PER_COMPANY) {
            return redirect()->route('users.index')
                ->with('error', 'Maximum user limit reached. You can only have ' . self::MAX_USERS_PER_COMPANY . ' users per company.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_id' => $companyId,
        ]);

        // Assign role
        $role = Role::findByName($validated['role']);
        $user->assignRole($role);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, User $user): View
    {
        $companyId = $request->user()->company_id;
        
        // Ensure user belongs to the same company
        if ($user->company_id !== $companyId) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::all();
        $user->load('roles');
        
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $companyId = $request->user()->company_id;
        
        // Ensure user belongs to the same company
        if ($user->company_id !== $companyId) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        // Update role
        $role = Role::findByName($validated['role']);
        $user->syncRoles([$role]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        $companyId = $request->user()->company_id;
        
        // Ensure user belongs to the same company
        if ($user->company_id !== $companyId) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent users from deleting themselves
        if ($user->id === $request->user()->id) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Find an admin user in the same company to reassign invoices
        $adminUser = User::where('company_id', $companyId)
            ->whereHas('roles', function($query) {
                $query->where('name', 'Admin');
            })
            ->where('id', '!=', $user->id) // Don't select the user being deleted
            ->first();

        // If no admin found, use the current user (the one performing the deletion)
        if (!$adminUser) {
            $adminUser = $request->user();
        }

        // Reassign all invoices created by this user to the admin user
        $invoiceCount = $user->createdInvoices()->count();
        if ($invoiceCount > 0) {
            $user->createdInvoices()->update(['created_by' => $adminUser->id]);
        }

        // Reassign all payments recorded by this user to the admin user
        $paymentCount = $user->recordedPayments()->count();
        if ($paymentCount > 0) {
            $user->recordedPayments()->update(['created_by' => $adminUser->id]);
        }

        $user->delete();

        $message = 'User deleted successfully.';
        if ($invoiceCount > 0 || $paymentCount > 0) {
            $reassigned = [];
            if ($invoiceCount > 0) {
                $reassigned[] = $invoiceCount . ' invoice(s)';
            }
            if ($paymentCount > 0) {
                $reassigned[] = $paymentCount . ' payment(s)';
            }
            $message .= ' ' . implode(' and ', $reassigned) . ' reassigned to ' . $adminUser->name . '.';
        }

        return redirect()->route('users.index')
            ->with('success', $message);
    }
}
