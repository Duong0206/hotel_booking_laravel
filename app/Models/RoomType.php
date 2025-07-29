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

    /**
     * Get the reviews for the room type.
     */
    public function reviews()
    {
        return $this->hasMany(RoomTypeReview::class);
    }

    /**
     * Get the approved reviews for the room type.
     */
    public function approvedReviews()
    {
        return $this->hasMany(RoomTypeReview::class)->where('status', 'approved');
    }

    /**
     * Get the average rating for the room type.
     */
    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    /**
     * Get the reviews count for the room type.
     */
    public function getReviewsCountAttribute()
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Get the services for the room type.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'room_type_services');
    }
} 