<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // Get all bookings (Admin only)
    public function index()
    {
        $this->authorize('admin');

        $bookings = Booking::all();
        return response()->json($bookings);
    }

    // Get specific booking
    public function show($id)
    {
        $booking = Booking::findOrFail($id);
        return response()->json($booking);
    }

    // Create a new booking (Renter only)
    public function store(Request $request)
    {
        $this->authorize('renter');

        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'pickup_date' => 'required|date',
            'return_date' => 'required|date|after:pickup_date',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        $booking = Booking::create([
            'renter_id' => auth()->id(),
            'vehicle_id' => $vehicle->id,
            'pickup_date' => $request->pickup_date,
            'return_date' => $request->return_date,
        ]);

        return response()->json($booking, 201);
    }

    // Update booking (Renter only)
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $this->authorize('renter');

        $request->validate([
            'pickup_date' => 'sometimes|date',
            'return_date' => 'sometimes|date|after:pickup_date',
        ]);

        $booking->update($request->all());

        return response()->json($booking);
    }

    // Cancel booking (Renter only)
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $this->authorize('renter');

        $booking->delete();

        return response()->json(['message' => 'Booking canceled successfully']);
    }

    // Get all bookings made by the logged-in renter
    public function getRenterBookings()
    {
        $bookings = auth()->user()->bookings;
        return response()->json($bookings);
    }

    // Get all bookings for the logged-in owner's vehicles
    public function getOwnerBookings()
    {
        $bookings = Booking::whereIn('vehicle_id', auth()->user()->vehicles->pluck('id'))->get();
        return response()->json($bookings);
    }
}
