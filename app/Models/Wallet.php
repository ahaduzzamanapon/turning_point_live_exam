<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // Helper to add mock money (deposit)
    public function deposit($amount, $description = 'Deposit', $referenceId = null)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($amount, $description, $referenceId) {
            $this->increment('balance', $amount);
            $this->transactions()->create([
                'amount' => $amount,
                'type' => 'CREDIT',
                'description' => $description,
                'reference_id' => $referenceId,
            ]);
            return true;
        });
    }

    // Helper to spend money (withdraw)
    public function withdraw($amount, $description = 'Withdraw', $referenceId = null)
    {
        if ($this->balance < $amount) {
            return false;
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($amount, $description, $referenceId) {
            $this->decrement('balance', $amount);
            $this->transactions()->create([
                'amount' => $amount,
                'type' => 'DEBIT',
                'description' => $description,
                'reference_id' => $referenceId,
            ]);
            return true;
        });
    }
}
