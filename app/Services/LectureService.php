<?php

namespace App\Services;

use App\Models\Lecture;
use App\Repositories\LectureRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * LectureService
 *
 *
 *
 * @author      Matvei Zaitsev <3au4uwkos@gmail.com>
 * @category
 * @package     App\Services
 */
readonly class LectureService
{
    public function __construct(
        private readonly LectureRepository $lectureRepository
    ) {}

    public function getAllLectures(): Collection
    {
        return $this->lectureRepository->getAllLectures();
    }

    public function getLectureById(int $id): ?Lecture
    {
        return $this->lectureRepository->getLectureById($id);
    }

    public function getLectureWithDetails(int $id): ?Lecture
    {
        return $this->lectureRepository->getLectureWithDetails($id);
    }

    public function createLecture(array $data): Lecture
    {
        return $this->lectureRepository->createLecture($data);
    }

    public function updateLecture(int $id, array $data): bool
    {
        return $this->lectureRepository->updateLecture($id, $data);
    }

    public function deleteLecture(int $id): bool
    {
        return $this->lectureRepository->deleteLecture($id);
    }

    public function getLecturesByGroup(int $groupId): Collection
    {
        return $this->lectureRepository->getLecturesByGroup($groupId);
    }

    public function attachLectureToGroup(int $lectureId, int $groupId, int $order): void
    {
        $this->lectureRepository->attachLectureToGroup($lectureId, $groupId, $order);
    }

    public function detachLectureFromGroup(int $lectureId, int $groupId): void
    {
        $this->lectureRepository->detachLectureFromGroup($lectureId, $groupId);
    }

    public function markLectureAsAttended(int $lectureId, int $studentId): void
    {
        $this->lectureRepository->markLectureAsAttended($lectureId, $studentId);
    }
}
