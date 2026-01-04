<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class PatientMedia extends Model
{
    use SoftDeletes;

    protected $table = 'patient_media';

    protected $fillable = [
        'patient_id',
        'visit_id',
        'media_type',
        'path',
        'original_name',
        'file_size',
        'mime_type',
        'description',
        'tooth_code',
        'uploaded_by',
        'taken_at',
        'is_active',
        'deleted_by',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Media type constants
     */
    const TYPE_PHOTO_INTRAORAL = 'PHOTO_INTRAORAL';
    const TYPE_PHOTO_EXTRAORAL = 'PHOTO_EXTRAORAL';
    const TYPE_PHOTO_BEFORE = 'PHOTO_BEFORE';
    const TYPE_PHOTO_AFTER = 'PHOTO_AFTER';
    const TYPE_PHOTO_PROGRESS = 'PHOTO_PROGRESS';
    const TYPE_XRAY_PERIAPICAL = 'XRAY_PERIAPICAL';
    const TYPE_XRAY_PANORAMIC = 'XRAY_PANORAMIC';
    const TYPE_XRAY_CEPHALOMETRIC = 'XRAY_CEPHALOMETRIC';
    const TYPE_XRAY_BITEWING = 'XRAY_BITEWING';
    const TYPE_DOC_CONSENT = 'DOC_CONSENT_FORM';
    const TYPE_DOC_MEDICAL = 'DOC_MEDICAL_REPORT';
    const TYPE_DOC_LAB = 'DOC_LAB_RESULT';
    const TYPE_DOC_OTHER = 'DOC_OTHER';

    /**
     * Get all photo types
     */
    public static function getPhotoTypes()
    {
        return [
            self::TYPE_PHOTO_INTRAORAL => 'Foto Intraoral',
            self::TYPE_PHOTO_EXTRAORAL => 'Foto Extraoral',
            self::TYPE_PHOTO_BEFORE => 'Foto Sebelum',
            self::TYPE_PHOTO_AFTER => 'Foto Sesudah',
            self::TYPE_PHOTO_PROGRESS => 'Foto Progress',
        ];
    }

    /**
     * Get all x-ray types
     */
    public static function getXrayTypes()
    {
        return [
            self::TYPE_XRAY_PERIAPICAL => 'X-Ray Periapical',
            self::TYPE_XRAY_PANORAMIC => 'X-Ray Panoramic',
            self::TYPE_XRAY_CEPHALOMETRIC => 'X-Ray Cephalometric',
            self::TYPE_XRAY_BITEWING => 'X-Ray Bitewing',
        ];
    }

    /**
     * Get all document types
     */
    public static function getDocumentTypes()
    {
        return [
            self::TYPE_DOC_CONSENT => 'Form Persetujuan',
            self::TYPE_DOC_MEDICAL => 'Laporan Medis',
            self::TYPE_DOC_LAB => 'Hasil Lab',
            self::TYPE_DOC_OTHER => 'Dokumen Lainnya',
        ];
    }

    /**
     * Get all media types
     */
    public static function getAllTypes()
    {
        return array_merge(
            self::getPhotoTypes(),
            self::getXrayTypes(),
            self::getDocumentTypes()
        );
    }

    /**
     * Get the patient that owns the media
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the visit associated with the media
     */
    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Get the user who uploaded the media
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the user who deleted the media
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get the full URL for the media
     */
    public function getMediaUrlAttribute()
    {
        return Storage::url($this->path);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return '-';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->file_size;
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get formatted taken date
     */
    public function getFormattedTakenAtAttribute()
    {
        if (!$this->taken_at) {
            return $this->created_at->format('d M Y');
        }
        
        return $this->taken_at->format('d M Y');
    }

    /**
     * Get media type label
     */
    public function getMediaTypeLabelAttribute()
    {
        $types = self::getAllTypes();
        return $types[$this->media_type] ?? $this->media_type;
    }

    /**
     * Check if media is a photo
     */
    public function isPhoto()
    {
        return str_starts_with($this->media_type, 'PHOTO_');
    }

    /**
     * Check if media is an x-ray
     */
    public function isXray()
    {
        return str_starts_with($this->media_type, 'XRAY_');
    }

    /**
     * Check if media is a document
     */
    public function isDocument()
    {
        return str_starts_with($this->media_type, 'DOC_');
    }

    /**
     * Scope to get only active media
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only photos
     */
    public function scopePhotos($query)
    {
        return $query->where('media_type', 'like', 'PHOTO_%');
    }

    /**
     * Scope to get only x-rays
     */
    public function scopeXrays($query)
    {
        return $query->where('media_type', 'like', 'XRAY_%');
    }

    /**
     * Scope to get only documents
     */
    public function scopeDocuments($query)
    {
        return $query->where('media_type', 'like', 'DOC_%');
    }

    /**
     * Scope to filter by media type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('media_type', $type);
    }

    /**
     * Get media category (photo/xray/document)
     */
    public function getCategoryAttribute()
    {
        if ($this->isPhoto()) return 'photo';
        if ($this->isXray()) return 'xray';
        if ($this->isDocument()) return 'document';
        return 'other';
    }

    /**
     * Get color class for media type badge
     */
    public function getBadgeColorAttribute()
    {
        if ($this->isPhoto()) return 'blue';
        if ($this->isXray()) return 'purple';
        if ($this->isDocument()) return 'gray';
        return 'gray';
    }
}
