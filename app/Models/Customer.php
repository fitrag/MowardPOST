<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Customer extends Model
{
    protected $fillable = [
        'card_number',
        'name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'member_tier',
        'member_since',
        'total_points',
        'total_spent',
        'total_transactions',
        'status',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'member_since' => 'date',
        'total_points' => 'integer',
        'total_spent' => 'decimal:2',
        'total_transactions' => 'integer',
    ];

    // Relationships
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function pointHistory()
    {
        return $this->hasMany(CustomerPoint::class)->orderBy('created_at', 'desc');
    }

    // Helper Methods
    public static function generateCardNumber()
    {
        $date = now()->format('Ymd');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $cardNumber = "CUST-{$date}-{$random}";
        
        // Ensure uniqueness
        while (self::where('card_number', $cardNumber)->exists()) {
            $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $cardNumber = "CUST-{$date}-{$random}";
        }
        
        return $cardNumber;
    }

    public function addPoints($points, $type, $description, $transactionId = null)
    {
        CustomerPoint::create([
            'customer_id' => $this->id,
            'transaction_id' => $transactionId,
            'points' => $points,
            'type' => $type,
            'description' => $description,
        ]);

        $this->increment('total_points', $points);
    }

    public function redeemPoints($points, $description, $transactionId = null)
    {
        if ($this->total_points < $points) {
            return false;
        }

        CustomerPoint::create([
            'customer_id' => $this->id,
            'transaction_id' => $transactionId,
            'points' => -$points,
            'type' => 'redeem',
            'description' => $description,
        ]);

        $this->decrement('total_points', $points);
        return true;
    }

    public function updateStats($transactionTotal)
    {
        $this->increment('total_spent', $transactionTotal);
        $this->increment('total_transactions');
        
        // Auto upgrade tier
        $this->upgradeTier();
    }

    public function upgradeTier()
    {
        $totalSpent = $this->total_spent;
        
        if ($totalSpent >= 15000000 && $this->member_tier !== 'platinum') {
            $this->update(['member_tier' => 'platinum']);
        } elseif ($totalSpent >= 5000000 && $this->member_tier === 'silver') {
            $this->update(['member_tier' => 'gold']);
        }
    }

    public function getPointMultiplier()
    {
        return match($this->member_tier) {
            'platinum' => 2.0,
            'gold' => 1.5,
            default => 1.0,
        };
    }

    public function getTierBadgeAttribute()
    {
        return match($this->member_tier) {
            'platinum' => '💎 Platinum',
            'gold' => '🥇 Gold',
            default => '🥈 Silver',
        };
    }
}
