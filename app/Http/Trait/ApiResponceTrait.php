<?php

namespace App\Http\Trait;

use Illuminate\Http\JsonResponse;

/**
 * Trait ApiResponserTrait
 *
 * Provides a set of methods for generating consistent API responses.
 * This trait is intended to be used in controllers to standardize success
 * and error responses.
 *
 * @package App\Http\Traits
 */
trait ApiResponceTrait
{
    /**
     * Generate a success response in JSON format.
     *
     * @param array|null $data The data to include in the response. Defaults to an empty array.
     * @param string $message The success message to include in the response. Defaults to 'Success'.
     * @param int $httpResponseCode The HTTP response code. Defaults to 200.
     * @return JsonResponse The JSON response.
     */
    protected function successResponse(?array $data = [], string $message = 'Success', int $httpResponseCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'errors'  => null,
        ], $httpResponseCode);
    }

    /**
     * Generate an error response in JSON format.
     *
     * @param string $message The error message to include in the response.
     * @param array|null $errors An array of errors to include in the response. Defaults to an empty array.
     * @param int $httpResponseCode The HTTP response code. Defaults to 401.
     * @return JsonResponse The JSON response.
     */
    protected function errorResponse(string $message, ?array $errors = [], int $httpResponseCode = 401): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null,
            'errors'  => $errors,
        ], $httpResponseCode);
    }

    /**
     * Generate a not found response in JSON format.
     *
     * @param string $message The message to include in the response.
     * @param int $httpResponseCode The HTTP response code. Defaults to 404.
     * @return JsonResponse The JSON response.
     */
    public function notFound(string $message, int $httpResponseCode = 404): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code'    => $httpResponseCode,
        ], $httpResponseCode);
    }

    /**
     * Generate a generic API response.
     *
     * @param bool $success Indicates whether the response is successful or not.
     * @param string $message The message to include in the response.
     * @param array $data The data to include in the response. Defaults to an empty array.
     * @param int $statusCode The HTTP response code. Defaults to 200.
     * @return JsonResponse The JSON response.
     */
    protected function apiResponse(bool $success, string $message, array $data = [], int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    /**
     * Generate a success response for testing purposes.
     *
     * @param string $message The success message to include in the response. Defaults to 'Success'.
     * @param array $data The data to include in the response. Defaults to an empty array.
     * @param int $statusCode The HTTP response code. Defaults to 200.
     * @return JsonResponse The JSON response.
     */
    protected function successResponseTest(string $message = 'Success', array $data = [], int $statusCode = 200): JsonResponse
    {
        return $this->apiResponse(true, $message, $data, $statusCode);
    }

    /**
     * Generate an error response for testing purposes.
     *
     * @param string $message The error message to include in the response. Defaults to 'Invalid'.
     * @param int $statusCode The HTTP response code. Defaults to 500.
     * @return JsonResponse The JSON response.
     */
    protected function errorResponseTest(string $message = 'Invalid', int $statusCode = 500): JsonResponse
    {
        return $this->apiResponse(false, $message, [], $statusCode);
    }
}
