<?php

namespace App\Repositories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * StudentRepository
 *
 *
 *
 * @author      Matvei Zaitsev <3au4uwkos@gmail.com>
 * @category
 * @package     App\Repositories
 */

class StudentRepository
{
    public function getAllStudents(): Collection
    {
        return Cache::remember('students:all', 600, function () {
            return Student::with('group')->get();
        });
    }

    public function getStudentById(int $id): ?Student
    {
        return Cache::remember("student:{$id}", 600, function () use ($id) {
            return Student::with(['group', 'lectures'])->find($id);
        });
    }

    public function createStudent(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            $student = Student::create($data);
            $this->clearCache();
            return $student;
        });
    }

    public function updateStudent(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $result = Student::where('id', $id)->update($data);
            $this->clearCache();
            $this->clearStudentCache($id);
            return $result;
        });
    }

    public function deleteStudent(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $result = Student::destroy($id);
            $this->clearCache();
            $this->clearStudentCache($id);
            return $result;
        });
    }

    public function attachToGroup(int $studentId, int $groupId): void
    {
        DB::transaction(function () use ($studentId, $groupId) {
            $student = Student::findOrFail($studentId);
            $student->group()->associate($groupId);
            $student->save();

            $this->clearCache();
            $this->clearStudentCache($studentId);
        });
    }

    public function detachFromGroup(int $studentId): void
    {
        DB::transaction(function () use ($studentId) {
            $student = Student::findOrFail($studentId);
            $student->group()->dissociate();
            $student->save();

            $this->clearCache();
            $this->clearStudentCache($studentId);
        });
    }

    private function clearCache(): void
    {
        Cache::forget('students:all');
    }

    private function clearStudentCache(int $id): void
    {
        Cache::forget("student:{$id}");
    }
}
