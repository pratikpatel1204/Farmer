<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StateController extends Controller
{
    public function state_list()
    {
        $states = State::orderBy('id', 'DESC')->get();
        return view('admin.state.list', compact('states'));
    }

    // Returns only table HTML - used for AJAX refresh (no full page reload)
    public function table()
    {
        $states = State::orderBy('id', 'DESC')->get();

        $html = view('admin.state.ajax_table', compact('states'))->render();

        return response()->json([
            'html' => $html
        ]);
    }

    public function state_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:states,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        State::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'State created successfully'
        ]);
    }

    public function state_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:states,name,' . $request->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $state = State::findOrFail($request->id);
        $state->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'State updated successfully'
        ]);
    }

    public function state_destroy($id)
    {
        $state = State::find($id);

        if (!$state) {
            return response()->json([
                'success' => false,
                'message' => 'State not found'
            ], 404);
        }

        $state->delete();

        return response()->json([
            'success' => true,
            'message' => 'State deleted successfully'
        ]);
    }
}
