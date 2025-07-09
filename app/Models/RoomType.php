<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'capacity'
    ];

    /**
     * Get the rooms for the room type.
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Quan hệ với các khuyến mại có thể áp dụng cho loại phòng này
     */
    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_room_type');
    }
} 