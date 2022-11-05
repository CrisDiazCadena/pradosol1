<?php

namespace Tests\Feature\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function can_update_users()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $response = $this->putJson(route('api.v1.users.update', $user), [
        
            'password' => 'Update password',
            'status' => 'updated active',
            'address' => 'updated address',
            'email' => 'updated email',
            'phone' => 'updated phone'
                
    ])->assertOK();


    $user = User::first();

    $response->assertHeader(
        'Location',
        route('api.v1.users.show', $user)
    );

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
