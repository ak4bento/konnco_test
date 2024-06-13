<?php

namespace Tests\Unit;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    #[Test]
    public function can_create_a_transaction() : void
    {
        $user = User::factory()->create();
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'amount' => 100.00,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'user_id' => $user->id,
            'amount' => 100.00,
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function can_update_transaction_status() : void
    {
        $transaction = Transaction::factory()->create([
            'status' => 'pending',
        ]);

        $transaction->update(['status' => 'completed']);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'completed',
        ]);
    }
}
