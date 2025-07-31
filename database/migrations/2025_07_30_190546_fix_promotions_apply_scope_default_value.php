<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix any existing promotions with null apply_scope
        DB::table('promotions')
            ->whereNull('apply_scope')
            ->update(['apply_scope' => 'all']);
            
        // Also fix any promotions where apply_scope doesn't match their actual relationships
        $promotions = DB::table('promotions')->get();
        
        foreach ($promotions as $promotion) {
            $hasRooms = DB::table('promotion_room')
                ->where('promotion_id', $promotion->id)
                ->exists();
                
            $hasRoomTypes = DB::table('promotion_room_type')
                ->where('promotion_id', $promotion->id)
                ->exists();
            
            $correctScope = 'all';
            if ($hasRooms) {
                $correctScope = 'specific_rooms';
            } elseif ($hasRoomTypes) {
                $correctScope = 'room_types';
            }
            
            // Update nếu apply_scope không khớp với relationships
            if ($promotion->apply_scope !== $correctScope) {
                DB::table('promotions')
                    ->where('id', $promotion->id)
                    ->update(['apply_scope' => $correctScope]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần reverse vì đây là data fix
    }
};
