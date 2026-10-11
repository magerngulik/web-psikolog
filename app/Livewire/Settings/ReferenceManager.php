<?php

namespace App\Livewire\Settings;

use App\Models\SystemReference;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReferenceManager extends Component
{
    public $group_key = 'case_category';
    public $label = '';
    public $value = '';
    public $editingId = null;

    protected function rules()
    {
        return [
            'group_key' => 'required|string',
            'label' => 'required|string|max:255',
            'value' => in_array($this->group_key, ['psychological_intervention', 'case_category']) ? 'nullable|string|max:255' : 'required|string|max:255',
        ];
    }

    public function mount()
    {
        $this->resequenceGroup();
    }

    public function resequenceGroup(?string $groupKey = null): void
    {
        $group = $groupKey ?: $this->group_key;
        $items = SystemReference::where('group_key', $group)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $order = 1;
        foreach ($items as $item) {
            if ($item->sort_order !== $order) {
                $item->update(['sort_order' => $order]);
            }
            $order++;
        }
    }

    public function switchGroup(string $group)
    {
        $this->group_key = $group;
        $this->reset(['label', 'value', 'editingId']);
        $this->resequenceGroup();
    }

    public function save()
    {
        $this->validate();

        // Auto-assign numeric value for psychological_intervention if left blank
        if ($this->group_key === 'psychological_intervention' && empty(trim((string) $this->value))) {
            $maxVal = SystemReference::where('group_key', 'psychological_intervention')
                ->pluck('value')
                ->filter(fn ($v) => is_numeric($v))
                ->map(fn ($v) => (int) $v)
                ->max();
            $this->value = (string) (($maxVal ?: 0) + 1);
        }

        // Auto-assign value from label for case_category if left blank
        if ($this->group_key === 'case_category' && empty(trim((string) $this->value))) {
            $this->value = $this->label;
        }

        if ($this->editingId) {
            $ref = SystemReference::findOrFail($this->editingId);
            $ref->update([
                'label' => $this->label,
                'value' => $this->value,
            ]);
            session()->flash('message', 'Opsi berhasil diperbarui!');
        } else {
            $this->resequenceGroup();

            // Smart placement: Jika opsi paling bawah adalah 'Lainnya' / 'Other',
            // sisipkan opsi baru tepat sebelum 'Lainnya' agar 'Lainnya' tetap berada di urutan terakhir.
            $lastItem = SystemReference::where('group_key', $this->group_key)
                ->orderBy('sort_order', 'desc')
                ->first();

            $isLastItemLainnya = false;
            if ($lastItem) {
                $normalizedLabel = strtolower(trim($lastItem->label));
                $normalizedValue = strtolower(trim($lastItem->value));
                if (str_contains($normalizedLabel, 'lainnya') || 
                    str_contains($normalizedLabel, 'other') || 
                    str_contains($normalizedLabel, 'dll') ||
                    $normalizedValue === 'lainnya' || 
                    $normalizedValue === 'other') {
                    $isLastItemLainnya = true;
                }
            }

            if ($isLastItemLainnya && $lastItem) {
                $insertOrder = $lastItem->sort_order;
                $lastItem->update(['sort_order' => $insertOrder + 1]);
            } else {
                $insertOrder = (SystemReference::where('group_key', $this->group_key)->max('sort_order') ?: 0) + 1;
            }

            SystemReference::create([
                'group_key' => $this->group_key,
                'label' => $this->label,
                'value' => $this->value,
                'sort_order' => $insertOrder,
            ]);

            $this->resequenceGroup();
            session()->flash('message', 'Opsi baru berhasil ditambahkan!');
        }

        $this->reset(['label', 'value', 'editingId']);
    }

    public function moveUp($id)
    {
        $this->resequenceGroup();

        $current = SystemReference::findOrFail($id);
        $previous = SystemReference::where('group_key', $this->group_key)
            ->where('sort_order', '<', $current->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($previous) {
            DB::transaction(function () use ($current, $previous) {
                $currentOrder = $current->sort_order;
                $prevOrder = $previous->sort_order;

                $current->update(['sort_order' => $prevOrder]);
                $previous->update(['sort_order' => $currentOrder]);
            });

            $this->resequenceGroup();
            session()->flash('message', "Urutan '{$current->label}' berhasil dinaikkan.");
        }
    }

    public function moveDown($id)
    {
        $this->resequenceGroup();

        $current = SystemReference::findOrFail($id);
        $next = SystemReference::where('group_key', $this->group_key)
            ->where('sort_order', '>', $current->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();

        if ($next) {
            DB::transaction(function () use ($current, $next) {
                $currentOrder = $current->sort_order;
                $nextOrder = $next->sort_order;

                $current->update(['sort_order' => $nextOrder]);
                $next->update(['sort_order' => $currentOrder]);
            });

            $this->resequenceGroup();
            session()->flash('message', "Urutan '{$current->label}' berhasil diturunkan.");
        }
    }

    public function edit($id)
    {
        $ref = SystemReference::findOrFail($id);
        $this->editingId = $ref->id;
        $this->group_key = $ref->group_key;
        $this->label = $ref->label;
        $this->value = $ref->value;
    }

    public function cancelEdit()
    {
        $this->reset(['label', 'value', 'editingId']);
    }

    public function toggleActive($id)
    {
        $ref = SystemReference::findOrFail($id);
        $ref->update(['is_active' => !$ref->is_active]);
    }

    public function delete($id)
    {
        $ref = SystemReference::findOrFail($id);
        $label = $ref->label;
        $ref->delete();
        $this->resequenceGroup();
        session()->flash('message', "Opsi '{$label}' berhasil dihapus!");
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
