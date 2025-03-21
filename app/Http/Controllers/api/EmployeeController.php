<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {



        $query = Employee::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->has('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        $employees = $query->paginate(5);
        return response()->json($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data
        $validatedData = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'salary' => 'required|string|max:255',
            'profile' => 'required|mimes:jpg,png,jpeg|max:10240',
        ]);
        if ($validatedData->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validatedData->errors()
            ], 422);
        }
        $data = $validatedData->validated();
        if($request->hasFile('profile')) {
            $fileName = uniqid() . $request->file('profile')->getClientOriginalName();
            $request->file('profile')->storeAs('public/profile',$fileName);
            $data['profile'] = $fileName;
        }


        // Create a new Company instance and save it to the database
        $employee = Employee::query()->create($data);

        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'Employee created successfully',
            'data' => $employee
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $searchId = Employee::where('id', $id)->first();
        if (isset($searchId)) {
            $data = Employee::where('id', $id)->first();
            return response()->json($data, 200);
        }
        return response()->json(['status' => False, 'message' => 'Try Again'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'message' => "Employee not found",
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'string',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:employees,email,' . $id,
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'position' => 'required|string',
            'salary' => 'required|string',
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Check if a new logo is uploaded
        if($request->hasFile('profile')){
            Storage::disk('public')->delete('profile/' . $employee->profile);

            // save new image1
            $fileName = uniqid() . $request->file('profile')->getClientOriginalName();
            $request->file('profile')->storeAs('public/profile',$fileName);
            $data['profile'] = $fileName;
        }

        // Update the company with validated data
        $employee->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Employee updated successfully',
            'data' => $employee
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Employee::where('id', $id)->first();

        if (isset($data)) {
            $dbImage = Employee::where('id', $id)->first();
            $dbImage = $dbImage->image_path;

            if ($dbImage != null) {
                Storage::disk('public')->delete('profile/' . dbImage);
            }
            $delete = Employee::where('id', $id)->delete();
            return response()->json(['message' => 'Delete successfully'], 200);
        }
        return response()->json(["Status" => false, "Message" => "Have's Id"], 200);
    }
}
