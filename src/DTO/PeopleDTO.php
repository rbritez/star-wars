<?php

namespace App\DTO;

use DateTime;

class PeopleDTO
{
    public $data;

    public function __construct($data)
    {
        $this->data = $this->filterData($data);
    }

    private function filterData(array $data): array
    {
        return array_map(function ($item) {
            $date = new DateTime($item->created);
            return [
                'name' => $item->name,
                'height' => $item->height,
                'mass' => $item->mass,
                'eye_color' => $item->eye_color,
                'gender' => $item->gender,
                'created' => $date->format('Y-m-d H:i:s'),
            ];
        }, $data);
    }
}