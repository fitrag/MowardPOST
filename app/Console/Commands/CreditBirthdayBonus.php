<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreditBirthdayBonus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:birthday-bonus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Credit birthday bonus points to customers in their birthday month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Find customers with birthdays this month who haven't received bonus this year
        $customers = \App\Models\Customer::where('status', 'active')
            ->whereMonth('date_of_birth', $currentMonth)
            ->whereNotNull('date_of_birth')
            ->get();
        
        $credited = 0;
        
        foreach ($customers as $customer) {
            // Check if already received birthday bonus this year
            $alreadyReceived = \App\Models\CustomerPoint::where('customer_id', $customer->id)
                ->where('type', 'birthday_bonus')
                ->whereYear('created_at', $currentYear)
                ->exists();
            
            if (!$alreadyReceived) {
                // Credit 500 points birthday bonus
                $customer->addPoints(
                    500,
                    'birthday_bonus',
                    'Birthday bonus for ' . $customer->date_of_birth->format('F')
                );
                
                $credited++;
                $this->info("✓ Credited 500 points to {$customer->name} (Birthday: {$customer->date_of_birth->format('M d')})");
            }
        }
        
        if ($credited > 0) {
            $this->info("\n🎉 Successfully credited birthday bonus to {$credited} customer(s)!");
        } else {
            $this->info("ℹ️  No customers eligible for birthday bonus today.");
        }
        
        return 0;
    }
}
