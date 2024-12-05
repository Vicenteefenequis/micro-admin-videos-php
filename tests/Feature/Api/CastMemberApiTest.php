<?php

namespace Tests\Feature\Api;

use App\Models\CastMember;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class CastMemberApiTest extends TestCase
{
    protected string $endpoint = '/api/cast_members';

    public function test_get_all_empty()
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    public function test_get_all()
    {
        CastMember::factory()->count(50)->create();
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(15, 'data');
    }

    public function test_paginate_two()
    {
        CastMember::factory()->count(25)->create();
        $response = $this->getJson("$this->endpoint?page=2");

        $response->assertStatus(ResponseAlias::HTTP_OK);

        $this->assertEquals(2, $response->json('meta.current_page'));
        $this->assertEquals(25, $response->json('meta.total'));
        $response->assertJsonCount(10, 'data');
    }

    public function test_get_by_id_not_found()
    {
        $response = $this->getJson("$this->endpoint/fake_value");
        $response->assertStatus(ResponseAlias::HTTP_NOT_FOUND);
    }

    public function test_get_by()
    {
        $castMember = CastMember::factory()->create();
        $response = $this->getJson("$this->endpoint/{$castMember->id}");
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'created_at'
            ]
        ]);

    }
}
