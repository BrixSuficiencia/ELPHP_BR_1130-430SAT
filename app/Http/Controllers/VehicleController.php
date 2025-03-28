<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    // Get all vehicles
    public function index()
    {
        $vehicles = Vehicle::all();
        return response()->json($vehicles);
    }

    // Get details of a specific vehicle
    public function show($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return response()->json($vehicle);
    }

    // Add a new vehicle (Owner only)
    public function store(Request $request)
    {
        $this->authorize('owner');

        $request->validate([
            'name' => 'required|string|max:255',
            'plate_number' => 'required|string|unique:vehicles',
            'model' => 'required|string|max:255',
            'fuel_type' => 'required|string',
            'price' => 'required|numeric',
            'location' => 'required|string',
        ]);

        $vehicle = Vehicle::create([
            'owner_id' => auth()->id(),
            'name' => $request->name,
            'plate_number' => $request->plate_number,
            'model' => $request->model,
            'fuel_type' => $request->fuel_type,
            'price' => $request->price,
            'location' => $request->location,
        ]);

        return response()->json($vehicle, 201);
    }

    // Update vehicle (Owner only)
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $this->authorize('owner');

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'plate_number' => 'sometimes|string|unique:vehicles,plate_number,' . $id,
            'model' => 'sometimes|string|max:255',
            'fuel_type' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'location' => 'sometimes|string',
        ]);

        $vehicle->update($request->all());

        return response()->json($vehicle);
    }

    // Delete vehicle (Owner only)
    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $this->authorize('owner');

        $vehicle->delete();

        return response()->json(['message' => 'Vehicle deleted successfully']);
    }

    // Get all vehicles listed by the logged-in owner
    public function getOwnerVehicles()
    {
        $vehicles = auth()->user()->vehicles;
        return response()->json($vehicles);
    }
}
