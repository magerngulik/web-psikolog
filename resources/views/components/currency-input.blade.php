@props([
    'id' => null,
    'placeholder' => '0',
    'disabled' => false,
])

@php
    $wireModel = $attributes->wire('model')->value();
@endphp

<div x-data="{
    raw: @if($wireModel) @entangle($attributes->wire('model')) @else 0 @endif,
    display: '',
    format(val) {
        if (val === null || val === undefined || val === '') return '';
        let num = parseInt(val, 10);
        return isNaN(num) ? '' : new Intl.NumberFormat('id-ID').format(num);
    },
    syncFromRaw() {
        this.display = this.format(this.raw);
    },
    onInput(e) {
        let clean = e.target.value.replace(/[^0-9]/g, '');
        this.raw = clean ? parseInt(clean, 10) : 0;
        this.display = this.format(this.raw);
    }
}" x-init="syncFromRaw(); $watch('raw', () => syncFromRaw())" class="relative w-full">
    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-teal-400 font-mono text-xs font-bold">
        Rp
    </div>
    <input type="text"
           id="{{ $id }}"
           x-model="display"
           @input="onInput"
           {{ $disabled ? 'disabled' : '' }}
           placeholder="{{ $placeholder }}"
           {{ $attributes->whereDoesntStartWith('wire:model')->class([
               'w-full bg-spruce-900 border border-teal-500/20 rounded-xl pl-11 pr-4 py-2.5 text-white font-mono text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition disabled:bg-spruce-950/80 disabled:text-slate-400 disabled:border-teal-500/10 disabled:cursor-not-allowed'
           ]) }}>
</div>

