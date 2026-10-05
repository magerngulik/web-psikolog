<?php

namespace App\Livewire\Settings;

use App\Models\SystemReference;
use Livewire\Component;

class ReferenceManager extends Component
{
    public $group_key = 'case_category';
    public $label = '';
    public $value = '';
    public $editingId = null;

    protected $rules = [
        'group_key' => 'required|string',
        'label' => 'required|string|max:255',
        'value' => 'required|string|max:255',
    ];

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            $ref = SystemReference::findOrFail($this->editingId);
            $ref->update([
                'label' => $this->label,
                'value' => $this->value,
            ]);
            session()->flash('message', 'Opsi berhasil diperbarui!');
        } else {
            SystemReference::create([
                'group_key' => $this->group_key,
                'label' => $this->label,
                'value' => $this->value,
                'sort_order' => SystemReference::where('group_key', $this->group_key)->count() + 1,
            ]);
            session()->flash('message', 'Opsi baru berhasil ditambahkan!');
        }

        $this->reset(['label', 'value', 'editingId']);
    }

    public function edit($id)
    {
        $ref = SystemReference::findOrFail($id);
        $this->editingId = $ref->id;
        $this->group_key = $ref->group_key;
        $this->label = $ref->label;
        $this->value = $ref->value;
    }

    public function toggleActive($id)
    {
        $ref = SystemReference::findOrFail($id);
        $ref->update(['is_active' => !$ref->is_active]);
    }

    public function render()
    {
        return view('livewire.settings.reference-manager', [
            'references' => SystemReference::where('group_key', $this->group_key)
                ->orderBy('sort_order')
                ->get(),
        ])->layout('components.layouts.app');
    }
}
