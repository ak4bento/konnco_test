<?php

namespace Tests\Feature\Auth;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    #[Test]
    public function user_can_login()
    {
        // Account User
        // username: 'admin@akil.co.id'
        // password: 'Ve5JbvSCBXBkdni'
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.auth.login'), [
            'email' => 'admin@akil.co.id',
            'password' => 'Ve5JbvSCBXBkdni',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'description',
            'data' => [
                'id',
                'name',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at',
            ],
            'token',
        ]);
    }

    #[Test]
    public function invalid_email()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.auth.login'), [
            'email' => 'usermailinator.com', // invalid email
            'password' => 'userApp123!',
        ]);

        $response->assertStatus(400);
    }

    #[Test]
    public function email_not_provided()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.auth.login'), [
            'email' => '', // invalid email
            'password' => 'userApp123!',
        ]);

        $response->assertStatus(400);
    }

    #[Test]
    public function password_not_provided()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.auth.login'), [
            'email' => 'user@mailinator.com',
            'password' => '',
        ]);

        $response->assertStatus(400);
    }
}
