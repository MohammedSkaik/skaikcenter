<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Room::all()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                \Illuminate\Validation\Rule::unique('rooms')->whereNull('deleted_at')->where('is_deleted', 0)
            ],
            'capacity' => 'integer|min:1',
            'type' => 'in:classroom,lab,hall'
        ]);

        $room = Room::create($validated);
        return response()->json(['message' => 'Room created', 'data' => $room], 201);
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => [
                'string',
                \Illuminate\Validation\Rule::unique('rooms')->ignore($room->id)->whereNull('deleted_at')->where('is_deleted', 0)
            ],
            'capacity' => 'integer|min:1',
            'type' => 'in:classroom,lab,hall'
        ]);

        $room->update($validated);
        return response()->json(['message' => 'Room updated', 'data' => $room]);
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return response()->json(['message' => 'Room deleted']);
    }
}
