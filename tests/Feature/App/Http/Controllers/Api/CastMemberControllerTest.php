<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreCastMemberRequest;
use App\Http\Requests\UpdateCastMemberRequest;
use App\Models\CastMember as Model;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\CastMember\ListCastMemberUseCase;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class CastMemberControllerTest extends TestCase
{
    protected CastMemberEloquentRepository $repository;
    protected CastMemberController $controller;

    protected function setUp(): void
    {
        $this->repository = new CastMemberEloquentRepository(new Model());
        $this->controller = new CastMemberController();
        parent::setUp();
    }

    public function test_index()
    {
        $useCase = new ListCastMembersUseCase($this->repository);
        $response = $this->controller->index(new Request(), $useCase);

        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    public function test_store()
    {
        $useCase = new CreateCastMemberUseCase($this->repository);
        $request = new StoreCastMemberRequest();
        $request->headers->set('Content-Type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => 'Teste',
            'type' => 1
        ]));
        $response = $this->controller->store($request, $useCase);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(ResponseAlias::HTTP_CREATED, $response->getStatusCode());
    }

    public function test_show()
    {
        $castMember = Model::factory()->create();
        $response = $this->controller->show(
            useCase: new ListCastMemberUseCase($this->repository),
            id: $castMember->id,
        );
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(ResponseAlias::HTTP_OK, $response->getStatusCode());
    }

    public function test_update()
    {
        $castMember = Model::factory()->create();
        $request = new UpdateCastMemberRequest();
        $request->headers->set('Content-Type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => 'Updated',
            'type' => 2
        ]));
        $response = $this->controller->update(
            request: $request,
            useCase: new UpdateCastMemberUseCase($this->repository),
            id: $castMember->id,
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(ResponseAlias::HTTP_OK, $response->getStatusCode());
    }

    public function test_delete()
    {
        $castMember = Model::factory()->create();

        $response = $this->controller->destroy(
            useCase: new DeleteCastMemberUseCase($this->repository),
            id: $castMember->id,
        );

        $this->assertEquals(ResponseAlias::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
