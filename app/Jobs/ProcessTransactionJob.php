<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transaction;

    public $status;

    /**
     * Create a new job instance.
     */
    public function __construct($transaction, $status)
    {
        $this->transaction = $transaction;
        $this->status = $status;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->transaction->update([
            'status' => $this->status,
        ]);
    }
}
