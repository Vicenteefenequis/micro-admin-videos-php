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
        $response = $this->postJson($this->endpoint,[
            'name' => 'new genre',
            'is_active' => true,
            'categories_ids' => $categories->pluck('id')->toArray()
        ]);

        $response->assertStatus(ResponseAlias::HTTP_CREATED);
    }
}
