<?php

namespace App\Contracts\Repositories;

use App\Models\CustomerInsurance;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CustomerInsuranceRepositoryInterface
{
    public function getPaginated(Request $request, int $perPage = 10): LengthAwarePaginator;
    
    public function create(array $data): CustomerInsurance;
    
    public function update(CustomerInsurance $customerInsurance, array $data): CustomerInsurance;
    
    public function delete(CustomerInsurance $customerInsurance): bool;
    
    public function findById(int $id): ?CustomerInsurance;
    
    public function updateStatus(int $id, int $status): bool;
    
    public function getByCustomerId(int $customerId): Collection;
    
    public function getAllForExport(): Collection;
    
    public function getExpiringPolicies(int $days = 30): Collection;
    
    public function findWithRelations(int $id, array $relations = []): ?CustomerInsurance;
}