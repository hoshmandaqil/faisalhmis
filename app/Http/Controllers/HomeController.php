<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function users()
    {
        $users = User::latest()->get();
        return view('user.users', compact('users'));
    }

    public function register_user(Request $request)
    {
        if (!in_array('user_add', user_permissions())){
            return view('access_denied');
        }
        if ($request->password != $request->confirm_password){
            return redirect()->back()->with('alert','Password didnt match')->with('alert-type','alert-danger');
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->type = $request->type;
        $user->password = Hash::make($request->password);
         $user->OPD_fee = $request->OPD_fee;
              $user->attendance_id = $request->attendance_id;
         $user->check_in = $request->check_in;
         $user->check_out = $request->check_out;
        if ($user->save()){

            // If the registered user is Admin, then we set permission to all for this user.
            if ($request->type == 1){
                $permissions = DB::table('permissions')->get();
                foreach ($permissions as $perm){
                    DB::table('user_permissions')->insert(['user_id' => $user->id, 'permission_id' => $perm->id]);
                }
            }

        }
        return redirect()->back()->with('alert','The User has been created!')->with('alert-type','alert-success');
    }

    public function edit_user(Request $request, $id)
    {
        if (!in_array('user_edit', user_permissions())){
            return view('access_denied');
        }
        $user = User::findorfail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->type = $request->type;
         $user->OPD_fee = $request->OPD_fee;
              $user->attendance_id = $request->attendance_id;
         $user->check_in = $request->check_in;
         $user->check_out = $request->check_out;
        if ($request->password != null && $request->confirm_password != null){
            if ($request->password != $request->confirm_password){
                return redirect()->back()->with('alert','Password didnt match')->with('alert-type','alert-danger');
            }
            else {
                $user->password = Hash::make($request->password);
            }
        }
        $user->save();
        return redirect()->back()->with('alert','The User has been Successfully Edited')->with('alert-type','alert-info');
    }

    public function deactivate_user($id)
    {
        if (!in_array('user_deactivate', user_permissions())){
            return view('access_denied');
        }
        $user = User::findorfail($id);
        $user->status = 0;
        $user->save();
        return redirect()->back()->with('alert','The User has been Successfully Deactivated!')->with('alert-type','alert-info');

    }

    public function delete_user($id)
    {
        if (!in_array('user_delete', user_permissions())){
            return view('access_denied');
        }
        $user = User::findorfail($id);
        $user->delete();
        return redirect()->back()->with('alert','The User has been Successfully Deleted!')->with('alert-type','alert-info');
    }

    public function activate_user($id)
    {
        $user = User::findorfail($id);
        $user->status = 1;
        $user->save();
        return redirect()->back()->with('alert','The User has been Successfully Activated!')->with('alert-type','alert-info');
    }

    public function set_permission($id)
    {
        $permissionGroup = Permission::groupBy('permission_group')->pluck('permission_group', 'id');
        $permissionArray = [];
        foreach ($permissionGroup as $perm_group){
             $permList = Permission::where('permission_group', $perm_group)->get();
             $permissionArray[$perm_group] = $permList;
        }
        $userPermission = UserPermission::where('user_id', $id)->pluck('permission_id', 'id')->toArray();
        return view('user.permission_list', compact('permissionArray', 'id', 'userPermission'));
    }

    public function save_permissions(Request $request)
    {
        $userId = $request->user_id;
        $permissions = $request->permissions;

        // Delete previous Permissions.
        DB::table('user_permissions')->where('user_id', $userId)->delete();
        foreach ($permissions as $permission){
            DB::table('user_permissions')->insert(['user_id' => $userId, 'permission_id' => $permission]);
        }
        return redirect(url('users'))->with('alert','Permission has been set Successfully!')->with('alert-type','alert-info');

    }
    
    
     public function change_password()
    {
        return view('user.change_password');
    }

    public function save_change_password(Request $request)
    {
        $user = User::findorfail(Auth::user()->id);
        if ($request->new_password != null && $request->confirm_password != null){
            if ($request->new_password != $request->confirm_password){
                return redirect()->back()->with('alert','Password didnt match')->with('alert-type','alert-danger');
            }
            else {
                $user->password = Hash::make($request->new_password);
                $user->save();
                return redirect()->back()->with('alert','The User has been Successfully Edited')->with('alert-type','alert-info');
            }
        }
        return redirect()->back()->with('alert','Fields are reqquired!')->with('alert-type','alert-danger');

    }
}
