<?php

namespace Wotz\FilamentTableFilterPresets\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Wotz\FilamentTableFilterPresets\Database\Factories\SavedTableFilterFactory;

class SavedTableFilter extends Model
{
    use HasFactory;

    protected $fillable = [
        'administrator_id',
        'resource',
        'name',
        'filters',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'json',
            'is_default' => 'boolean',
        ];
    }

    public function administrator(): BelongsTo
    {
        return $this->belongsTo(config('filament-table-filter-presets.administrator_model'), 'administrator_id');
    }

    protected static function newFactory(): SavedTableFilterFactory
    {
        return SavedTableFilterFactory::new();
    }
}
