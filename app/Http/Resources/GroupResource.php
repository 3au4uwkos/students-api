<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * GroupResource
 *
 *
 *
 * @author      Matvei Zaitsev <3au4uwkos@gmail.com>
 * @category
 * @package     App\Http\Resources
 */
class GroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'students' => $this->whenLoaded('students', function () {
                return $this->students->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'email' => $student->email
                    ];
                });
            }),
            'lectures' => $this->whenLoaded('lectures', function () {
                return $this->lectures->map(function ($lecture) {
                    return [
                        'id' => $lecture->id,
                        'topic' => $lecture->topic,
                        'description' => $lecture->description,
                        'order' => $lecture->pivot->order ?? null
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
