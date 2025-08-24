<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, check for duplicate emails and update them
        $this->fixDuplicateEmails();
        
        // Then add unique constraint
        Schema::table('customers', function (Blueprint $table) {
            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });
    }

    /**
     * Fix duplicate emails by appending customer ID
     */
    private function fixDuplicateEmails(): void
    {
        $duplicateEmails = \DB::table('customers')
            ->select('email', \DB::raw('COUNT(*) as count'))
            ->groupBy('email')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicateEmails as $duplicate) {
            $customers = \DB::table('customers')
                ->where('email', $duplicate->email)
                ->orderBy('id')
                ->get();

            // Keep first customer with original email, update others
            foreach ($customers->skip(1) as $index => $customer) {
                $newEmail = str_replace('@', "+{$customer->id}@", $customer->email);
                \DB::table('customers')
                    ->where('id', $customer->id)
                    ->update(['email' => $newEmail]);
            }
        }
    }
};
