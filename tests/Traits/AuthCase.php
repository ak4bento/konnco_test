<?php

namespace Tests\Traits;

use Error;

trait AuthCase
{
    private const EMAIL = 'admin@akil.co.id';
    private const PASSWORD = 'Ve5JbvSCBXBkdni';

    //return json from authentication
    public function getAccessToken(string $username, string $password)
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.auth.login'), [
            'email' => $username,
            'password' => $password,
        ]);

        if (! $response['token']) {
            throw new Error('Login Failed', 401);
        }

        return $response['token'];
    }

    public function getUser(string $username, string $password)
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson(route('api.auth.login'), [
            'email' => $username,
            'password' => $password,
        ]);

        if (! $response['data']) {
            throw new Error('Login Failed', 401);
        }

        return $response['data'];
    }
}
