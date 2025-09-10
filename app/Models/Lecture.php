<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Lecture extends Model
{
    /** @use HasFactory<\Database\Factories\LectureFactory> */
    use HasFactory;

    protected $fillable = ['topic', 'description'];
    protected $guarded = ['id'];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_lecture')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_lecture')
            ->withTimestamps();
    }
}
