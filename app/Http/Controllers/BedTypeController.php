<?php

namespace App\Http\Controllers;

use App\Models\BedType; // Make sure you have a BedType model
use Illuminate\Http\Request;

class BedTypeController extends Controller
{
    // Display a list of bed types
    public function index()
    {
        $bedTypes = BedType::all(); // Fetch all bed types from the database
        return view('admin.bed_types', compact('bedTypes')); // Return the view with the data
    }

    // Show the form to create a new bed type
    public function create()
    {
        return view('admin.bed_types.create'); // Show the create form view
    }

    // Store a newly created bed type in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255', // Validation for the name field
        ]);

        BedType::create([
            'name' => $request->name,
        ]);

        return redirect()->route('bed_types.index')->with('success', 'Loại giường đã được tạo thành công!');
    }


    // Update the specified bed type in the database
    public function update(Request $request, BedType $bed_type)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $bed_type->update([
            'name' => $request->name,
        ]);

        return redirect()->route('bed_types.index')->with('success', 'Loại giường đã được cập nhật!');
    }

    // Remove the specified bed type from the database
    public function destroy(BedType $bedType)
    {
        $bedType->delete();

        return redirect()->route('bed_types.index')->with('success', 'Loại giường đã được xóa!');
    }
}
