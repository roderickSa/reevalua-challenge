<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'year',
        'month',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function loans()
    {
        return $this->hasMany(ReportLoan::class);
    }

    public function otherDebts()
    {
        return $this->hasMany(ReportOtherDebt::class);
    }

    public function creditCards()
    {
        return $this->hasMany(ReportCreditCard::class);
    }
}
