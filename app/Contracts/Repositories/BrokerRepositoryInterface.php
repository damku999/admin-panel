<?php

namespace App\Contracts\Repositories;

use App\Models\Broker;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BrokerRepositoryInterface
{
    public function getPaginated(Request $request, int $perPage = 10): LengthAwarePaginator;
    
    public function create(array $data): Broker;
    
    public function update(Broker $broker, array $data): Broker;
    
    public function delete(Broker $broker): bool;
    
    public function findById(int $id): ?Broker;
    
    public function updateStatus(int $id, int $status): bool;
    
    public function getActive(): Collection;
    
    public function getAllForExport(): Collection;
}