<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display the users list(index) view.
     *
     * @return \Illuminate\View\View
    */

    public function index()
    {
        return view('user');
    }


    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = Role::all();
        return view('auth.register', compact('roles'));
    }

    /**
     * Handle an incoming registration request with assigned role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->assignRole($request->role);

        return redirect('/users');
    }

    /**
     * Display the edit view with associated user 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function edit(Request $request)
    {
        $roles = Role::all();
        $user = User::findOrFail($request->id);
        return view('edit', compact(['user', 'roles']));
    }

    /**
     * Handle an incoming update request with assigned role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => Rule::unique('users')->ignore($request->id),
        ]);
        $user = User::findOrFail($request->id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        $role = Role::where('id', $request->role)->first();
        $user->syncRoles($role);

        return redirect('/users');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect('/users');
    }
    
    public function getUsers(Request $request)
    {
        $users = User::latest()->get();
        if($request->ajax()) {
            
            return Datatables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function($user){
                    $auth_user = Auth::user();
                    if($auth_user->can('edit users') || $auth_user->can('delete users')){
                        $actionBtn = '<a href="http://localhost:8000/edit/user/'.$user->id .'" class="edit btn btn-success btn-sm">Edit</a> 
                        <a href="http://localhost:8000/delete/user/'.$user->id .'" class="delete btn btn-danger btn-sm">Delete</a>';
                        return $actionBtn;
                    }
                    
                    $actionBtn = '<a href="#" class="edit btn disabled btn-success btn-sm">Edit</a> 
                        <a href="#" class="delete btn disabled btn-danger btn-sm">Delete</a>';
                        return $actionBtn;
                })
                ->addColumn('role', function($user){
                    $current_user = $user->getRoleNames();
                    return $current_user[0];
                })
                ->rawColumns(['action'])
                ->make(true); 
        }
        return $users;
    }
}
