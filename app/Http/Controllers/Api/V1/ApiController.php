<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Sab app API controllers ka base. Har jawab ek hi shape me:
 *   success: { success: true,  message, data, meta? }
 *   error:   { success: false, message, errors? }
 */
abstract class ApiController extends Controller
{
    protected function ok($data = null, string $message = 'OK', int $status = 200, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $extra), $status);
    }

    protected function fail(string $message, int $status = 422, $errors = null, array $extra = []): JsonResponse
    {
        $body = ['success' => false, 'message' => $message];
        if ($errors) {
            $body['errors'] = $errors;
        }

        return response()->json(array_merge($body, $extra), $status);
    }

    /** Paginated list: data = $map se bane items, meta = page info */
    protected function paginated(LengthAwarePaginator $page, callable $map, array $meta = [], array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'success' => true,
            'message' => 'OK',
            'data'    => collect($page->items())->map($map)->values(),
            'meta'    => array_merge([
                'current_page' => $page->currentPage(),
                'last_page'    => $page->lastPage(),
                'per_page'     => $page->perPage(),
                'total'        => $page->total(),
            ], $meta),
        ], $extra));
    }

    /** Validation fail ho to seedha 422 (website jaisa message + field errors) */
    protected function check(Request $request, array $rules, array $messages = []): array
    {
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new HttpResponseException($this->fail(
                'Please check the highlighted fields and try again.',
                422,
                $validator->errors()
            ));
        }

        return $validator->validated();
    }

    protected function perPage(Request $request, int $default = 12, int $max = 30): int
    {
        $perPage = (int) $request->query('per_page', $default);

        return $perPage > 0 ? min($perPage, $max) : $default;
    }
}
