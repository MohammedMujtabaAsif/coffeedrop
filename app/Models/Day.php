<?php

namespace App\Models;

use App\Enums\Days;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'location_id',
        'day',
        'opentime',
        'closetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
    ];

    /**
     * The attributes that should be appeneded when the model is retrieved.
     *
     */
    protected $appends = [
        'dayname',
    ];

    /**
     * Retrieve the name of the day based upon it's id from the Days enum.
     */
    public function getDaynameAttribute()
    {
        return Days::getNameFromId($this->attributes['day']);
    }
}
