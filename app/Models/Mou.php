<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mou extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'document_path',
        'status',
        'validity_period_start',
        'validity_period_end',
    ];

    protected $casts = [
        'validity_period_start' => 'date',
        'validity_period_end' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pksSubmissions()
    {
        return $this->hasMany(PksSubmission::class);
    }

    /**
     * Get the rapat associated with the MoU.
     */
    public function rapat()
    {
        return $this->hasOne(Rapat::class);
    }
}
