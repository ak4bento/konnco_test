<?php

namespace Tests\Feature;

use App\Jobs\ProcessTransactionJob;
use App\Models\Transaction;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthCase;

class QueueTest extends TestCase
{
    use AuthCase;

    #[Test]
    public function dispatches_a_job_to_process_transaction()
    {
        Queue::fake();

        $token = $this->getAccessToken($this::EMAIL, $this::PASSWORD);
        $user = $this->getUser($this::EMAIL, $this::PASSWORD);

        $responseNewTransaction = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/transactions', [
            'amount' => 100.00,
        ]);

        $responseNewTransaction->assertStatus(201);

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
                'description',
            ]);

        $transaction = Transaction::find($responseNewTransaction->json('data.id'));

        Queue::assertPushed(ProcessTransactionJob::class);
    }
}
