<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    protected $guarded = [];

    protected $casts = [
        'entry_date' => 'date',
        'posting_date' => 'datetime'
    ];

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function validateBalanced(): bool
    {
        $totalDebit = $this->journalEntryLines()->sum('debit_amount');
        $totalCredit = $this->journalEntryLines()->sum('credit_amount');
        
        return $totalDebit == $totalCredit;
    }
} 