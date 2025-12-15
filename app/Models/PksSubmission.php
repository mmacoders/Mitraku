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
        'draft_document_path',
        'signed_document_path',
        'mou_id',
        'validity_period_start',
        'validity_period_end',
    ];

    /**
     * Get the MoU that owns the PKS submission.
     */
    public function mou()
    {
        return $this->belongsTo(Mou::class);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'validity_period_start' => 'date',
        'validity_period_end' => 'date',
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
    
    /**
     * Get the rapat with draft document associated with the PKS submission.
     */
    public function rapatWithDraft()
    {
        return $this->hasOne(Rapat::class)->whereNotNull('draft_document_path');
    }
    
    /**
     * Get the rapat with signed document associated with the PKS submission.
     */
    public function rapatWithSignedDocument()
    {
        return $this->hasOne(Rapat::class)->whereNotNull('signed_document_path');
    }
    
    /**
     * Get the full URL to the draft document.
     */
    public function getDraftDocumentUrlAttribute()
    {
        if ($this->draft_document_path) {
            return asset('storage/' . $this->draft_document_path);
        }
        return null;
    }
    
    /**
     * Get the full URL to the signed document.
     */
    public function getSignedDocumentUrlAttribute()
    {
        if ($this->signed_document_path) {
            return asset('storage/' . $this->signed_document_path);
        }
        return null;
    }
}