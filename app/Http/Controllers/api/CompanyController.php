<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;



class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Company::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        $Companies = $query->paginate(5);
        return response()->json($Companies);
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        // Validate incoming data

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Ensure logo is an image file
            'website' => 'required|string|url',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        $data = $validator->validated();
        if($request->hasFile('logo')) {
            $fileName = uniqid() . $request->file('logo')->getClientOriginalName();
            $request->file('logo')->storeAs('public/logo',$fileName);
            $data['logo'] = $fileName;
        }
        
        $company = Company::query()->create($data);

        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'Company created successfully',
            'data' => $company
        ], 200);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $searchId = Company::where('id', $id)->first();
        if (isset($searchId)) {
            $data = Company::where('id', $id)->first();
            return response()->json($data, 200);
        }
        return response()->json(['status' => False, 'message' => 'Try Again'], 200);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {
        $company = Company::find($id);
        if (!$company) {
            return response()->json([
                'message' => "Company not found",
            ], 400);
        }
    
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies,email,' . $id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'website' => 'required|string|url',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $validatedData = $validator->validated();
    
        // Check if a new logo is uploaded
        if ($request->hasFile('logo')) {
            // Delete the old logo if it exists
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
    
            // Handle new logo upload and resizing
            $imageFile = $request->file('logo');
            $fileName = uniqid() . '_' . $imageFile->getClientOriginalName();
    
            // Create image manager with GD driver
            $manager = new ImageManager(new Driver());
    
            // Make image from file and resize
            $image = $manager->read($imageFile->getRealPath());
    
            // Resize image to 300px width, keeping aspect ratio
            $image->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
    
            // Save the resized image to storage
            $image->save(storage_path("app/public/{$fileName}"));
    
            // Update logo filename in the database
            $validatedData['logo'] = $fileName;
        }
    
        // Update the company with validated data
        $company->update($validatedData);
    
        return response()->json([
            'status' => true,
            'message' => 'Company updated successfully',
            'data' => $company
        ], 200);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Company::where('id',$id)->first();

        if (isset($data)) {
            $dbImage = Company::where('id',$id)->first();
            $dbImage = $dbImage->image_path;

            if ($dbImage != null) {
                Storage::disk('public')->delete($dbImage);
            }
            $update = Company::where('id',$id)->delete();
            return response()->json(['message' => 'Delete successfully'], 201);
        }
        return response()->json(["Status" => false,"Message" => "Have's Id"], 200);

    }
}
