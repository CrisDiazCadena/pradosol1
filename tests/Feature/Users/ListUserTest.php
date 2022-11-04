<?php

namespace Tests\Feature\Partner;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListPartnerTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function can_fetch_a_single_users() //Get a specific user
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $response = $this->getJson(route('api.v1.users.show', $user));

        $response -> assertExactJson([
            'data' => [
                'type' => 'user',
                'id' => (string) $user -> getRouteKey(),
                'attributes' => [
                    'name' => $user->name,
                    'email' => $user->email
                ],
                'links' => [
                    'self' => route('api.v1.users.show', $user)
                ]
            ]
        ]);
    }

    /** @test */

    public function can_fetch_all_users()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->count(3)->create();

        $response = $this->getJson('api.v1.users.index');

        $response -> assertExactJson([
            'data' => [
                [
                    'type' => 'user',
                    'id' => (string) $user[0] -> getRouteKey(),
                    'attributes' => [
                        'name' => $user[0]->name,
                        'email' => $user[0]->email
                    ],
                    'links' => [
                        'self' => route('api.v1.users.show', $user[0])
                    ]
                    ],
                [
                    'type' => 'user',
                    'id' => (string) $user[1] -> getRouteKey(),
                    'attributes' => [
                        'name' => $user[1]->name,
                        'email' => $user[1]->email
                    ],
                    'links' => [
                        'self' => route('api.v1.users.show', $user[1])
                    ]
                    ],
                [
                    'type' => 'user',
                    'id' => (string) $user[2] -> getRouteKey(),
                    'attributes' => [
                        'name' => $user[2]->name,
                        'email' => $user[2]->email
                    ],
                    'links' => [
                        'self' => route('api.v1.users.show', $user[2])
                    ]
                    ],
                
            ],
            'links' => [
                'self' => route('api.v1.users.index')
            ]
        ]);
    }
}
