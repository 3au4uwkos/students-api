<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Requests\UpdateGroupCurriculumRequest;
use App\Http\Resources\GroupResource;
use App\Services\GroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Groups",
 *     description="Операции с группами (классами)"
 * )
 */
class GroupController extends Controller
{
    public function __construct(
        private readonly GroupService $groupService
    ) {}

    /**
     * @OA\Get(
     *     path="/groups",
     *     summary="Получить список всех групп",
     *     tags={"Groups"},
     *     @OA\Response(
     *         response=200,
     *         description="Успешное получение списка групп",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Группа 101"),
     *                 @OA\Property(
     *                     property="students",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                         @OA\Property(property="email", type="string", format="email", example="ivan@example.com")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="lectures",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="topic", type="string", example="Введение в программирование"),
     *                         @OA\Property(property="description", type="string", example="Основные понятия"),
     *                         @OA\Property(property="order", type="integer", example=1)
     *                     )
     *                 ),
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
            $groups = $this->groupService->getAllGroups();
            return GroupResource::collection($groups);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/groups/{id}",
     *     summary="Получить информацию о конкретной группе",
     *     tags={"Groups"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID группы",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешное получение группы",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Группа 101"),
     *             @OA\Property(
     *                 property="students",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                     @OA\Property(property="email", type="string", format="email", example="ivan@example.com")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="lectures",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="topic", type="string", example="Введение в программирование"),
     *                     @OA\Property(property="description", type="string", example="Основные понятия"),
     *                     @OA\Property(property="order", type="integer", example=1)
     *                 )
     *             ),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Группа не найдена",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Group not found")
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
    public function show(int $id): JsonResponse|GroupResource
    {
        try {
            $group = $this->groupService->getGroupById($id);

            if (!$group) {
                return response()->json(['error' => 'Group not found'], 404);
            }

            return new GroupResource($group);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/groups/{id}/curriculum",
     *     summary="Получить учебный план группы",
     *     tags={"Groups"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID группы",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Учебный план группы",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Группа 101"),
     *             @OA\Property(
     *                 property="lectures",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="topic", type="string", example="Введение в программирование"),
     *                     @OA\Property(property="description", type="string", example="Основные понятия программирования"),
     *                     @OA\Property(property="order", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Группа не найдена",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Group not found")
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
    public function curriculum(int $id): JsonResponse
    {
        try {
            $group = $this->groupService->getGroupWithCurriculum($id);

            if (!$group) {
                return response()->json(['error' => 'Group not found'], 404);
            }

            return response()->json([
                'id' => $group->id,
                'name' => $group->name,
                'lectures' => $group->lectures->map(function ($lecture) {
                    return [
                        'id' => $lecture->id,
                        'topic' => $lecture->topic,
                        'description' => $lecture->description,
                        'order' => $lecture->pivot->order
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/groups",
     *     summary="Создать новую группу",
     *     tags={"Groups"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Группа 101")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Группа успешно создана",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Группа 101"),
     *             @OA\Property(property="students", type="array", nullable=true, @OA\Items()),
     *             @OA\Property(property="lectures", type="array", nullable=true, @OA\Items()),
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
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The name has already been taken.")
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
    public function store(StoreGroupRequest $request): JsonResponse|GroupResource
    {
        try {
            $group = $this->groupService->createGroup($request->validated());
            return new GroupResource($group)
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/groups/{id}",
     *     summary="Обновить информацию о группе",
     *     tags={"Groups"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID группы",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Группа 102")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Группа успешно обновлена",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Группа 102"),
     *             @OA\Property(
     *                 property="students",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                     @OA\Property(property="email", type="string", format="email", example="ivan@example.com")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="lectures",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="topic", type="string", example="Введение в программирование"),
     *                     @OA\Property(property="description", type="string", example="Основные понятия"),
     *                     @OA\Property(property="order", type="integer", example=1)
     *                 )
     *             ),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Группа не найдена",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Group not found")
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
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The name has already been taken.")
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
    public function update(UpdateGroupRequest $request, int $id): JsonResponse|GroupResource
    {
        try {
            $updated = $this->groupService->updateGroup($id, $request->validated());

            if (!$updated) {
                return response()->json(['error' => 'Group not found'], 404);
            }

            $group = $this->groupService->getGroupById($id);
            return new GroupResource($group);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/groups/{id}",
     *     summary="Удалить группу",
     *     tags={"Groups"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID группы",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Группа успешно удалена"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Группа не найдена",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Group not found")
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
            $deleted = $this->groupService->deleteGroup($id);

            if (!$deleted) {
                return response()->json(['error' => 'Group not found'], 404);
            }

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/groups/{id}/curriculum",
     *     summary="Обновить учебный план группы",
     *     tags={"Groups"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID группы",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"lectures"},
     *             @OA\Property(
     *                 property="lectures",
     *                 type="array",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Учебный план успешно обновлен",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Curriculum updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Группа не найдена",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Group not found")
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
     *                     property="lectures",
     *                     type="array",
     *                     @OA\Items(type="string", example="The lectures field is required.")
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
    public function updateCurriculum(UpdateGroupCurriculumRequest $request, int $id): JsonResponse
    {
        try {
            $this->groupService->updateCurriculum($id, $request->validated()['lectures']);
            return response()->json(['message' => 'Curriculum updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
