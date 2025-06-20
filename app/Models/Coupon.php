<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'discount',
        'expired_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expired_at' => 'date',
        'discount' => 'decimal:2',
    ];

    /**
     * Kiểm tra khuyến mãi còn hiệu lực không
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->expired_at > now();
    }

    /**
     * Scope để lấy các khuyến mãi còn hiệu lực
     */
    public function scopeActive($query)
    {
        return $query->where('expired_at', '>', now());
    }
}
