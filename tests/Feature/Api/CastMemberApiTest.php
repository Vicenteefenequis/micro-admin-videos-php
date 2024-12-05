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
}
