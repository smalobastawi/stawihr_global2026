<?php

namespace App\Http\Controllers\Hr;

use App\Http\Requests\DocumentCategoriesRequest;
use App\Models\DocumentCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentCategoriesController extends Model
{
    use HasFactory;
    public function index()
    {

        $categories = DocumentCategory::all();
        return view('admin.hr.document-categories.index', ['categories' => $categories]);
    }
    public function create(){
        return view('admin.hr.document-categories.create');
    }

    public function store(Request $request)
    {
      

        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:255',

        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $category = new DocumentCategory();
        $category->name = $request->category_name;
        $category->description = $request->description;
        $category->save();
        
        return redirect()->route('document-categories.index')->with('success', 'Document Category Added Successfully');
    }

    public function edit ($documentCategoryId){

        $documentCategory = DocumentCategory::find($documentCategoryId);
        return view('admin.hr.document-categories.edit', ['documentCategory' => $documentCategory]);
    }

 
    public function updateDocumentCategory(DocumentCategoriesRequest $request, DocumentCategory $documentCategory)
    {
      
        // $category = DocumentCategory::find($documentCategory);
        $documentCategory->name = $request->category_name;
        $documentCategory->description = $request->description;
        
        $documentCategory->save();
        
        return redirect()->route('document-categories.index')->with('success', 'Document Category Updated Successfully');

     }
     
    public function destroyDocumentCategory($documentCategory)
    {
        try{

            $category = DocumentCategory::findOrFail($documentCategory);
            $category->delete();        
            echo "success";
            
        } catch (\Exception $e) {

            echo 'error';

        }
     
    }
}
