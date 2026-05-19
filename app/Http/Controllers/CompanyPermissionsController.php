<?php

namespace App\Http\Controllers;

use App\Lib\Enumerations\GeneralStatus;
use App\Models\Company;
use App\Models\Employee;
use App\Models\CompanyPermissions;
use App\Models\Module;
use App\Models\GroupedMenuRoutePermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class CompanyPermissionsController extends Controller
{

    public function index()
    {
        $data = User::with('employeeDetails')
            ->whereHas('PermittedCompanies')
            ->with('PermittedCompanies.company')
            ->get();

        return view('admin.permissions.company-permissions.index', compact('data'));
    }

    public function create()
    {
        $employees = Employee::with('user')
            ->where('status', GeneralStatus::ACTIVE)
            ->where('user_id', '!=', auth()->id())
            ->whereHas('user')
            ->get();

        $companies = Company::where('status', GeneralStatus::ACTIVE)->get();
        $modules = Module::all();

        return view('admin.permissions.company-permissions.form', compact('employees', 'companies', 'modules'));
    }

    public function getPermissions(Request $request)
    {
        $userId = $request->user_id;
        $companyId = $request->company_id;

        // Get existing permissions for this user and company
        $existingPermissions = CompanyPermissions::where('user_id', $userId)
            ->where('company_id', $companyId)
            ->pluck('permission_name')
            ->toArray();

        // Get all modules with their permission groups
        $groupPms = GroupedMenuRoutePermission::with('module')
            ->select('menu_name', 'module_id')
            ->groupBy('menu_name')
            ->groupBy('module_id')
            ->get();

        $modules = Module::all();

        $actionTypeColors = [
            'CREATE' => 'text-success',
            'READ' => 'text-primary',
            'UPDATE' => 'text-warning',
            'DELETE' => 'text-danger',
        ];

        return view('admin.permissions.company-permissions.permission_tree')->with([
            'menus' => $groupPms,
            'modules' => $modules,
            'existing_permissions' => $existingPermissions,
            'actionTypeColors' => $actionTypeColors
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:user,id',
            'company_id' => 'required|exists:companies,id',
            'permission' => 'nullable|array',
            'permission.*' => 'string',
        ]);

        $createdBy = auth()->id();
        $createdAt = now();

        DB::beginTransaction();
        try {
            // Delete existing permissions for this user and company
            CompanyPermissions::where('user_id', $request->user_id)
                ->where('company_id', $request->company_id)
                ->delete();

            // Insert new permissions
            $data = [];
            if ($request->has('permission') && is_array($request->permission)) {
                foreach ($request->permission as $permissionName) {
                    $data[] = [
                        'user_id' => $request->user_id,
                        'company_id' => $request->company_id,
                        'permission_name' => $permissionName,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                        'created_by' => $createdBy,
                        'updated_by' => $createdBy,
                    ];
                }
            }

            if (!empty($data)) {
                CompanyPermissions::insert($data);
            }

            DB::commit();
            return redirect()->route('company.permissions.index')->with('success', 'Company permissions successfully assigned.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $employees = Employee::with('user')
            ->where('status', GeneralStatus::ACTIVE)
            ->where('user_id', '!=', auth()->id())
            ->whereHas('user')
            ->get();

        $companies = Company::where('status', GeneralStatus::ACTIVE)->get();

        // Get the user and company from the first permission record
        $firstPermission = CompanyPermissions::where('user_id', $id)->first();
        $editModeData = [
            'user_id' => $id,
            'company_id' => $firstPermission ? $firstPermission->company_id : null,
        ];

        return view('admin.permissions.company-permissions.form', compact('employees', 'companies', 'editModeData'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:user,id',
            'company_id' => 'required|exists:companies,id',
            'permission' => 'nullable|array',
            'permission.*' => 'string',
        ]);

        $updatedBy = auth()->id();
        $updatedAt = now();

        DB::beginTransaction();
        try {
            // Delete existing permissions for this user and company
            CompanyPermissions::where('user_id', $request->user_id)
                ->where('company_id', $request->company_id)
                ->delete();

            // Insert new permissions
            $data = [];
            if ($request->has('permission') && is_array($request->permission)) {
                foreach ($request->permission as $permissionName) {
                    $data[] = [
                        'user_id' => $request->user_id,
                        'company_id' => $request->company_id,
                        'permission_name' => $permissionName,
                        'created_at' => $updatedAt,
                        'updated_at' => $updatedAt,
                        'created_by' => $updatedBy,
                        'updated_by' => $updatedBy,
                    ];
                }
            }

            if (!empty($data)) {
                CompanyPermissions::insert($data);
            }

            DB::commit();
            return redirect()->route('company.permissions.index')->with('success', 'Company permissions successfully updated.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $data = CompanyPermissions::where('user_id', $id)->get();
            foreach ($data as $item) {
                $item->forceDelete();
            }
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            echo "success";
        } elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }

    public function destroy($id)
    {
        //
    }
}
