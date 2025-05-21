<?php

namespace App\Models\dashboard;

use Illuminate\Database\Eloquent\Model;

class PriceDetail extends Model
{
    protected $guarded = [];

    public function pieceResourse(){
        return $this->belongsTo(PieceSource::class,'piece_resource');
    }
}
