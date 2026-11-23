<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LevelController extends Controller
{
    public function index()
    {
        return view('admin.level.level-list');
    }

    public function table(Request $request)
    {
        $levels = Level::query()
            ->when($request->search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('level', 'like', '%' . $search . '%')
                        ->orWhere('amount', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('level')
            ->paginate(10, ['*'], 'page', $request->page ?? 1);

        return response()->json([
            'html' => view('admin.level.level-table', compact('levels'))->render(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level'  => 'required|integer|min:1|unique:levels,level',
            'amount' => 'required|numeric|min:0',
        ], [
            'level.unique' => 'This level already exists',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        Level::create($request->only(['level', 'amount']));

        return response()->json(['status' => true, 'message' => 'Level added successfully']);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'     => 'required|exists:levels,id',
            'level'  => 'required|integer|min:1|unique:levels,level,' . $request->id,
            'amount' => 'required|numeric|min:0',
        ], [
            'level.unique' => 'This level already exists',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $level = Level::findOrFail($request->id);
        $level->update($request->only(['level', 'amount']));

        return response()->json(['status' => true, 'message' => 'Level updated successfully']);
    }

    public function destroy($id)
    {
        $level = Level::find($id);

        if (!$level) {
            return response()->json(['success' => false, 'message' => 'Level not found']);
        }

        $level->delete();

        return response()->json(['success' => true, 'message' => 'Level deleted successfully']);
    }
}