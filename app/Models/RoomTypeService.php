<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomTypeService extends Model
{
    use HasFactory;

    protected $fillable = ['room_type_id', 'service_id'];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
