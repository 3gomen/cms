<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category',
        'brand',
        'type',
        'color',
        'supplier_id',
        'guarantee',
        'price',
        'sell_price',
        'has_storage',
        'comment',
        'created_at',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
