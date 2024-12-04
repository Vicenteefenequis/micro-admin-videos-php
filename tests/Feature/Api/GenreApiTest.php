<?php

namespace Tests\Feature\Api;

use App\Models\Genre as Model;
use App\Models\Category as CategoryModel;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class GenreApiTest extends TestCase
{
    protected $endpoint = '/api/genres';

    public function test_list_all_empty()
    {
        $response = $this->getJson($this->endpoint);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    public function test_list_all()
    {
        Model::factory()->count(20)->create();
        $response = $this->getJson($this->endpoint);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(15, 'data');
    }

    public function test_store()
    {
        $categories = CategoryModel::factory()->count(10)->create();
        $response = $this->postJson($this->endpoint, [
            'name' => 'new genre',
            'is_active' => true,
            'categories_ids' => $categories->pluck('id')->toArray()
        ]);

        $response->assertStatus(ResponseAlias::HTTP_CREATED);
    }

    public function test_validation_store()
    {
        $categories = CategoryModel::factory()->count(2)->create();

        $payload = [
            'name' => '',
            'is_active' => true,
            'categories_ids' => $categories->pluck('id')->toArray()
        ];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name'
            ]
        ]);
    }

    public function test_show_not_found()
    {
        $response = $this->getJson("{$this->endpoint}/fake_value");
        $response->assertStatus(ResponseAlias::HTTP_NOT_FOUND);
    }

    public function test_show()
    {
        $genre = Model::factory()->create();
        $response = $this->getJson("{$this->endpoint}/{$genre->id}");
        $response->assertStatus(ResponseAlias::HTTP_OK);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active'
            ]
        ]);

    }

    public function test_update_not_found()
    {
        $categories = CategoryModel::factory()->count(2)->create();
        $response = $this->putJson("{$this->endpoint}/fake_value", [
            'name' => 'New Name',
            'categories_ids' => $categories->pluck('id')->toArray()
        ]);
        $response->assertStatus(ResponseAlias::HTTP_NOT_FOUND);
    }


    public function test_update()
    {
        $genre = Model::factory()->create();
        $categories = CategoryModel::factory()->count(2)->create();
        $response = $this->putJson("{$this->endpoint}/{$genre->id}", [
            'name' => 'New Name',
            'categories_ids' => $categories->pluck('id')->toArray()
        ]);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active'
            ]
        ]);
    }

    public function test_update_validations()
    {

        $response = $this->putJson("{$this->endpoint}/fake_value", [
            'name' => 'New Name',
            'categories_ids' => []
        ]);
        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'categories_ids'
            ]
        ]);
    }

    public function test_destroy_not_found()
    {
        $response = $this->deleteJson("{$this->endpoint}/fake_value");
        $response->assertStatus(ResponseAlias::HTTP_NOT_FOUND);
    }

    public function test_delete()
    {
        $genre = Model::factory()->create();
        $response = $this->deleteJson("{$this->endpoint}/{$genre->id}");
        $response->assertStatus(ResponseAlias::HTTP_NO_CONTENT);
    }

}
