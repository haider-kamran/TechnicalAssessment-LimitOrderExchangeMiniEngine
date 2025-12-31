<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use App\Enums\ResponseCodeEnum;
use Illuminate\Http\RedirectResponse;

trait ResponseTrait
{
    /**
     * Return a JSON response with success status.
     *
     * @param string $message
     * @param mixed $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success(string $message = 'Success', mixed $data = null, int $code = ResponseCodeEnum::SUCCESS->value): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data
        ], $code);
    }

    /**
     * Return a JSON response with error status.
     *
     * @param string $message
     * @param int $code
     * @param mixed $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(string $message = 'Error', int $code = ResponseCodeEnum::BAD_REQUEST->value, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'data'    => [],
            'errors'  => $errors
        ], $code);
    }

    /**
     * Redirect back with success message.
     *
     * @param string $message
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectWithSuccess(string $message): RedirectResponse
    {
        return back()->with('success', $message);
    }

    /**
     * Redirect back with error message.
     *
     * @param string $message
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectWithError(string $message): RedirectResponse
    {
        return back()->with('error', $message)->withInput();
    }
}
