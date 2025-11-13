<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chiller;
use App\Models\Lift;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(): Factory|View
    {
        $lifts = Lift::query()->withCount('tasks')->get();
        $chillers = Chiller::query()->withCount('tasks')->get();

        return view('admin.assets.index', compact('lifts', 'chillers'));
    }

    public function storeLift(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255|unique:lifts,name',
            'location' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'last_maintenance_date' => 'nullable|date|before_or_equal:today',
        ], [
            'name.required' => 'Lift name is required',
            'name.min' => 'Lift name must be at least 2 characters',
            'name.unique' => 'A lift with this name already exists',
            'last_maintenance_date.before_or_equal' => 'Last maintenance date cannot be in the future',
        ]);

        Lift::query()->create($validated);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Lift added successfully!');
    }

    public function storeChiller(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255|unique:chillers,name',
            'location' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'last_maintenance_date' => 'nullable|date|before_or_equal:today',
        ], [
            'name.required' => 'Chiller name is required',
            'name.min' => 'Chiller name must be at least 2 characters',
            'name.unique' => 'A chiller with this name already exists',
            'last_maintenance_date.before_or_equal' => 'Last maintenance date cannot be in the future',
        ]);

        Chiller::query()->create($validated);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Chiller added successfully!');
    }

    public function updateLift(Request $request, Lift $lift): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|min:2|max:255|unique:lifts,name,'.$lift->id,
                'location' => 'nullable|string|max:255',
                'model_number' => 'nullable|string|max:255',
                'serial_number' => 'nullable|string|max:255',
                'last_maintenance_date' => 'nullable|date|before_or_equal:today',
            ], [
                'name.required' => 'Lift name is required',
                'name.min' => 'Lift name must be at least 2 characters',
                'name.unique' => 'A lift with this name already exists',
                'last_maintenance_date.before_or_equal' => 'Last maintenance date cannot be in the future',
            ]);

            $lift->update($validated);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Lift updated successfully!']);
            }

            return redirect()->route('admin.assets.index')
                ->with('success', 'Lift updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $e->errors(), 'message' => 'Validation failed'], 422);
            }
            throw $e;
        }
    }

    public function updateChiller(Request $request, Chiller $chiller): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|min:2|max:255|unique:chillers,name,'.$chiller->id,
                'location' => 'nullable|string|max:255',
                'model_number' => 'nullable|string|max:255',
                'serial_number' => 'nullable|string|max:255',
                'last_maintenance_date' => 'nullable|date|before_or_equal:today',
            ], [
                'name.required' => 'Chiller name is required',
                'name.min' => 'Chiller name must be at least 2 characters',
                'name.unique' => 'A chiller with this name already exists',
                'last_maintenance_date.before_or_equal' => 'Last maintenance date cannot be in the future',
            ]);

            $chiller->update($validated);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Chiller updated successfully!']);
            }

            return redirect()->route('admin.assets.index')
                ->with('success', 'Chiller updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $e->errors(), 'message' => 'Validation failed'], 422);
            }
            throw $e;
        }
    }

    public function destroyLift(Lift $lift): RedirectResponse
    {
        $lift->delete();

        return redirect()->route('admin.assets.index')
            ->with('success', 'Lift deleted successfully!');
    }

    public function destroyChiller(Chiller $chiller): RedirectResponse
    {
        $chiller->delete();

        return redirect()->route('admin.assets.index')
            ->with('success', 'Chiller deleted successfully!');
    }
}
