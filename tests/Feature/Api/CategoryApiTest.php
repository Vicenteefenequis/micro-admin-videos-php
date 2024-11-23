<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{

    protected string $endpoint = '/api/categories/';

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
                'last_page',
                'first_page',
                'per_page',
                'to',
                'from'
            ]
        ]);
    }
}
