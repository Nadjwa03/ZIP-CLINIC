<?php

namespace App\Http\Livewire\Admin\Patients;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Patient;
use App\Models\PatientMedia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaGallery extends Component
{
    use WithFileUploads;

    public $patient;
    public $files = [];
    public $mediaType = 'PHOTO_INTRAORAL';
    public $description = '';
    public $toothCode = '';
    public $takenAt;
    public $visitId = null;
    public $showUploadModal = false;
    public $selectedMedia = null;
    public $showViewModal = false;
    public $filterCategory = 'all'; // all, photo, xray, document

    protected $rules = [
        'files.*' => 'file|max:10240', // 10MB max
        'mediaType' => 'required',
        'takenAt' => 'nullable|date',
    ];

    public function mount($patientId)
    {
        $this->patient = Patient::with(['visits'])->findOrFail($patientId);
        $this->takenAt = now()->format('Y-m-d');
    }

    public function openUploadModal()
    {
        $this->showUploadModal = true;
        $this->takenAt = now()->format('Y-m-d');
    }

    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->reset(['files', 'mediaType', 'description', 'toothCode', 'visitId']);
        $this->takenAt = now()->format('Y-m-d');
    }

    public function uploadFiles()
    {
        $this->validate();

        foreach ($this->files as $file) {
            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $extension;
            
            // Determine storage path based on media type
            $category = $this->getCategoryFromType($this->mediaType);
            $storagePath = 'patient-media/' . $this->patient->patient_id . '/' . $category;
            
            // Store file
            $path = $file->storeAs($storagePath, $filename, 'public');

            // Create database record
            PatientMedia::create([
                'patient_id' => $this->patient->patient_id,
                'visit_id' => $this->visitId,
                'media_type' => $this->mediaType,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'description' => $this->description,
                'tooth_code' => $this->toothCode,
                'uploaded_by' => auth()->id(),
                'taken_at' => $this->takenAt,
                'is_active' => true,
            ]);
        }

        session()->flash('success', count($this->files) . ' file berhasil diupload!');
        
        $this->closeUploadModal();
        $this->patient->refresh();
    }

    private function getCategoryFromType($type)
    {
        if (str_starts_with($type, 'PHOTO_')) return 'photos';
        if (str_starts_with($type, 'XRAY_')) return 'xrays';
        if (str_starts_with($type, 'DOC_')) return 'documents';
        return 'other';
    }

    public function viewMedia($mediaId)
    {
        $this->selectedMedia = PatientMedia::with(['uploader', 'visit'])->find($mediaId);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedMedia = null;
    }

    public function toggleActive($mediaId)
    {
        $media = PatientMedia::find($mediaId);
        $media->update([
            'is_active' => !$media->is_active
        ]);

        $this->patient->refresh();
        session()->flash('success', 'Status media diupdate!');
    }

    public function deleteMedia($mediaId)
    {
        $media = PatientMedia::find($mediaId);
        
        // Soft delete with audit
        $media->update([
            'deleted_by' => auth()->id(),
            'deleted_at' => now(),
        ]);

        $this->patient->refresh();
        $this->closeViewModal();
        session()->flash('success', 'Media berhasil dihapus!');
    }

    public function render()
    {
        // Get media based on filter
        $mediaQuery = $this->patient->media()->with('uploader');

        if ($this->filterCategory === 'photo') {
            $mediaQuery->photos();
        } elseif ($this->filterCategory === 'xray') {
            $mediaQuery->xrays();
        } elseif ($this->filterCategory === 'document') {
            $mediaQuery->documents();
        }

        $media = $mediaQuery->get();

        return view('livewire.admin.patients.media-gallery', [
            'mediaList' => $media,
            'photoTypes' => PatientMedia::getPhotoTypes(),
            'xrayTypes' => PatientMedia::getXrayTypes(),
            'documentTypes' => PatientMedia::getDocumentTypes(),
        ]);
    }
}
