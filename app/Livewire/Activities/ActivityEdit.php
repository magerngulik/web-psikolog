<?php

namespace App\Livewire\Activities;

use App\Models\Activity;
use App\Models\ActivityAttachment;
use App\Models\Organization;
use Livewire\Component;
use Livewire\WithFileUploads;

class ActivityEdit extends Component
{
    use WithFileUploads;

    public Activity $activity;

    public $organization_id;
    public $title;
    public $event_type;
    public $role;
    public $start_date;
    public $end_date;
    public $start_time;
    public $end_time;
    public $delivery_mode;
    public $location_venue;
    public $organizer_name;
    public $event_pic_name;
    public $event_pic_phone;
    public $target_audience;
    public $estimated_audience;
    public $fee;
    public $payment_status;
    public $payment_method;
    public $skp_points;
    public $summary_notes;
    public $status;

    // Additional file uploads
    public $new_invitation_file;
    public $new_certificate_file;
    public $new_photos = [];

    public function mount($id)
    {
        $this->activity = Activity::with(['organization', 'attachments'])->findOrFail($id);

        $this->organization_id = $this->activity->organization_id;
        $this->title = $this->activity->title;
        $this->event_type = $this->activity->event_type;
        $this->role = $this->activity->role;
        $this->start_date = $this->activity->start_date ? $this->activity->start_date->format('Y-m-d') : null;
        $this->end_date = $this->activity->end_date ? $this->activity->end_date->format('Y-m-d') : null;
        $this->start_time = $this->activity->start_time ? substr($this->activity->start_time, 0, 5) : null;
        $this->end_time = $this->activity->end_time ? substr($this->activity->end_time, 0, 5) : null;
        $this->delivery_mode = $this->activity->delivery_mode;
        $this->location_venue = $this->activity->location_venue;
        $this->organizer_name = $this->activity->organizer_name;
        $this->event_pic_name = $this->activity->event_pic_name;
        $this->event_pic_phone = $this->activity->event_pic_phone;
        $this->target_audience = $this->activity->target_audience;
        $this->estimated_audience = $this->activity->estimated_audience;
        $this->fee = $this->activity->fee;
        $this->payment_status = $this->activity->payment_status;
        $this->payment_method = $this->activity->payment_method;
        $this->skp_points = $this->activity->skp_points;
        $this->summary_notes = $this->activity->summary_notes;
        $this->status = $this->activity->status;
    }

    protected function rules()
    {
        return [
            'title' => 'required|string|min:3|max:255',
            'event_type' => 'required|string',
            'role' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'delivery_mode' => 'required|string',
            'location_venue' => 'nullable|string|max:255',
            'organizer_name' => 'nullable|string|max:255',
            'event_pic_name' => 'nullable|string|max:100',
            'event_pic_phone' => 'nullable|string|max:30',
            'target_audience' => 'nullable|string|max:255',
            'estimated_audience' => 'nullable|numeric|min:0',
            'fee' => 'nullable|numeric|min:0',
            'payment_status' => 'required|string',
            'payment_method' => 'nullable|string',
            'skp_points' => 'nullable|numeric|min:0',
            'summary_notes' => 'nullable|string',
            'status' => 'required|string',
            'new_invitation_file' => 'nullable|file|max:25600',
            'new_certificate_file' => 'nullable|file|max:25600',
            'new_photos.*' => 'nullable|file|max:25600',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->activity->update([
            'organization_id' => !empty($this->organization_id) ? $this->organization_id : null,
            'title' => $this->title,
            'event_type' => $this->event_type ?: 'seminar',
            'role' => $this->role ?: 'keynote_speaker',
            'start_date' => $this->start_date,
            'end_date' => !empty($this->end_date) ? $this->end_date : $this->start_date,
            'start_time' => !empty($this->start_time) ? $this->start_time : null,
            'end_time' => !empty($this->end_time) ? $this->end_time : null,
            'delivery_mode' => $this->delivery_mode ?: 'offline',
            'location_venue' => $this->location_venue,
            'organizer_name' => $this->organizer_name,
            'event_pic_name' => $this->event_pic_name,
            'event_pic_phone' => $this->event_pic_phone,
            'target_audience' => $this->target_audience,
            'estimated_audience' => !empty($this->estimated_audience) ? (int)$this->estimated_audience : null,
            'fee' => !empty($this->fee) ? (float)$this->fee : 0,
            'payment_status' => $this->payment_status ?: 'unpaid',
            'payment_method' => $this->payment_method,
            'skp_points' => !empty($this->skp_points) ? (float)$this->skp_points : null,
            'summary_notes' => $this->summary_notes,
            'status' => $this->status ?: 'scheduled',
        ]);

        if ($this->new_invitation_file) {
            try {
                $path = $this->new_invitation_file->store('activities/invitations', 'public');
                ActivityAttachment::create([
                    'activity_id' => $this->activity->id,
                    'file_type' => 'invitation_letter',
                    'file_path' => $path,
                    'file_name' => $this->new_invitation_file->getClientOriginalName(),
                    'mime_type' => $this->new_invitation_file->getMimeType(),
                    'file_size' => $this->new_invitation_file->getSize(),
                    'caption' => 'Surat Undangan / Surat Tugas Resmi',
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Upload surat undangan error: ' . $e->getMessage());
            }
        }

        if ($this->new_certificate_file) {
            try {
                $path = $this->new_certificate_file->store('activities/certificates', 'public');
                ActivityAttachment::create([
                    'activity_id' => $this->activity->id,
                    'file_type' => 'certificate',
                    'file_path' => $path,
                    'file_name' => $this->new_certificate_file->getClientOriginalName(),
                    'mime_type' => $this->new_certificate_file->getMimeType(),
                    'file_size' => $this->new_certificate_file->getSize(),
                    'caption' => 'Sertifikat / Piagam Narasumber',
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Upload sertifikat error: ' . $e->getMessage());
            }
        }

        if (!empty($this->new_photos)) {
            foreach ($this->new_photos as $idx => $photo) {
                try {
                    $path = $photo->store('activities/photos', 'public');
                    ActivityAttachment::create([
                        'activity_id' => $this->activity->id,
                        'file_type' => 'documentation_photo',
                        'file_path' => $path,
                        'file_name' => $photo->getClientOriginalName(),
                        'mime_type' => $photo->getMimeType(),
                        'file_size' => $photo->getSize(),
                        'caption' => 'Dokumentasi Foto Tambahan',
                    ]);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Upload foto error: ' . $e->getMessage());
                }
            }
        }

        session()->flash('message', 'Perubahan kegiatan berhasil disimpan.');
        return redirect()->route('activities.show', $this->activity->id);
    }

    public function deleteAttachment($id)
    {
        $att = ActivityAttachment::where('activity_id', $this->activity->id)->findOrFail($id);
        \Illuminate\Support\Facades\Storage::disk('public')->delete($att->file_path);
        $att->delete();
        $this->activity->load('attachments');
        session()->flash('message', 'Berkas lampiran berhasil dihapus.');
    }

    public function deleteActivity()
    {
        $title = $this->activity->title;
        $this->activity->delete();
        session()->flash('message', 'Kegiatan "' . $title . '" berhasil dihapus.');
        return redirect()->route('activities.index');
    }

    public function removeNewInvitationFile()
    {
        $this->new_invitation_file = null;
    }

    public function removeNewCertificateFile()
    {
        $this->new_certificate_file = null;
    }

    public function removeNewPhoto($index)
    {
        unset($this->new_photos[$index]);
        $this->new_photos = array_values($this->new_photos);
    }

    public function render()
    {
        $organizations = Organization::orderBy('name')->get();

        return view('livewire.activities.activity-edit', [
            'organizations' => $organizations,
        ])->layout('components.layouts.app');
    }
}

