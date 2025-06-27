<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =
    [
        'name',
        'phone_number',
        'booking_trx_id',
        'is_paid',
        'office_space_id',
        'total_amount',
        'duration',
        'started_at',
        'ended_at'
    ];

    public static function generateUniqueTrxId()
    {
        $trx = 'FO';
        do {
            $randomString = $trx . mt_rand(1000, 9999);
        } while (self::where('booking_trx_id', $randomString)->exists());
        return $randomString;
    }
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);

    }
    public function officeSpace()
    {
        return $this->belongsTo(OfficeSpace::class);
    }
}
