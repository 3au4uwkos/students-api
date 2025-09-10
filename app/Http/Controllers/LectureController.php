<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLectureRequest;
use App\Http\Requests\UpdateLectureRequest;
use App\Http\Resources\LectureResource;
use App\Services\LectureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Lectures",
 *     description="Операции с лекциями"
 * )
 */
class LectureController extends Controller
{
    public function __construct(
        private LectureService $lectureService
    ) {}

    /**
     * @OA\Get(
     *     path="/lectures",
     *     summary="Получить список всех лекций",
     *     tags={"Lectures"},
     *     @OA\Response(
     *         response=200,
     *         description="Успешное получение списка лекций",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="topic", type="string", example="Введение в программирование"),
     *                 @OA\Property(property="description", type="string", example="Основные понятия программирования"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ошибка сервера",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse|AnonymousResourceCollection
    {
        try {
            $lectures = $this->lectureService->getAllLectures();
            return LectureResource::collection($lectures);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/lectures/{id}",
     *     summary="Получить информацию о конкретной лекции",
     *     tags={"Lectures"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID лекции",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешное получение лекции",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="topic", type="string", example="Введение в программирование"),
     *             @OA\Property(property="description", type="string", example="Основные понятия программирования"),
     *             @OA\Property(
     *                 property="groups",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Группа 101"),
     *                     @OA\Property(property="order", type="integer", example=1)
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="students",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                     @OA\Property(property="email", type="string", format="email", example="ivan@example.com")
     *                 )
     *             ),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Лекция не найдена",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Lecture not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ошибка сервера",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse|LectureResource
    {
        try {
            $lecture = $this->lectureService->getLectureWithDetails($id);

            if (!$lecture) {
                return response()->json(['error' => 'Lecture not found'], 404);
            }

            return new LectureResource($lecture);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/lectures",
     *     summary="Создать новую лекцию",
     *     tags={"Lectures"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"topic", "description"},
     *             @OA\Property(property="topic", type="string", example="Введение в программирование"),
     *             @OA\Property(property="description", type="string", example="Основные понятия программирования")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Лекция успешно создана",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="topic", type="string", example="Введение в программирование"),
     *             @OA\Property(property="description", type="string", example="Основные понятия программирования"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="topic",
     *                     type="array",
     *                     @OA\Items(type="string", example="The topic has already been taken.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ошибка сервера",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function store(StoreLectureRequest $request): JsonResponse|LectureResource
    {
        try {
            $lecture = $this->lectureService->createLecture($request->validated());
            return (new LectureResource($lecture))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/lectures/{id}",
     *     summary="Обновить информацию о лекции",
     *     tags={"Lectures"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID лекции",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="topic", type="string", example="Обновленное введение в программирование"),
     *             @OA\Property(property="description", type="string", example="Обновленные основные понятия")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Лекция успешно обновлена",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="topic", type="string", example="Обновленное введение в программирование"),
     *             @OA\Property(property="description", type="string", example="Обновленные основные понятия"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Лекция не найдена",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Lecture not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="topic",
     *                     type="array",
     *                     @OA\Items(type="string", example="The topic has already been taken.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ошибка сервера",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function update(UpdateLectureRequest $request, int $id): JsonResponse|LectureResource
    {
        try {
            $updated = $this->lectureService->updateLecture($id, $request->validated());

            if (!$updated) {
                return response()->json(['error' => 'Lecture not found'], 404);
            }

            $lecture = $this->lectureService->getLectureById($id);
            return new LectureResource($lecture);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/lectures/{id}",
     *     summary="Удалить лекцию",
     *     tags={"Lectures"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID лекции",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Лекция успешно удалена"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Лекция не найдена",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Lecture not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ошибка сервера",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->lectureService->deleteLecture($id);

            if (!$deleted) {
                return response()->json(['error' => 'Lecture not found'], 404);
            }

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/lectures/group/{groupId}",
     *     summary="Получить лекции по группе",
     *     tags={"Lectures"},
     *     @OA\Parameter(
     *         name="groupId",
     *         in="path",
     *         required=true,
     *         description="ID группы",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список лекций группы",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="topic", type="string", example="Введение в программирование"),
     *                 @OA\Property(property="description", type="string", example="Основные понятия программирования"),
     *                 @OA\Property(property="order", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ошибка сервера",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function byGroup(int $groupId): JsonResponse
    {
        try {
            $lectures = $this->lectureService->getLecturesByGroup($groupId);
            return response()->json($lectures);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/lectures/{lectureId}/attend/{studentId}",
     *     summary="Отметить посещение лекции студентом",
     *     tags={"Lectures"},
     *     @OA\Parameter(
     *         name="lectureId",
     *         in="path",
     *         required=true,
     *         description="ID лекции",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="studentId",
     *         in="path",
     *         required=true,
     *         description="ID студента",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Посещение отмечено",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lecture attendance marked")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Лекция или студент не найдены",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Lecture or student not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ошибка сервера",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function markAttendance(int $lectureId, int $studentId): JsonResponse
    {
        try {
            $this->lectureService->markLectureAsAttended($lectureId, $studentId);
            return response()->json(['message' => 'Lecture attendance marked']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
