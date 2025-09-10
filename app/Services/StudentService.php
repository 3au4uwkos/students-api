<?php

namespace App\Services;

use App\Models\Student;
use App\Repositories\StudentRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * StudentService
 *
 *
 *
 * @author      Matvei Zaitsev <3au4uwkos@gmail.com>
 * @category
 * @package     App\Services
 */
readonly class StudentService
{
    public function __construct(
        private StudentRepository $studentRepository
    ) {}

    public function getAllStudents(): Collection
    {
        return $this->studentRepository->getAllStudents();
    }

    public function getStudentById(int $id): ?Student
    {
        return $this->studentRepository->getStudentById($id);
    }

    public function createStudent(array $data): Student
    {
        return $this->studentRepository->createStudent($data);
    }

    public function updateStudent(int $id, array $data): bool
    {
        return $this->studentRepository->updateStudent($id, $data);
    }

    public function deleteStudent(int $id): bool
    {
        return $this->studentRepository->deleteStudent($id);
    }

    public function attachToGroup(int $studentId, int $groupId): void
    {
        $this->studentRepository->attachToGroup($studentId, $groupId);
    }

    public function detachFromGroup(int $studentId): void
    {
        $this->studentRepository->detachFromGroup($studentId);
    }
}
