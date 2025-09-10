<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * GroupRepository
 *
 *
 *
 * @author      Matvei Zaitsev <3au4uwkos@gmail.com>
 * @category
 * @package     App\Repositories
 */
class GroupRepository
{
    public function getAllGroups(): Collection
    {
        return Cache::remember('groups:all', 600, function () {
            return Group::all();
        });
    }

    public function getGroupById(int $id): ?Group
    {
        return Cache::remember("group:{$id}", 600, function () use ($id) {
            return Group::with('students')->find($id);
        });
    }

    public function getGroupWithCurriculum(int $id): ?Group
    {
        return Cache::remember("group:{$id}:curriculum", 600, function () use ($id) {
            return Group::with(['lectures' => function ($query) {
                $query->orderBy('order');
            }])->find($id);
        });
    }

    public function createGroup(array $data): Group
    {
        return DB::transaction(function () use ($data) {
            $group = Group::create($data);
            $this->clearCache();
            return $group;
        });
    }

    public function updateGroup(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $result = Group::where('id', $id)->update($data);
            $this->clearCache();
            $this->clearGroupCache($id);
            return $result;
        });
    }

    public function deleteGroup(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            DB::table('group_student')
                ->where('group_id', $id)
                ->delete();

            $result = Group::destroy($id);
            $this->clearCache();
            $this->clearGroupCache($id);
            return $result;
        });
    }

    public function updateCurriculum(int $groupId, array $lectureIds): void
    {
        DB::transaction(function () use ($groupId, $lectureIds) {
            DB::table('group_lecture')
                ->where('group_id', $groupId)
                ->delete();

            $curriculumData = [];
            foreach ($lectureIds as $index => $lectureId) {
                $curriculumData[] = [
                    'group_id' => $groupId,
                    'lecture_id' => $lectureId,
                    'order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            DB::table('group_lecture')->insert($curriculumData);

            $this->clearCache();
            $this->clearGroupCache($groupId);
            Cache::forget("group:{$groupId}:curriculum");
        });
    }

    public function addStudentToGroup(int $groupId, int $studentId): void
    {
        DB::transaction(function () use ($groupId, $studentId) {
            DB::table('group_student')
                ->where('student_id', $studentId)
                ->delete();

            DB::table('group_student')->insert([
                'group_id' => $groupId,
                'student_id' => $studentId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $this->clearCache();
            $this->clearGroupCache($groupId);
        });
    }

    public function removeStudentFromGroup(int $groupId, int $studentId): void
    {
        DB::transaction(function () use ($groupId, $studentId) {
            DB::table('group_student')
                ->where('group_id', $groupId)
                ->where('student_id', $studentId)
                ->delete();

            $this->clearCache();
            $this->clearGroupCache($groupId);
        });
    }

    private function clearCache(): void
    {
        Cache::forget('groups:all');
    }

    private function clearGroupCache(int $id): void
    {
        Cache::forget("group:{$id}");
        Cache::forget("group:{$id}:curriculum");
    }
}
