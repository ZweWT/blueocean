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

class UserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
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

        return redirect('/data');
    }

    public function edit(Request $request)
    {
        $user = User::findOrFail($request->id);
        return view('edit', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => Rule::unique('users')->ignore($request->id),
        ]);
        $user = User::findOrFail($request->id);  
        $user->update($request->all());

        return redirect('/data');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect('/data');
    }
    
    public function getUsers(Request $request)
    {
        $users = User::latest()->get();
        if($request->ajax()) {
            
            return Datatables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function($user){
                    $actionBtn = '<a href="http://localhost:8000/edit/user/'.$user->id .'" class="edit btn btn-success btn-sm">Edit</a> 
                    <a href="http://localhost:8000/delete/user/'.$user->id .'" class="delete btn btn-danger btn-sm">Delete</a>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true); 
        }
        return $users;
    }
}
