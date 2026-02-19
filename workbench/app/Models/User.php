<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'title',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'json',
            'is_active' => 'json',
        ];
    }

    protected static function newFactory(): \Workbench\Database\Factories\UserFactory
    {
        return \Workbench\Database\Factories\UserFactory::new();
    }
}
