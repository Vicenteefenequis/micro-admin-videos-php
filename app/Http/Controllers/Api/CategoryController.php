<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Core\UseCase\DTO\Category\CategoryInputDto;
use Core\UseCase\DTO\Category\CreateCategory\CategoryCreateInputDto;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesInputDto;
use Core\UseCase\DTO\Category\UpdateCategory\CategoryUpdateInputDto;
use Core\UseCase\Category\{CreateCategoryUseCase,
    DeleteCategoryUseCase,
    ListCategoriesUseCase,
    ListCategoryUseCase,
    UpdateCategoryUseCase
};
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CategoryController extends Controller
{
    public function index(Request $request, ListCategoriesUseCase $useCase)
    {
        $response = $useCase->execute(input: new ListCategoriesInputDto(
            filter: $request->get('filter', ''),
            order: $request->get('order', 'DESC'),
            page: (int)$request->get('page', 1),
            totalPage: (int)$request->get('total_page', 1),
        ));

        return CategoryResource::collection(collect($response->items))
            ->additional([
                'meta' => [
                    'total' => $response->total,
                    'current_page' => $response->current_page,
                    'last_page' => $response->last_page,
                    'first_page' => $response->first_page,
                    'per_page' => $response->per_page,
                    'to' => $response->to,
                    'from' => $response->from,
                ]
            ]);
    }

    public function store(StoreCategoryRequest $request, CreateCategoryUseCase $useCase)
    {

        $response = $useCase->execute(
            input: new CategoryCreateInputDto(
                name: $request->name,
                description: $request->description ?? '',
                isActive: (bool)$request->is_active ?? true,
            )
        );

        return (new CategoryResource($response))->response()->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(ListCategoryUseCase $useCase, $id)
    {
        $response = $useCase->execute(new CategoryInputDto($id));

        return (new CategoryResource($response))->response();
    }

    public function update(UpdateCategoryRequest $request, UpdateCategoryUseCase $useCase, $id)
    {
        $response = $useCase->execute(new CategoryUpdateInputDto(
            id: $id,
            name: $request->name,
            description: $request->description
        ));
        return (new CategoryResource($response))->response();
    }

    public function destroy(DeleteCategoryUseCase $useCase, $id)
    {
        $useCase->execute(new CategoryInputDto($id));
        return response()->noContent();
    }


}
