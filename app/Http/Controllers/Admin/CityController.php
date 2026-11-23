<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    public function city_list()
    {
        $states = State::orderBy('name')->get();

        return view('admin.city.list', compact('states'));
    }

    // Returns only table HTML - used for AJAX refresh (search + pagination, server-side)
    public function table(Request $request)
    {
        $query = City::with('state')->orderBy('id', 'DESC');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('state', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $cities = $query->paginate(10)->withQueryString();

        $html = view('admin.city.ajax_table', compact('cities'))->render();

        return response()->json([
            'html' => $html,
            'total' => $cities->total(),
            'from' => $cities->firstItem(),
            'to' => $cities->lastItem(),
        ]);
    }

    public function city_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'state_id' => 'required|exists:states,id',
            'name'     => 'required|string|max:255|unique:cities,name,NULL,id,state_id,' . $request->state_id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        City::create([
            'state_id' => $request->state_id,
            'name'     => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'City created successfully'
        ]);
    }

    public function city_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'state_id' => 'required|exists:states,id',
            'name'     => 'required|string|max:255|unique:cities,name,' . $request->id . ',id,state_id,' . $request->state_id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $city = City::findOrFail($request->id);
        $city->update([
            'state_id' => $request->state_id,
            'name'     => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'City updated successfully'
        ]);
    }

    public function city_destroy($id)
    {
        $city = City::find($id);

        if (!$city) {
            return response()->json([
                'success' => false,
                'message' => 'City not found'
            ], 404);
        }

        $city->delete();

        return response()->json([
            'success' => true,
            'message' => 'City deleted successfully'
        ]);
    }
}