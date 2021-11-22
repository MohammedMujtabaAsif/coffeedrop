<?php

namespace App\Models;

use App\Models\Userrequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashback extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'coffee',
        'userrequest_id',
        'count',
        'cashback',
    ];

    /**
     * Get the Userrequest that owns the Cashback
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userrequest()
    {
        return $this->belongsTo(Userrequest::class);
    }
}
