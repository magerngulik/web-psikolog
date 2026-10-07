<?php

namespace App\Livewire\Activities;

use App\Models\Activity;
use App\Models\ActivityAttachment;
use App\Models\Organization;
use Livewire\Component;
use Livewire\WithFileUploads;

class ActivityCreate extends Component
{
    use WithFileUploads;

    public $organization_id = '';
    public $title = '';
    public $event_type = 'seminar';
    public $role = 'keynote_speaker';
    public $start_date;
    public $end_date;
    public $start_time = '09:00';
    public $end_time = '12:00';
    public $delivery_mode = 'offline';
    public $location_venue = '';
    public $organizer_name = '';
    public $event_pic_name = '';
    public $event_pic_phone = '';
    public $target_audience = '';
    public $estimated_audience = 50;
    public $fee = 0;
    public $payment_status = 'unpaid';
    public $payment_method = 'transfer';
    public $skp_points = null;
    public $summary_notes = '';
    public $status = 'scheduled';

    // File attachments
    public $invitation_file;
    public $certificate_file;
    public $photos = [];

    // Quick add organization modal
    public $showNewOrgModal = false;
    public $newOrgName = '';
    public $newOrgCategory = 'school';
    public $newOrgCity = '';
    public $newOrgPicName = '';
    public $newOrgPicPhone = '';

    public function mount()
    {
        $this->start_date = date('Y-m-d');
        $this->organization_id = request()->query('organization_id', '');

        if ($this->organization_id) {
            $org = Organization::find($this->organization_id);
            if ($org) {
                $this->location_venue = $org->name . ($org->city ? ', ' . $org->city : '');
                $this->event_pic_name = $org->pic_name;
                $this->event_pic_phone = $org->pic_phone;
            }
        }
    }

    public function updatedOrganizationId($value)
    {
        if (!empty($value)) {
            $org = Organization::find($value);
            if ($org) {
                if (empty($this->location_venue)) {
                    $this->location_venue = $org->name . ($org->city ? ', ' . $org->city : '');
                }
                if (empty($this->event_pic_name)) {
                    $this->event_pic_name = $org->pic_name;
                }
                if (empty($this->event_pic_phone)) {
                    $this->event_pic_phone = $org->pic_phone;
                }
            }
        }
    }

    public function removePhoto($index)
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    public function removeInvitationFile()
    {
        $this->invitation_file = null;
    }

    public function removeCertificateFile()
    {
        $this->certificate_file = null;
    }

    public function openNewOrgModal()
    {
        $this->newOrgName = '';
        $this->newOrgCategory = 'school';
        $this->newOrgCity = '';
        $this->newOrgPicName = '';
        $this->newOrgPicPhone = '';
        $this->showNewOrgModal = true;
    }

    public function saveNewOrg()
    {
        $this->validate([
            'newOrgName' => 'required|string|min:2|max:255',
            'newOrgCategory' => 'required|string',
            'newOrgCity' => 'nullable|string',
            'newOrgPicName' => 'nullable|string',
            'newOrgPicPhone' => 'nullable|string',
        ]);

        $org = Organization::create([
            'name' => $this->newOrgName,
            'category' => $this->newOrgCategory,
            'city' => $this->newOrgCity,
            'pic_name' => $this->newOrgPicName,
            'pic_phone' => $this->newOrgPicPhone,
        ]);

        $this->organization_id = $org->id;
        $this->location_venue = $org->name . ($org->city ? ', ' . $org->city : '');
        $this->event_pic_name = $org->pic_name;
        $this->event_pic_phone = $org->pic_phone;
        $this->showNewOrgModal = false;
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
            'delivery_mode' => 'required|string|in:offline,online,hybrid',
            'location_venue' => 'nullable|string|max:255',
            'organizer_name' => 'nullable|string|max:255',
            'event_pic_name' => 'nullable|string|max:100',
            'event_pic_phone' => 'nullable|string|max:30',
            'target_audience' => 'nullable|string|max:255',
            'estimated_audience' => 'nullable|numeric|min:0',
            'fee' => 'nullable|numeric|min:0',
            'payment_status' => 'required|string|in:unpaid,paid,waived_pro_bono',
            'payment_method' => 'nullable|string',
            'skp_points' => 'nullable|numeric|min:0',
            'summary_notes' => 'nullable|string',
            'status' => 'required|string|in:scheduled,in_progress,completed,cancelled',
            'invitation_file' => 'nullable|file|max:25600',
            'certificate_file' => 'nullable|file|max:25600',
            'photos.*' => 'nullable|file|max:25600',
        ];
    }

    public function save()
    {
        $this->validate();

        $activity = Activity::create([
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

        // Upload Surat Undangan
        if ($this->invitation_file) {
            try {
                $path = $this->invitation_file->store('activities/invitations', 'public');
                ActivityAttachment::create([
                    'activity_id' => $activity->id,
                    'file_type' => 'invitation_letter',
                    'file_path' => $path,
                    'file_name' => $this->invitation_file->getClientOriginalName(),
                    'mime_type' => $this->invitation_file->getMimeType(),
                    'file_size' => $this->invitation_file->getSize(),
                    'caption' => 'Surat Undangan / Surat Tugas Resmi',
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Upload surat undangan error: ' . $e->getMessage());
            }
        }

        // Upload Sertifikat
        if ($this->certificate_file) {
            try {
                $path = $this->certificate_file->store('activities/certificates', 'public');
                ActivityAttachment::create([
                    'activity_id' => $activity->id,
                    'file_type' => 'certificate',
                    'file_path' => $path,
                    'file_name' => $this->certificate_file->getClientOriginalName(),
                    'mime_type' => $this->certificate_file->getMimeType(),
                    'file_size' => $this->certificate_file->getSize(),
                    'caption' => 'Sertifikat / Piagam Narasumber',
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Upload sertifikat error: ' . $e->getMessage());
            }
        }

        // Upload Galeri Foto Dokumentasi
        if (!empty($this->photos)) {
            foreach ($this->photos as $idx => $photo) {
                try {
                    $path = $photo->store('activities/photos', 'public');
                    ActivityAttachment::create([
                        'activity_id' => $activity->id,
                        'file_type' => 'documentation_photo',
                        'file_path' => $path,
                        'file_name' => $photo->getClientOriginalName(),
                        'mime_type' => $photo->getMimeType(),
                        'file_size' => $photo->getSize(),
                        'caption' => 'Dokumentasi Kegiatan Foto #' . ($idx + 1),
                    ]);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Upload foto error: ' . $e->getMessage());
                }
            }
        }

        session()->flash('message', 'Kegiatan ' . $activity->title . ' berhasil dijadwalkan beserta dokumen lampiran.');
        return redirect()->route('activities.show', $activity->id);
    }

    public function render()
    {
        $organizations = Organization::orderBy('name')->get();

        return view('livewire.activities.activity-create', [
            'organizations' => $organizations,
        ])->layout('components.layouts.app');
    }
}

