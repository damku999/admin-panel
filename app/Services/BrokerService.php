<?php

namespace App\Services;

use App\Contracts\Services\BrokerServiceInterface;
use App\Contracts\Repositories\BrokerRepositoryInterface;
use App\Exports\BrokerExport;
use App\Models\Broker;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class BrokerService implements BrokerServiceInterface
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
        DB::beginTransaction();
        try {
            $broker = $this->brokerRepository->create($data);
            DB::commit();
            return $broker;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function updateBroker(Broker $broker, array $data): Broker
    {
        DB::beginTransaction();
        try {
            $updatedBroker = $this->brokerRepository->update($broker, $data);
            DB::commit();
            return $updatedBroker;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function deleteBroker(Broker $broker): bool
    {
        DB::beginTransaction();
        try {
            $result = $this->brokerRepository->delete($broker);
            DB::commit();
            return $result;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function updateStatus(int $brokerId, int $status): bool
    {
        DB::beginTransaction();
        try {
            $result = $this->brokerRepository->updateStatus($brokerId, $status);
            DB::commit();
            return $result;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
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