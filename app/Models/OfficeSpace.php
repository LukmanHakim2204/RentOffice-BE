<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeSpace extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'description',
        'about',
        'city_id',
        'is_open',
        'is_fullbook',
        'photo',
        'price',
        'duration',
        'address',
    ];

    public  function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = str($value)->slug();
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function photos()
    {
        return $this->hasMany(OfficeSpacePhoto::class);
    }

    public function benefits()
    {
        return $this->hasMany(OfficeSpaceBenefit::class);
    }
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
