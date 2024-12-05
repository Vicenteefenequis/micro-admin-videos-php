<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCastMemberRequest;
use App\Http\Requests\UpdateCastMemberRequest;
use App\Http\Resources\CastMemberResource;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\CastMember\ListCastMemberUseCase;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CastMemberInputDto;
use Core\UseCase\DTO\CastMember\CreateCastMember\CastMemberCreateInputDto;
use Core\UseCase\DTO\CastMember\ListCastMembers\ListCastMembersInputDto;
use Core\UseCase\DTO\CastMember\UpdateCastMember\CastMemberUpdateInputDto;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CastMemberController extends Controller
{
    public function index(Request $request, ListCastMembersUseCase $useCase)
    {
        $response = $useCase->execute(input: new ListCastMembersInputDto(
            filter: $request->get('filter', ''),
            order: $request->get('order', 'DESC'),
            page: (int)$request->get('page', 1),
            totalPage: (int)$request->get('total_page', 1),
        ));

        return CastMemberResource::collection(collect($response->items))
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

    public function store(StoreCastMemberRequest $request, CreateCastMemberUseCase $useCase)
    {

        $response = $useCase->execute(
            input: new CastMemberCreateInputDto(
                name: $request->name,
                type: $request->type
            )
        );

        return (new CastMemberResource($response))->response()->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function show(ListCastMemberUseCase $useCase, $id)
    {
        $response = $useCase->execute(new CastMemberInputDto($id));

        return (new CastMemberResource($response))->response();
    }

    public function update(UpdateCastMemberRequest $request, UpdateCastMemberUseCase $useCase, $id)
    {
        $response = $useCase->execute(new CastMemberUpdateInputDto(
            id: $id,
            name: $request->name,
        ));
        return (new CastMemberResource($response))->response();
    }

    public function destroy(DeleteCastMemberUseCase $useCase, $id)
    {
        $useCase->execute(new CastMemberInputDto($id));
        return response()->noContent();
    }

}
