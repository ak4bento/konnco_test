<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthCase;

class PaymentControllerTest extends TestCase
{
    use AuthCase;
    
    #[Test]
    public function can_create_a_transaction()
    {
        $token = $this->getAccessToken($this::EMAIL, $this::PASSWORD);
        $user = $this->getUser($this::EMAIL, $this::PASSWORD);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/transactions', [
            'amount' => 100.00,
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                    'data' => [
                        'id',
                        'user' => [
                            'id',
                            'name',
                            'email',
                        ],
                        'amount',
                        'status',
                        'created_at',
                        'updated_at'
                    ],
                    'status',
                    'message',
                    'description'
                ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user['id'],
            'amount' => 100.00,
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function cant_create_a_transaction_without_amount()
    {
        $token = $this->getAccessToken($this::EMAIL, $this::PASSWORD);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/transactions');

        $response->assertStatus(400);
    }

    #[Test]
    public function cant_create_a_transaction_without_token()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson('/api/transactions', [
            'amount' => 100.00,
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function can_update_a_transaction()
    {
        $token = $this->getAccessToken($this::EMAIL, $this::PASSWORD);
        $user = $this->getUser($this::EMAIL, $this::PASSWORD);

        $responseNewTransaction = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/transactions', [
            'amount' => 100.00,
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->patchJson('/api/transactions/'.$responseNewTransaction['data']['id'], [
            'amount' => 100.00,
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'status',
                    'message',
                    'description'
                ]);

        $this->assertDatabaseHas('transactions', [
            'id' => $responseNewTransaction['data']['id'],
            'user_id' => $user['id'],
            'amount' => 100.00,
            'status' => 'completed',
        ]);
    }

    #[Test]
    public function cant_update_a_transaction_without_amount()
    {
        $token = $this->getAccessToken($this::EMAIL, $this::PASSWORD);

        $responseNewTransaction = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/transactions', [
            'amount' => 100.00,
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->patchJson('/api/transactions/'.$responseNewTransaction['data']['id']);

        $response->assertStatus(400);
    }

    #[Test]
    public function can_get_user_transactions()
    {
        $token = $this->getAccessToken($this::EMAIL, $this::PASSWORD);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/user/transactions');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'user' => [
                                'id',
                                'name',
                                'email'
                            ],
                            'amount',
                            'status',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'links' => [
                        'first',
                        'last',
                        'prev',
                        'next'
                    ],
                    'meta' => [
                        'current_page',
                        'from',
                        'last_page',
                        'links' => [
                            '*' => [
                                'url',
                                'label',
                                'active'
                            ]
                        ],
                        'path',
                        'per_page',
                        'to',
                        'total'
                    ]
                ]);
    }

    #[Test]
    public function cant_get_user_transactions_without_token()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->getJson('/api/user/transactions');

        $response->assertStatus(401);
    }

    #[Test]
    public function can_get_user_transactions_summary()
    {
        $token = $this->getAccessToken($this::EMAIL, $this::PASSWORD);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/user/transactions/summary');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at'
                    ],
                    'data' => [
                        'total_transactions',
                        'total_amount_completed',
                        'total_amount_pending',
                        'total_amount_failed'
                    ],
                    'status',
                    'message',
                    'description'
                ]);
    }

    #[Test]
    public function cant_get_user_transactions_summary_without_token()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->getJson('/api/user/transactions/summary');

        $response->assertStatus(401);
    }

    #[Test]
    public function can_get_all_transactions_summary()
    {
        $token = $this->getAccessToken($this::EMAIL, $this::PASSWORD);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/user/transactions/all-summary');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'total_transactions',
                    'average_amount',
                    'highest_transaction' => [
                        'id',
                        'user_id',
                        'amount',
                        'status',
                        'created_at',
                        'updated_at'
                    ],
                    'lowest_transaction' => [
                        'id',
                        'user_id',
                        'amount',
                        'status',
                        'created_at',
                        'updated_at'
                    ],
                    'longest_name_transaction' => [
                        'id',
                        'user_id',
                        'amount',
                        'status',
                        'created_at',
                        'updated_at',
                        'user_name'
                    ],
                    'status_distribution' => [
                        'pending',
                        'completed',
                        'failed'
                    ]
                ]);
    }

    #[Test]
    public function cant_get_all_transactions_summary_without_token()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->getJson('/api/user/transactions/all-summary');

        $response->assertStatus(401);
    }
}
