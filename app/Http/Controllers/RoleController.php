<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Actions\Role\CreateRole;
use App\Actions\Role\UpdateRole;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\RoleFormRequest;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Session;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::with('permissions')->latest()->get();
        return view('role.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //abort_if(!userCan('role.delete'), 403);

        $permissions = Permission::all();
        // $permission_groups = User::getPermissionGroup();
        return view('role.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //abort_if(!userCan('role.delete'), 403);

        $request->validate([
            'name' =>'required|unique:roles,name',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->has('permissions')) {
            // Fetch permissions by their IDs
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        }

        session()->flash('success', 'Role Created!');
        return redirect()->route('roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //abort_if(!userCan('role.delete'), 403);

        $permissions = Permission::all();
        $role = Role::with('permissions')->find($id);
        $data = $role->permissions()->pluck('id')->toArray();

        return view('role.edit', compact(['permissions', 'role', 'data']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


     public function update(Request $request, Role $role)
     {

        //abort_if(!userCan('role.delete'), 403);

         // Validate the request
         $request->validate([
             'name' => 'required|unique:roles,name,' . $role->id,
         ]);

         // Update the role name
         $role->update(['name' => $request->name]);

         // Check if permissions are provided in the request
         if ($request->has('permissions')) {
             // Fetch permissions by their IDs to ensure they exist
             $permissions = Permission::whereIn('id', $request->permissions)->get();

             // Sync the fetched permissions with the role
             $role->syncPermissions($permissions);
         } else {
             // If no permissions are provided, sync with an empty array to remove existing permissions
             $role->syncPermissions([]);
         }

         // Flash a success message
         session()->flash('success', 'Role has been updated successfully!');

         // Redirect back to the previous page
         return redirect()->route('roles.index');
     }

    public function update_old(Request $request, Role $role)
    {
        //abort_if(!userCan('role.update'), 403);

        $request->validate([
            'name' =>'required|unique:roles,name,' . $role->id,
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        session()->flash('success', 'Role has been updated successfully!');
        return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        //abort_if(!userCan('role.delete'), 403);

        $role->delete();
        session()->flash('success', 'Role has been deleted successfully!');
        return redirect()->route('roles.index');

    }
}
