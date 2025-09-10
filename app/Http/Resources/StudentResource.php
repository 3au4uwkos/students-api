<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * StudentResource
 *
 *
 *
 * @author      Matvei Zaitsev <3au4uwkos@gmail.com>
 * @category
 * @package     App\Http\Resources
 */
class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'group' => $this->whenLoaded('group', fn() => [
                'id' => $this->group->id,
                'name' => $this->group->name
            ]),
            'lectures' => $this->whenLoaded('lectures', fn() =>
            $this->lectures->map(fn($lecture) => [
                'id' => $lecture->id,
                'topic' => $lecture->topic
            ])
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
