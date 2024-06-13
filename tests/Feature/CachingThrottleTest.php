<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthCase;

class CachingThrottleTest extends TestCase
{
    use AuthCase;

    #[Test]
    public function caches_all_transaction_summary()
    {
        $valueTotalTransactions = 5;
        $token = $this->getAccessToken($this::EMAIL, $this::PASSWORD);

        $cacheKey = 'all_transactions_page';

        Cache::shouldReceive('remember')
            ->once()
            ->with($cacheKey, 60, \Closure::class)
            ->andReturn([
                'total_transactions' => $valueTotalTransactions,
                'average_amount' => 150.00,
                'highest_transaction' => [
                    'id' => 1,
                    'user_id' => 1,
                    'amount' => 200.00,
                    'status' => 'completed',
                    'created_at' => '2024-06-13T08:36:02.000000Z',
                    'updated_at' => '2024-06-13T08:36:02.000000Z',
                ],
                'lowest_transaction' => [
                    'id' => 2,
                    'user_id' => 1,
                    'amount' => 100.00,
                    'status' => 'completed',
                    'created_at' => '2024-06-13T08:36:02.000000Z',
                    'updated_at' => '2024-06-13T08:36:02.000000Z',
                ],
                'longest_name_transaction' => [
                    'id' => 3,
                    'user_id' => 1,
                    'amount' => 150.00,
                    'status' => 'completed',
                    'created_at' => '2024-06-13T08:36:02.000000Z',
                    'updated_at' => '2024-06-13T08:36:02.000000Z',
                    'user_name' => 'John Doe',
                ],
                'status_distribution' => [
                    'pending' => 1,
                    'completed' => 3,
                    'failed' => 1,
                ],
            ]);

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
                    'updated_at',
                ],
                'lowest_transaction' => [
                    'id',
                    'user_id',
                    'amount',
                    'status',
                    'created_at',
                    'updated_at',
                ],
                'longest_name_transaction' => [
                    'id',
                    'user_id',
                    'amount',
                    'status',
                    'created_at',
                    'updated_at',
                    'user_name',
                ],
                'status_distribution' => [
                    'pending',
                    'completed',
                    'failed',
                ],
            ]);
        $this->assertEquals($response->json('total_transactions'), $valueTotalTransactions);
    }

    #[Test]
    public function throttles_requests()
    {
        $user = $this->getUser($this::EMAIL, $this::PASSWORD);
        $token = $this->getAccessToken($this::EMAIL, $this::PASSWORD);

        for ($i = 0; $i < 130; $i++) {
            $response = $this->withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ])->getJson('/api/user/transactions');

            if ($i < 119) {
                $response->assertStatus(200);
            } else {
                $response->assertStatus(429); // Too Many Requests
            }
        }
    }
}
