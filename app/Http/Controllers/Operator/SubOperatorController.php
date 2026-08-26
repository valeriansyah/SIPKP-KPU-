<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Operator\StoreSubOperatorRequest;
use App\Http\Requests\Operator\UpdateSubOperatorRequest;
use App\Models\District;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SubOperatorController extends Controller
{
    // Middleware is applied in routes/web.php

    public function index()
    {
        $subOperators = User::with(['district', 'role'])
            ->whereHas('role', function ($query) {
                // Ensure we only list sub operators, no matter what the role name exactly is (using LIKE or exact match)
                $query->where('role_name', 'Sub Operator')
                      ->orWhere('role_name', 'sub_operator');
            })
            ->latest()
            ->paginate(10);
            
        return view('operator.master-data.sub-operators.index', compact('subOperators'));
    }

    public function create()
    {
        $districts = District::orderBy('name')->get();
        return view('operator.master-data.sub-operators.create', compact('districts'));
    }

    public function store(StoreSubOperatorRequest $request)
    {
        $role = Role::where('role_name', 'Sub Operator')->orWhere('role_name', 'sub_operator')->firstOrFail();
        
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = $role->id;
        
        // Auto-generate username from email if needed
        $data['username'] = explode('@', $data['email'])[0] . '_' . rand(100, 999);

        User::create($data);
        return redirect()->route('operator.master-data.sub-operators.index')->with('success', 'Akun Sub Operator berhasil dibuat.');
    }

    public function edit(User $subOperator)
    {
        // Security check to ensure the user being edited is actually a sub operator
        if (!Str::contains(strtolower($subOperator->role->role_name), 'sub operator') && !Str::contains(strtolower($subOperator->role->role_name), 'sub_operator')) {
            abort(403, 'Akses ditolak: User bukan Sub Operator.');
        }

        $districts = District::orderBy('name')->get();
        return view('operator.master-data.sub-operators.edit', compact('subOperator', 'districts'));
    }

    public function update(UpdateSubOperatorRequest $request, User $subOperator)
    {
        if (!Str::contains(strtolower($subOperator->role->role_name), 'sub operator') && !Str::contains(strtolower($subOperator->role->role_name), 'sub_operator')) {
            abort(403, 'Akses ditolak: User bukan Sub Operator.');
        }

        $data = $request->validated();
        
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $subOperator->update($data);
        return redirect()->route('operator.master-data.sub-operators.index')->with('success', 'Akun Sub Operator berhasil diperbarui.');
    }
}
