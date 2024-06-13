<?php

namespace App\Http\Repositories;

use App\Http\Repositories\Contracts\PaymentContract;
use App\Jobs\ProcessTransactionJob;
use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentRepository extends BaseRepository implements PaymentContract
{
    protected $transaction;

    public function __construct(Transaction $transaction)
    {
        parent::__construct($transaction);
        $this->transaction = $transaction;
    }

    public function createTransaction(array $request)
    {
        return DB::transaction(function () use ($request) {
            $user = auth()->user();
            $storePayment = [
                'user_id' => $user->id,
                'amount' => $request['amount'],
                'status' => 'pending',
            ];

            $data = $this->store($storePayment);

            return $data;
        });
    }

    public function updateTransaction(array $request, $id)
    {
        $transaction = $this->transaction::find($id);

        if (! $transaction) {
            return null;
        }

        $amount = $request['amount'];

        // Lakukan pengecekan apakah jumlah pembayaran sesuai dengan nilai transaksi
        if ($amount == $transaction->amount) {
            // Dispatch job
            // Jika sesuai, ubah status menjadi 'completed'
            dispatch(new ProcessTransactionJob($transaction, 'completed'));
        } else {
            // Dispatch job
            // Jika tidak sesuai, ubah status menjadi 'failed'
            dispatch(new ProcessTransactionJob($transaction, 'failed'));
        }

        return $transaction;
    }

    public function getUserTransactionSummary()
    {
        $user = auth()->user();

        $cacheKey = "user_{$user->id}_transactions_page";

        Log::info("Fetching transactions for user {$user->id}");

        $transactionsUserSummary = Cache::remember($cacheKey, 120, function () use ($user) {

            Log::info('Cache miss for key, querying database');

            $totalTransactions = $this->transaction::where('user_id', $user->id)->count();

            $totalAmountCompleted = $this->transaction::where('user_id', $user->id)
                ->where('status', 'completed')
                ->sum('amount');

            $totalAmountPending = $this->transaction::where('user_id', $user->id)
                ->where('status', 'pending')
                ->sum('amount');

            $totalAmountFailed = $this->transaction::where('user_id', $user->id)
                ->where('status', 'failed')
                ->sum('amount');

            $summary = [
                'total_transactions' => $totalTransactions,
                'total_amount_completed' => $totalAmountCompleted,
                'total_amount_pending' => $totalAmountPending,
                'total_amount_failed' => $totalAmountFailed,
            ];

            return $summary;
        });

        Log::info("Returning transactions for user {$user->id}");

        return $transactionsUserSummary;
    }

    public function getAllTransactionSummary()
    {
        $cacheKey = 'all_transactions_page';

        $transactionsAllSummary = Cache::remember($cacheKey, 60, function () {

            $totalTransactions = Transaction::count();

            $averageAmount = Transaction::avg('amount');

            $highestTransaction = Transaction::orderBy('amount', 'desc')
                ->first();

            $lowestTransaction = Transaction::orderBy('amount', 'asc')
                ->first();

            $longestNameTransaction = Transaction::join('users', 'transactions.user_id', '=', 'users.id')
                ->orderBy(DB::raw('LENGTH(users.name)'), 'desc')
                ->select('transactions.*', 'users.name as user_name')
                ->first();

            $statusDistribution = Transaction::select(DB::raw('status, count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status');

            $summary = [
                'total_transactions' => $totalTransactions,
                'average_amount' => $averageAmount,
                'highest_transaction' => $highestTransaction,
                'lowest_transaction' => $lowestTransaction,
                'longest_name_transaction' => $longestNameTransaction,
                'status_distribution' => [
                    'pending' => $statusDistribution['pending'] ?? 0,
                    'completed' => $statusDistribution['completed'] ?? 0,
                    'failed' => $statusDistribution['failed'] ?? 0,
                ],
            ];

            return $summary;
        });

        return $transactionsAllSummary;
    }
}
