<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    use HasFactory;

    public const STATUSES = [
        'idea',
        'toteutetaan',
        'aloitettu',
        'tehty 50%',
        'tehty 70%',
        'valmis',
        'hylätään ehdotus',
        'tehdään seuraavaan versioon',
    ];

    public const URGENCIES = [
        'Ei kiirettä',
        'heti',
        'eilen',
    ];

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'urgency',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
