<?php

namespace App\DTO;

use App\Entity\Winner;

class WinnerDto
{
    public static function ObjectToArray(Winner $winner): array
    {
        return [
            'id'       => $winner->getId(),
            'name'     => $winner->getName(),
            'position' => $winner->getPosition(),
        ];
    }

    /**
     * @param Winner[] $winners
     *
     * @return array
     */
    public static function ObjectsToArray(array $winners): array
    {
        return array_map(function ($winner) {
            return self::ObjectToArray($winner);
        }, $winners);
    }
}