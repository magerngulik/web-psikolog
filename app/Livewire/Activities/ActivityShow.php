<?php

namespace App\Livewire\Activities;

use App\Models\Activity;
use App\Models\ActivityAttachment;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ActivityShow extends Component
{
    use WithFileUploads;

    public Activity $activity;
    public $summary_notes;

    // Quick upload from show page
    public $upload_type = 'documentation_photo';
    public $uploaded_files = [];
    public $showUploadModal = false;

    // Lightbox modal
    public $selectedPhotoUrl = null;

    public function mount($id)
    {
        $this->activity = Activity::with(['organization', 'attachments'])->findOrFail($id);
        $this->summary_notes = $this->activity->summary_notes;
    }

    public function updateNotes()
    {
        $this->activity->update([
            'summary_notes' => $this->summary_notes,
        ]);
        session()->flash('message', 'Ringkasan materi & catatan evaluasi berhasil disimpan.');
    }

    public function markStatus($status)
    {
        $this->activity->update(['status' => $status]);
        session()->flash('message', 'Status kegiatan berhasil diubah menjadi ' . ucfirst($status));
    }

    public function markPaymentStatus($status)
    {
        $this->activity->update(['payment_status' => $status]);
        session()->flash('message', 'Status pembayaran berhasil diubah.');
    }

    public function openLightbox($url)
    {
        $this->selectedPhotoUrl = $url;
    }

    public function closeLightbox()
    {
        $this->selectedPhotoUrl = null;
    }

    public function deleteAttachment($id)
    {
        $att = ActivityAttachment::where('activity_id', $this->activity->id)->findOrFail($id);
        Storage::disk('public')->delete($att->file_path);
        $att->delete();
        $this->activity->load('attachments');
        session()->flash('message', 'Berkas lampiran berhasil dihapus.');
    }

    public function saveUploadedFiles()
    {
        $this->validate([
            'upload_type' => 'required|string',
            'uploaded_files.*' => 'required|file|max:10240',
        ]);

        foreach ($this->uploaded_files as $file) {
            $folder = match ($this->upload_type) {
                'invitation_letter' => 'activities/invitations',
                'certificate' => 'activities/certificates',
                default => 'activities/photos',
            };
            $path = $file->store($folder, 'public');

            ActivityAttachment::create([
                'activity_id' => $this->activity->id,
                'file_type' => $this->upload_type,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'caption' => ucfirst(str_replace('_', ' ', $this->upload_type)),
            ]);
        }

        $this->uploaded_files = [];
        $this->showUploadModal = false;
        $this->activity->load('attachments');
        session()->flash('message', 'Berkas baru berhasil diunggah.');
    }

    public function deleteActivity()
    {
        $title = $this->activity->title;
        $this->activity->delete();
        session()->flash('message', 'Kegiatan "' . $title . '" berhasil dihapus.');
        return redirect()->route('activities.index');
    }

    public function render()
    {
        return view('livewire.activities.activity-show')->layout('components.layouts.app');
    }
}

