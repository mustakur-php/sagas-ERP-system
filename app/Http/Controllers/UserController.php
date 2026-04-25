<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;



class UserController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasPermission('view_users')) {
            abort(403, 'غير مصرح لك بعرض المستخدمين');
        }

        $users = User::with(['company', 'station'])->latest()->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        if (!auth()->user()->hasPermission('create_users')) {
            abort(403, 'غير مصرح لك بإضافة مستخدم');
        }

        $companies = Company::orderBy('name')->get();
        $stations = Station::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('users.create', compact('companies', 'stations', 'roles'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('create_users')) {
            abort(403, 'غير مصرح لك بإضافة مستخدم');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:6',
            'company_id' => 'required|exists:companies,id',
            'station_id' => 'nullable|exists:stations,id',
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $this->validateUserStationRules($request);
        $role = Role::find($request->role_id);

        if ($role && $role->slug === 'station_manager' && !$request->station_id) {
            return back()->withInput()->withErrors([
                'station_id' => 'يجب تحديد محطة لمشرف المحطة'
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'company_id' => $request->company_id,
            'station_id' => $request->station_id,
        ]);

        $user->roles()->sync($request->roles ?? []);


        return redirect()->route('users.index')->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function edit(User $user)
    {
        if (!auth()->user()->hasPermission('edit_users')) {
            abort(403, 'غير مصرح لك بتعديل المستخدمين');
        }
        
        $companies = Company::orderBy('name')->get();
        $stations = Station::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('users.edit', compact('user', 'companies', 'stations', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->hasPermission('edit_users')) {
            abort(403, 'غير مصرح لك بتعديل المستخدمين');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'company_id' => 'required|exists:companies,id',
            'station_id' => 'nullable|exists:stations,id',
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
            'password' => 'nullable|min:6',
        ]);
        $this->validateUserStationRules($request, $user);
        $role = Role::find($request->roles[]);

        if ($role && $role->slug === 'station_manager' && !$request->station_id) {
            return back()->withInput()->withErrors([
                'station_id' => 'يجب تحديد محطة لمشرف المحطة'
            ]);
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company_id' => $request->company_id,
            'station_id' => $request->station_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->roles()->sync($request->roles ?? []);

        return redirect()->route('users.index')->with('success', 'تم تحديث المستخدم');
    }

    private function validateUserStationRules(Request $request, ?User $user = null): void
    {
        $selectedRoles = $request->roles ?? [];

        $stationSupervisorRole = Role::where('slug', 'station_supervisor')->first();

        $isStationSupervisor = $stationSupervisorRole
            && in_array($stationSupervisorRole->id, $selectedRoles);

        if ($isStationSupervisor && !$request->station_id) {
            abort(redirect()->back()->withInput()->withErrors([
                'station_id' => 'يجب تحديد محطة لمشرف المحطة',
            ]));
        }

        if ($request->station_id) {
            $station = Station::find($request->station_id);

            if (!$station || (int) $station->company_id !== (int) $request->company_id) {
                abort(redirect()->back()->withInput()->withErrors([
                    'station_id' => 'المحطة المختارة لا تتبع الشركة المحددة',
                ]));
            }

            $query = User::where('station_id', $request->station_id);

            if ($user) {
                $query->where('id', '!=', $user->id);
            }

            if ($query->exists()) {
                abort(redirect()->back()->withInput()->withErrors([
                    'station_id' => 'هذه المحطة مرتبطة بمستخدم آخر',
                ]));
            }
        }
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->hasPermission('delete_users')) {
            abort(403, 'غير مصرح لك بحذف المستخدمين');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'تم حذف المستخدم');
    }
}