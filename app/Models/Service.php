<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'service_category_id', 'price', 'description'];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function roomTypes()
    {
        return $this->belongsToMany(RoomType::class, 'room_type_services');
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_services');
    }
}