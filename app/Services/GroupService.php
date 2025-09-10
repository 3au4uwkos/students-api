<?php

namespace App\Services;

use App\Models\Group;
use App\Repositories\GroupRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * GroupService
 *
 *
 *
 * @author      Matvei Zaitsev <3au4uwkos@gmail.com>
 * @category
 * @package     App\Services
 */
readonly class GroupService
{
    public function __construct(
        private GroupRepository $groupRepository
    ) {}

    public function getAllGroups(): Collection
    {
        return $this->groupRepository->getAllGroups();
    }

    public function getGroupById(int $id): ?Group
    {
        return $this->groupRepository->getGroupById($id);
    }

    public function getGroupWithCurriculum(int $id): ?Group
    {
        return $this->groupRepository->getGroupWithCurriculum($id);
    }

    public function createGroup(array $data): Group
    {
        return $this->groupRepository->createGroup($data);
    }

    public function updateGroup(int $id, array $data): bool
    {
        return $this->groupRepository->updateGroup($id, $data);
    }

    public function deleteGroup(int $id): bool
    {
        return $this->groupRepository->deleteGroup($id);
    }

    public function updateCurriculum(int $groupId, array $lectureIds): void
    {
        $this->groupRepository->updateCurriculum($groupId, $lectureIds);
    }

    public function addStudentToGroup(int $groupId, int $studentId): void
    {
        $this->groupRepository->addStudentToGroup($groupId, $studentId);
    }

    public function removeStudentFromGroup(int $groupId, int $studentId): void
    {
        $this->groupRepository->removeStudentFromGroup($groupId, $studentId);
    }
}
