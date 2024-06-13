<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Contracts\PaymentContract;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\PaymentStatusRequest;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\UserSummaryTransactionResource;
use App\Http\Searches\PaymentSearch;
use App\MessageTrait;
use App\Models\Constants;

class PaymentController extends Controller
{
    use MessageTrait;

    private $paymentRepository;

    public function __construct(PaymentContract $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function index()
    {
        $factory = app()->make(PaymentSearch::class);
        $data = $factory->apply()->paginate(request('per_page', 10));

        return PaymentResource::collection($data);
    }

    public function store(PaymentRequest $request)
    {
        $transaction = $this->paymentRepository->createTransaction($request->all());
        $responsePayment = [
            'data' => new PaymentResource($transaction),
        ];
        $response = array_merge($responsePayment, $this->Created());

        return response($response)->setStatusCode(Constants::RESOURCE_CREATED_STATUS);
    }

    public function processTransaction(PaymentStatusRequest $request, $id)
    {
        $isUpdateTransaction = $this->paymentRepository->updateTransaction($request->all(), $id);

        if (! $isUpdateTransaction) {
            return response($this->NotFound())->setStatusCode(Constants::RESOURCE_NOT_FOUND_STATUS);
        }

        return response($this->Ok())->setStatusCode(Constants::RESOURCE_OK_STATUS);
    }

    public function userTransactionSummary()
    {
        $summary = $this->paymentRepository->getUserTransactionSummary();

        $responseSummary = [
            'user' => auth()->user(),
            'data' => new UserSummaryTransactionResource($summary),
        ];
        $response = array_merge($responseSummary, $this->Ok());

        return response($response)->setStatusCode(Constants::RESOURCE_OK_STATUS);
    }

    public function allTransactionSummary()
    {
        $summary = $this->paymentRepository->getAllTransactionSummary();

        return response($summary)->setStatusCode(Constants::RESOURCE_OK_STATUS);
    }
}
