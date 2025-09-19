<?php

namespace App\Services;

use App\Contracts\Services\BrokerServiceInterface;
use App\Contracts\Repositories\BrokerRepositoryInterface;
use App\Exports\BrokerExport;
use App\Models\Broker;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Broker Service
 *
 * Handles Broker business logic operations.
 * Inherits transaction management from BaseService.
 */
class BrokerService extends BaseService implements BrokerServiceInterface
{
    public function __construct(
        private BrokerRepositoryInterface $brokerRepository
    ) {}
    
    public function getBrokers(Request $request): LengthAwarePaginator
    {
        return $this->brokerRepository->getPaginated($request);
    }
    
    public function createBroker(array $data): Broker
    {
        return $this->createInTransaction(
            fn() => $this->brokerRepository->create($data)
        );
    }

    public function updateBroker(Broker $broker, array $data): Broker
    {
        return $this->updateInTransaction(
            fn() => $this->brokerRepository->update($broker, $data)
        );
    }

    public function deleteBroker(Broker $broker): bool
    {
        return $this->deleteInTransaction(
            fn() => $this->brokerRepository->delete($broker)
        );
    }

    public function updateStatus(int $brokerId, int $status): bool
    {
        return $this->executeInTransaction(
            fn() => $this->brokerRepository->updateStatus($brokerId, $status)
        );
    }
    
    public function exportBrokers(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new BrokerExport, 'brokers.xlsx');
    }
    
    public function getActiveBrokers(): Collection
    {
        return $this->brokerRepository->getActive();
    }
}