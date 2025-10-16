<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rapat extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rapat';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'judul',
        'deskripsi',
        'tanggal_waktu',
        'lokasi',
        'user_id',
        'status',
        'pks_document_path',
        'pks_submission_id', // Add this field
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_waktu' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that created the rapat.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the mitra invited to the rapat.
     */
    public function invitedMitra()
    {
        return $this->belongsToMany(User::class, 'rapat_mitra', 'rapat_id', 'user_id')
                    ->withPivot('status_kehadiran')
                    ->withTimestamps();
    }
    
    /**
     * Get the PKS submission associated with this meeting.
     */
    public function pksSubmission()
    {
        return $this->belongsTo(PksSubmission::class, 'pks_submission_id');
    }
    
    /**
     * Get the full URL to the PKS document.
     */
    public function getPksDocumentUrlAttribute()
    {
        if ($this->pks_document_path) {
            return asset('storage/' . $this->pks_document_path);
        }
        return null;
    }
}