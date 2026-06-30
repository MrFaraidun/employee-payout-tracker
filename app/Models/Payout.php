<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    protected $fillable = ['employee_id', 'task', 'amount_iqd', 'status'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
