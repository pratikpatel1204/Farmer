<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Notification Master: admin sab members ke liye notification add/edit karta hai.
 * Default ON = member panel me sabse upar pinned (naya default banne tak).
 */
class NotificationMasterController extends Controller
{
    public function index()
    {
        return view('admin.notification_master.list');
    }

    public function table(Request $request)
    {
        $notifications = NotificationMaster::query()
            ->when($request->search, fn ($q, $search) => $q->where('title', 'like', '%' . $search . '%'))
            ->orderByDesc('is_default')
            ->latest('id')
            ->paginate(10, ['*'], 'page', $request->page ?? 1);

        return response()->json([
            'html' => view('admin.notification_master.table', compact('notifications'))->render(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        if ($data instanceof \Illuminate\Http\JsonResponse) {
            return $data;
        }

        $notification = NotificationMaster::create([
            'title'       => $data['title'],
            'description' => $data['description'],
            'status'      => $data['status'],
        ]);

        if ($data['is_default']) {
            $notification->makeDefault();
        }

        return response()->json(['status' => true, 'message' => 'Notification added successfully']);
    }

    public function update(Request $request)
    {
        $data = $this->validated($request, true);
        if ($data instanceof \Illuminate\Http\JsonResponse) {
            return $data;
        }

        $notification = NotificationMaster::findOrFail($request->id);
        $notification->fill([
            'title'       => $data['title'],
            'description' => $data['description'],
            'status'      => $data['status'],
            'is_default'  => false,
        ]);

        $data['is_default'] ? $notification->makeDefault() : $notification->save();

        return response()->json(['status' => true, 'message' => 'Notification updated successfully']);
    }

    /** Table ka Default toggle. */
    public function toggleDefault(NotificationMaster $notification)
    {
        if ($notification->is_default) {
            $notification->update(['is_default' => false]);
            $message = 'Notification removed from top.';
        } else {
            $notification->makeDefault();
            $message = $notification->status === 'Active'
                ? 'Notification set as default. It will stay on top for members.'
                : 'Set as default. Make it Active to show it to members.';
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    /** Table ka Active / Inactive toggle. */
    public function toggleStatus(NotificationMaster $notification)
    {
        $notification->update(['status' => $notification->status === 'Active' ? 'Inactive' : 'Active']);

        return response()->json([
            'status'  => true,
            'message' => 'Notification marked ' . $notification->status . '.',
        ]);
    }

    public function destroy(NotificationMaster $notification)
    {
        $notification->delete();

        return response()->json(['success' => true, 'message' => 'Notification deleted successfully']);
    }

    /* ---------- helpers ---------- */

    /** Validate karke saaf data do, fail hone par 422 JSON response. */
    private function validated(Request $request, bool $isUpdate = false)
    {
        $request->merge(['description' => NotificationMaster::cleanHtml($request->description)]);

        $rules = [
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string',
            'status'      => 'required|in:Active,Inactive',
        ];
        if ($isUpdate) {
            $rules['id'] = 'required|exists:notification_master,id';
        }

        $validator = Validator::make($request->all(), $rules);

        // CKEditor khali ho to bhi "<p>&nbsp;</p>" bhejta hai, isliye required ki jagah text check
        $validator->after(function ($v) use ($request) {
            $text = trim(html_entity_decode(strip_tags((string) $request->description)), " \t\n\r\0\x0B\xC2\xA0");
            if ($text === '') {
                $v->errors()->add('description', 'Please enter notification description.');
            }
        });

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        return [
            'title'       => trim($request->title),
            'description' => $request->description,
            'status'      => $request->status,
            'is_default'  => $request->boolean('is_default'),
        ];
    }
}
