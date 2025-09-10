<?php

namespace App\Repositories;

use App\Models\Lecture;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * LectureRepository
 *
 *
 *
 * @author      Matvei Zaitsev <3au4uwkos@gmail.com>
 * @category
 * @package     App\Repositories
 */
class LectureRepository
{
    public function getAllLectures(): Collection
    {
        return Cache::remember('lectures:all', 600, function () {
            return Lecture::all();
        });
    }

    public function getLectureById(int $id): ?Lecture
    {
        return Cache::remember("lecture:$id", 600, function () use ($id) {
            return Lecture::with(['groups', 'students'])->find($id);
        });
    }

    public function getLectureWithDetails(int $id): ?Lecture
    {
        return Cache::remember("lecture:$id:details", 600, function () use ($id) {
            return Lecture::with([
                'groups' => function ($query) {
                    $query->withCount('students');
                },
                'students' => function ($query) {
                    $query->with('group');
                }
            ])->find($id);
        });
    }

    public function createLecture(array $data): Lecture
    {
        return DB::transaction(function () use ($data) {
            $lecture = Lecture::create($data);
            $this->clearCache();
            return $lecture;
        });
    }

    public function updateLecture(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $result = Lecture::where('id', $id)->update($data);
            $this->clearCache();
            $this->clearLectureCache($id);
            return $result;
        });
    }

    public function deleteLecture(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            // Удаляем связи перед удалением лекции
            DB::table('group_lecture')->where('lecture_id', $id)->delete();
            DB::table('student_lecture')->where('lecture_id', $id)->delete();

            $result = Lecture::destroy($id);
            $this->clearCache();
            $this->clearLectureCache($id);
            return $result;
        });
    }

    public function getLecturesByGroup(int $groupId): Collection
    {
        return Cache::remember("lectures:group:$groupId", 600, function () use ($groupId) {
            return Lecture::whereHas('groups', function ($query) use ($groupId) {
                $query->where('groups.id', $groupId);
            })->orderBy('order')->get();
        });
    }

    public function attachLectureToGroup(int $lectureId, int $groupId, int $order): void
    {
        DB::transaction(function () use ($lectureId, $groupId, $order) {
            // Проверяем, не существует ли уже связи
            $exists = DB::table('group_lecture')
                ->where('group_id', $groupId)
                ->where('lecture_id', $lectureId)
                ->exists();

            if (!$exists) {
                DB::table('group_lecture')->insert([
                    'group_id' => $groupId,
                    'lecture_id' => $lectureId,
                    'order' => $order,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $this->clearCache();
                Cache::forget("lectures:group:$groupId");
            }
        });
    }

    public function detachLectureFromGroup(int $lectureId, int $groupId): void
    {
        DB::transaction(function () use ($lectureId, $groupId) {
            DB::table('group_lecture')
                ->where('group_id', $groupId)
                ->where('lecture_id', $lectureId)
                ->delete();

            $this->clearCache();
            Cache::forget("lectures:group:$groupId");
        });
    }

    public function markLectureAsAttended(int $lectureId, int $studentId): void
    {
        DB::transaction(function () use ($lectureId, $studentId) {
            $exists = DB::table('student_lecture')
                ->where('student_id', $studentId)
                ->where('lecture_id', $lectureId)
                ->exists();

            if (!$exists) {
                DB::table('student_lecture')->insert([
                    'student_id' => $studentId,
                    'lecture_id' => $lectureId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $this->clearLectureCache($lectureId);
            }
        });
    }

    private function clearCache(): void
    {
        Cache::forget('lectures:all');
    }

    private function clearLectureCache(int $id): void
    {
        Cache::forget("lecture:$id");
        Cache::forget("lecture:$id:details");
    }
}
