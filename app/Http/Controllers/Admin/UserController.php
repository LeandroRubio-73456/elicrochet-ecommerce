<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = \App\Models\User::query();

            // 1. Búsqueda Global
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $searchValue = $request->input('search.value');
                $query->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%")
                      ->orWhere('email', 'like', "%{$searchValue}%");
                });
            }

            // 2. Filtro por Rol (Columna 4)
            if ($request->has('columns')) {
                $roleSearch = $request->input("columns.4.search.value");
                if (!empty($roleSearch) && in_array($roleSearch, ['admin', 'customer'])) {
                    $query->where('role', $roleSearch);
                }
            }

            // 3. Ordenamiento
            if ($request->has('order')) {
                $orderColumnIndex = $request->input('order.0.column');
                $orderDirection = $request->input('order.0.dir');
                $columns = ['id', 'avatar', 'name', 'email', 'role', 'created_at', 'actions'];
                
                if (isset($columns[$orderColumnIndex]) && !in_array($columns[$orderColumnIndex], ['actions', 'avatar'])) {
                    $query->orderBy($columns[$orderColumnIndex], $orderDirection);
                }
            } else {
                $query->latest();
            }

            // 4. Paginación
            $totalRecords = \App\Models\User::count();
            $filteredRecords = $query->count();
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            
            $users = $query->skip($start)->take($length)->get();

            // 5. Transformación
            $data = $users->map(function ($user) {
                // Avatar
                $avatar = '<div class="avatar avatar-s bg-light-primary text-primary">' . strtoupper(substr($user->name, 0, 1)) . '</div>';
                
                // Rol Badge
                $roleBadge = match($user->role) {
                    'admin' => '<span class="badge bg-light-danger f-12">Admin</span>',
                    'customer' => '<span class="badge bg-light-success f-12">Cliente</span>',
                    default => '<span class="badge bg-light-secondary f-12">' . $user->role . '</span>',
                };

                // Acciones
                $editUrl = route('admin.users.edit', $user->id);
                $deleteUrl = route('admin.users.destroy', $user->id);
                
                $actions = '
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="' . $editUrl . '" class="btn btn-outline-primary"><i class="ti-pencil"></i></a>';
                
                if ($user->id !== auth()->id()) {
                    $actions .= '
                        <button type="button" class="btn btn-outline-danger delete-user-btn"
                            data-user-id="' . $user->id . '"
                            data-action-url="' . $deleteUrl . '"
                            data-user-name="' . $user->name . '">
                            <i class="ti-trash"></i>
                        </button>';
                }
                
                $actions .= '</div>';

                return [
                    'id' => $user->id,
                    'avatar' => $avatar,
                    'name' => '<h6 class="mb-0">' . $user->name . '</h6>',
                    'email' => $user->email,
                    'role' => $roleBadge,
                    'created_at' => $user->created_at->format('d/m/Y'),
                    'actions' => $actions
                ];
            });

            return response()->json([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                "data" => $data
            ]);
        }

        return view('back.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('back.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'role' => ['required', 'in:admin,customer'],
        ]);

        \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Not used for now, maybe later for detailed profile view
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = \App\Models\User::findOrFail($id);
        return view('back.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = \App\Models\User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,customer'],
            'password' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        // Prevent deleting self
        if ($user->id === auth()->id()) {
             return response()->json(['message' => 'No puedes eliminar tu propia cuenta.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente.']);
    }
}
