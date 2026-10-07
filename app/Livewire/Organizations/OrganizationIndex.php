<?php

namespace App\Livewire\Organizations;

use App\Models\Organization;
use Livewire\Component;
use Livewire\WithPagination;

class OrganizationIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = 'all';

    public $showModal = false;
    public $editingId = null;

    public $name = '';
    public $category = 'school';
    public $address = '';
    public $city = '';
    public $pic_name = '';
    public $pic_phone = '';
    public $pic_position = '';
    public $email = '';
    public $notes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $org = Organization::findOrFail($id);
        $this->editingId = $org->id;
        $this->name = $org->name;
        $this->category = $org->category;
        $this->address = $org->address;
        $this->city = $org->city;
        $this->pic_name = $org->pic_name;
        $this->pic_phone = $org->pic_phone;
        $this->pic_position = $org->pic_position;
        $this->email = $org->email;
        $this->notes = $org->notes;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->category = 'school';
        $this->address = '';
        $this->city = '';
        $this->pic_name = '';
        $this->pic_phone = '';
        $this->pic_position = '';
        $this->email = '';
        $this->notes = '';
        $this->resetValidation();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'category' => 'required|string|in:school,university,community,corporate,government,ngo,other',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'pic_name' => 'nullable|string|max:100',
            'pic_phone' => 'nullable|string|max:30',
            'pic_position' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'notes' => 'nullable|string',
        ];
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            $org = Organization::findOrFail($this->editingId);
            $org->update([
                'name' => $this->name,
                'category' => $this->category,
                'address' => $this->address,
                'city' => $this->city,
                'pic_name' => $this->pic_name,
                'pic_phone' => $this->pic_phone,
                'pic_position' => $this->pic_position,
                'email' => $this->email,
                'notes' => $this->notes,
            ]);
            session()->flash('message', 'Data mitra / organisasi berhasil diperbarui.');
        } else {
            Organization::create([
                'name' => $this->name,
                'category' => $this->category,
                'address' => $this->address,
                'city' => $this->city,
                'pic_name' => $this->pic_name,
                'pic_phone' => $this->pic_phone,
                'pic_position' => $this->pic_position,
                'email' => $this->email,
                'notes' => $this->notes,
            ]);
            session()->flash('message', 'Mitra / organisasi baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $org = Organization::findOrFail($id);
        $org->delete();
        session()->flash('message', 'Data mitra / organisasi berhasil dihapus.');
    }

    public function render()
    {
        $query = Organization::withCount('activities')->latest();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%')
                  ->orWhere('pic_name', 'like', '%' . $this->search . '%')
                  ->orWhere('org_code', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->categoryFilter !== 'all') {
            $query->where('category', $this->categoryFilter);
        }

        $organizations = $query->paginate(10);

        return view('livewire.organizations.organization-index', [
            'organizations' => $organizations,
        ])->layout('components.layouts.app');
    }
}

