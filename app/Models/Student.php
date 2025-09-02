<?php

namespace App\Models;

use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory;

    protected $fillable = ['name', 'email', 'class_id'];
    protected $guarded = ['id'];

    public function class(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
