<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipment = Equipment::withCount('tasks')->latest()->paginate(20);

        return view('admin.equipment.index', compact('equipment'));
    }

    public function create()
    {
        return view('admin.equipment.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_code' => 'required|unique:equipment',
            'type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'status' => 'required|in:operational,warning,maintenance',
            'last_maintenance_date' => 'nullable|date',
        ]);

        Equipment::create($validated);

        return redirect()->route('admin.equipment.index')
            ->with('success', 'Equipment created successfully');
    }

    public function show(Equipment $equipment)
    {
        $equipment->load('tasks');

        return view('admin.equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment)
    {
        return view('admin.equipment.edit', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'equipment_code' => 'required|unique:equipment,equipment_code,'.$equipment->id,
            'type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'status' => 'required|in:operational,warning,maintenance',
            'last_maintenance_date' => 'nullable|date',
        ]);

        $equipment->update($validated);

        return redirect()->route('admin.equipment.index')
            ->with('success', 'Equipment updated successfully');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();

        return redirect()->route('admin.equipment.index')
            ->with('success', 'Equipment deleted successfully');
    }
}
