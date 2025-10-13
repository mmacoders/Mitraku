<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PksSubmission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'document_path',
        'status',
        'revision_notes',
        'final_document_path',
        'digital_signature',
        'signed_by',
        'signed_at',
        'partner_name',
        'phone',
        'kak_document_path',
        'mou_document_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the PKS submission.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the status histories for the PKS submission.
     */
    public function statusHistories()
    {
        return $this->hasMany(StatusHistory::class);
    }

    /**
     * Get the rapat associated with the PKS submission.
     */
    public function rapat()
    {
        return $this->hasOne(Rapat::class);
    }
}