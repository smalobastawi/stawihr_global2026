<?php

namespace App\Http\Controllers\Disciplinary;

use App\Models\DisciplinaryCategory;
use App\Http\Requests\StoreDisciplinaryCategoryRequest;
use App\Http\Requests\UpdateDisciplinaryCategoryRequest;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DisciplinaryCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     *  @return \Illuminate\View\View
     */
    public function index()
    {
        $data = DisciplinaryCategory::all();
         
        return view('admin.disciplinary.categories.index', compact('data'));
    }
    public function trash()
    {
        $data = DisciplinaryCategory::onlyTrashed()->get( );
        
        return view('admin.disciplinary.categories.trash', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     *  @return \Illuminate\View\View
     */
    public function create()
    {

        return view('admin.disciplinary.categories.edit');
    }

 
    public function store(StoreDisciplinaryCategoryRequest $request)
    {
        $validated = $request->validated();
       
        $category = DisciplinaryCategory::create($validated);

        return redirect()->route('disciplinary.category.index')->with('success', 'Category Created successfully!'); 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DisciplinaryCategory  $disciplinaryCategory 
     */
    public function show( $disciplinaryCategory)
    {
        $readOnly=1;
        return view('admin.disciplinary.categories.edit', compact('editModeData','readOnly'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DisciplinaryCategory  $disciplinaryCategory 
     */
    public function edit( $id)
    {
        $editModeData = DisciplinaryCategory::find($id);
   
        return view('admin.disciplinary.categories.edit', compact('editModeData'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDisciplinaryCategoryRequest  $request
     * @param  \App\Models\DisciplinaryCategory  $disciplinaryCategory 
     */
    public function update(UpdateDisciplinaryCategoryRequest $request,  $id)
    {
        $validated = $request->validated();
        $disciplinaryCategory = DisciplinaryCategory::find($id);
        $disciplinaryCategory->update($validated);

        return redirect()->route('disciplinary.category.index')->with('success', 'Category updated successfully!'); 
    }

    /**
     * Remove the specified resource from storage (complete deletion).
     *
     * @param  \App\Models\DisciplinaryCategory  $disciplinaryCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $disciplinaryCategory = DisciplinaryCategory::withTrashed()->find($id);

        if (!$disciplinaryCategory) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found.'
            ], 404);
        }

        // Check if category has linked cases
        $hasCases = DB::table('disciplinary_cases')
            ->where('category_id', $id)
            ->exists();

        if ($hasCases) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete this category because it has disciplinary cases linked to it. Please reassign or delete the cases first.'
            ], 422);
        }

        try {
            // Force delete (complete deletion, not soft delete)
            $disciplinaryCategory->forceDelete();

            return response()->json([
                'status' => 'success',
                'message' => 'Category deleted successfully!'
            ]);
        } catch (\Exception $e) {
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();

            // Check for foreign key constraint violation
            if (str_contains($errorMessage, '1451') || str_contains($errorMessage, 'Integrity constraint violation')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete this category because it is being used by disciplinary cases.'
                ], 422);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting: ' . $errorMessage
            ], 500);
        }
    }
    /**
     * Permanently delete the specified resource from storage (from trash).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $data = DisciplinaryCategory::withTrashed()->find($id);

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found.'
            ], 404);
        }

        // Check if category has linked cases
        $hasCases = DB::table('disciplinary_cases')
            ->where('category_id', $id)
            ->exists();

        if ($hasCases) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete this category because it has disciplinary cases linked to it. Please reassign or delete the cases first.'
            ], 422);
        }

        try {
            $data->forceDelete();

            return response()->json([
                'status' => 'success',
                'message' => 'Category permanently deleted!'
            ]);
        } catch (\Exception $e) {
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();

            // Check for foreign key constraint violation
            if (str_contains($errorMessage, '1451') || str_contains($errorMessage, 'Integrity constraint violation')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete this category because it is being used by disciplinary cases.'
                ], 422);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting: ' . $errorMessage
            ], 500);
        }
    }
    /**
     * Restore the specified resource from trash.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $data = DisciplinaryCategory::withTrashed()->find($id);

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found.'
            ], 404);
        }

        try {
            $data->restore();

            return response()->json([
                'status' => 'success',
                'message' => 'Category restored successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while restoring: ' . $e->getMessage()
            ], 500);
        }
    }
}
