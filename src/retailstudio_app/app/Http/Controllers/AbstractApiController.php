<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaginateCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class AbstractApiController extends Controller
{
    private function convertStatusCode(int $response_code): int
    {
        return (int)substr((string)$response_code, 0, 3);
    }

    /**
     * @param Collection|Model|null $model
     * @param array<string, string|mixed> $data
     * @param int|null $responseCode
     * @param string[] $meta
     *
     * @return JsonResponse
     */
    protected function modelResponse(
        Collection|Model|null $model,
        array $data = null,
        int $responseCode = null,
        array $meta = null
    ): JsonResponse {
        if (!isset($model)) {
            throw new ModelNotFoundException('Not Found');
        }
        return $this->arrayResponse($model->toArray() + ($data ?? []), $responseCode, $meta);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function paginateResponse(
        Builder $query,
    ): JsonResponse {
        $request = request();
        $paginate = $query->paginate(
            $this->toInt($request->get('limit', 30))
        );
        $paginate->appends($request->query->all());
        return (new PaginateCollection($paginate))->toResponse($request);
    }
    function toInt(mixed $value): ?int
    {
        if (is_null($value)) {
            return null;
        }
        return intval($value);
    }
    /**
     * preprocessing()をオーバーライドして利用してください。
     *
     * @param Builder $query
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function preprocessingPaginateResponse(
        Builder $query,
    ): JsonResponse {
        $paginateResponse = $this->paginateResponse($query);
        $paginateResponseData = (array)$paginateResponse->getData(true);

        /** @var array<int, array<string,mixed>> $paginateResponseDataArray */
        $paginateResponseDataArray = $paginateResponseData['data'];
        $paginateResponseDataArray = $this->preprocessing($paginateResponseDataArray);

        $paginateResponseData['data'] = $paginateResponseDataArray;
        $paginateResponse->setData($paginateResponseData);

        return $paginateResponse;
    }

    /**
     * @param array<int, array<string,mixed>> $data
     * @return array<int, mixed>
     */
    protected function preprocessing(array $data): array
    {
        return $data;
    }

    /**
     * @param string|null $message
     * @param int|null $responseCode
     * @param string[] $meta
     * @return JsonResponse
     */
    protected function errorResponse(
        ?string $message = null,
        ?int $responseCode = null,
        array $meta = []
    ): JsonResponse {
        if (!isset($responseCode)) {
            $responseCode = 404000;
        }

        $status_code = $this->convertStatusCode($responseCode);

        // @formatter:off
        $message = $message ?? match (true) {
                404 === $status_code => 'Not Found',
                default => 'Unpredictable Error',
        };
        // @formatter:on

        return $this->arrayResponse(['message' => $message], $responseCode, $meta);
    }

    /**
     * @param array<string|int, string|mixed>|string $data
     * @param int|null $responseCode
     * @param string[] $meta
     *
     * @return JsonResponse
     */
    protected function arrayResponse(
        array|string $data = null,
        int $responseCode = null,
        array $meta = null
    ): JsonResponse {
        switch (true) {
            case isset($responseCode):
                break;
            case request()->isMethod('post'):
            case request()->isMethod('put'):
                $responseCode = 201000;
                break;
            default:
                $responseCode = 200000;
                break;
        }
        $response_data = [
            'code' => $responseCode,
            'data' => $data ?? ''
        ];
        if (!empty($meta)) {
            $response_data['meta'] = $meta;
        }

        return response()->json(
            $response_data,
            $this->convertStatusCode($responseCode)
        );
    }
}
