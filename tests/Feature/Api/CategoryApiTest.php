<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{

    protected string $endpoint = '/api/categories';

    public function test_list_empty_categories()
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(200);
    }

    public function test_list_all_categories()
    {
        Category::factory()->count(30)->create();

        $response = $this->getJson($this->endpoint);


        $response->assertStatus(200);

        $response->assertJsonStructure([
            'meta' => [
                'total',
                'current_page',
                'last_page',
                'first_page',
                'per_page',
                'to',
                'from'
            ]
        ]);
    }

    public function test_list_paginate_categories()
    {
        Category::factory()->count(30)->create();

        $response = $this->getJson("$this->endpoint?page=2");

        $response->assertStatus(200);

        $this->assertEquals(2, $response->json('meta.current_page'));
        $this->assertEquals(30, $response->json('meta.total'));
    }

    public function test_list_category_not_found()
    {
        $response = $this->getJson("$this->endpoint/fake_value");
        $response->assertStatus(404);
    }

    public function test_list_category()
    {
        $category = Category::factory()->create();
        $response = $this->getJson("$this->endpoint/{$category->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at'
            ]
        ]);

        $this->assertEquals($category->id, $response->json('data.id'));
    }

    public function test_validations_store()
    {
        $data = [];
        $response = $this->postJson($this->endpoint, $data);

        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name'
            ]
        ]);
    }

    public function test_store()
    {
        $data = [
            'name' => 'New Category',
        ];
        $response = $this->postJson($this->endpoint, $data);
        $response->assertStatus(ResponseAlias::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at'
            ]
        ]);
    }

    public function test_not_found_update()
    {
        $data = [
            'name' => 'New Category',
        ];
        $response = $this->putJson("$this->endpoint/fake_value", $data);

        $response->assertStatus(ResponseAlias::HTTP_NOT_FOUND);
    }

    public function test_validations_update()
    {
        $response = $this->putJson("$this->endpoint/fake_value", []);

        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name'
            ]
        ]);
    }

    public function test_update()
    {
        $data = [
            'name' => 'Category Updated',
        ];
        $category = Category::factory()->create();
        $response = $this->putJson("$this->endpoint/$category->id", $data);

        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertDatabaseHas('categories', [
            'name' => 'Category Updated',
        ]);
    }


}
