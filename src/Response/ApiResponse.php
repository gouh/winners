<?php

namespace App\Response;

use App\DTO\PaginationDto;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    /**
     * @param int                $statusCode
     * @param string|null        $error
     * @param array|null         $data
     * @param PaginationDto|null $pagination
     *
     * @return array
     */
    public static function createResponse(
        int $statusCode,
        string $error = null,
        array $data = null,
        PaginationDto $pagination = null
    ): array {
        return [
            'metadata'   => [
                'statusCode'    => $statusCode,
                'statusMessage' => Response::$statusTexts[$statusCode],
                'error'         => $error,
            ],
            'data'       => $data,
            'pagination' => $pagination?->toArray(),
        ];
    }
}
