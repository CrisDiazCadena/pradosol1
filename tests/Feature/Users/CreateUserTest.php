<?php

namespace Tests\Feature\Partners;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function can_create_users()
    {
        $this->withoutExceptionHandling();

        $response = $this->postJson(route('api.v1.users.create'), [
            'data' => [
                'type' => 'users',
                'attributes' => [
                    'type_identification' => 'cc',
                    'identification_card'  => '123456',
                    'password' => 'password',
                    'name' => 'name',
                    'lastname' => 'lastname',
                    'status' => 'active',
                    'address' => 'calle real 123',
                    'email' => 'email@correo.com',
                    'phone' => '78945123'
                ]
            ]
    ]);

    $response->assertCreated();

    $user = User::first();

    $response->assertJson([
        'data' => [
            'type' => 'users',
            'id' => (string) $user->getRouteKey(),
            'attributes' => [
                'type_identification' => 'cc',
                'identification_card'  => '123456',
                'password' => 'password',
                'name' => 'name',
                'lastname' => 'lastname',
                'status' => 'active',
                'address' => 'calle real 123',
                'email' => 'email@correo.com',
                'phone' => '78945123'
            ],
            'links' => [
                'self' => route('api.v1.users.show', $user)
            ]
        ]
    ]);
    }
}
