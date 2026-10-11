@props([
    'id' => null,
    'placeholder' => 'Pilih tanggal...',
    'maxDate' => null,
    'max_date' => null,
    'minDate' => null,
    'min_date' => null,
    'altFormat' => 'j F Y',
])

@php
    $wireModel = $attributes->wire('model')->value();
    $resolvedMaxDate = $maxDate ?? $max_date ?? null;
    $resolvedMinDate = $minDate ?? $min_date ?? null;
@endphp

<div wire:ignore
     wire:key="{{ $id ? 'dp_cnt_'.$id : 'dp_cnt_'.uniqid() }}"
     x-data="{
    value: @if($wireModel) @entangle($attributes->wire('model')) @else null @endif,
    picker: null,
    setupYearDropdown(instance) {
        if (!instance || !instance.calendarContainer) return;
        const monthContainers = instance.calendarContainer.querySelectorAll('.flatpickr-current-month');
        monthContainers.forEach((monthContainer) => {
            let yearSelect = monthContainer.querySelector('.flatpickr-monthDropdown-years');
            const numInput = monthContainer.querySelector('.numInputWrapper');
            if (numInput) {
                numInput.style.display = 'none';
            }

            const currentYear = instance.currentYear || new Date().getFullYear();
            let minYear = instance.config.minDate ? instance.config.minDate.getFullYear() : null;
            let maxYear = instance.config.maxDate ? instance.config.maxDate.getFullYear() : null;
            const isPastOrDob = maxYear && maxYear <= new Date().getFullYear();

            if (!minYear && !maxYear) {
                minYear = currentYear - 10;
                maxYear = currentYear + 10;
            } else if (isPastOrDob) {
                if (!minYear) minYear = maxYear - 100;
            } else {
                if (!minYear) minYear = currentYear - 10;
                if (!maxYear) maxYear = currentYear + 10;
            }

            minYear = Math.min(minYear, currentYear);
            maxYear = Math.max(maxYear, currentYear);

            if (!yearSelect) {
                yearSelect = document.createElement('select');
                yearSelect.className = 'flatpickr-monthDropdown-years';
                yearSelect.setAttribute('aria-label', 'Tahun');

                if (isPastOrDob) {
                    for (let y = maxYear; y >= minYear; y--) {
                        const opt = document.createElement('option');
                        opt.value = y;
                        opt.textContent = y;
                        yearSelect.appendChild(opt);
                    }
                } else {
                    for (let y = minYear; y <= maxYear; y++) {
                        const opt = document.createElement('option');
                        opt.value = y;
                        opt.textContent = y;
                        yearSelect.appendChild(opt);
                    }
                }

                yearSelect.addEventListener('change', (e) => {
                    const selectedYr = parseInt(e.target.value, 10);
                    instance.changeYear(selectedYr);
                    this.trimTrailingNextMonthRows(instance);
                });
                yearSelect.addEventListener('click', (e) => e.stopPropagation());
                yearSelect.addEventListener('mousedown', (e) => e.stopPropagation());

                if (numInput && numInput.parentNode) {
                    numInput.parentNode.insertBefore(yearSelect, numInput.nextSibling);
                } else {
                    monthContainer.appendChild(yearSelect);
                }
            }

            yearSelect.value = currentYear;
        });
    },
    trimTrailingNextMonthRows(instance) {
        if (!instance) return;
        const container = instance.days || instance.calendarContainer?.querySelector('.dayContainer');
        if (!container || !container.children) return;
        const days = container.children;
        if (days.length === 0) return;

        for (let i = 0; i < days.length; i++) {
            days[i].style.display = '';
        }

        for (let rowStart = days.length - 7; rowStart >= 28; rowStart -= 7) {
            let allNextMonth = true;
            for (let i = rowStart; i < rowStart + 7; i++) {
                if (!days[i] || !days[i].classList.contains('nextMonthDay')) {
                    allNextMonth = false;
                    break;
                }
            }
            if (allNextMonth) {
                for (let i = rowStart; i < rowStart + 7; i++) {
                    if (days[i]) days[i].style.display = 'none';
                }
            } else {
                break;
            }
        }
    },
    init() {
        this.picker = flatpickr(this.$refs.input, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: '{{ $altFormat }}',
            altInputClass: 'w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition font-mono pr-10 cursor-pointer',
            locale: 'id',
            defaultDate: this.value,
            maxDate: @js($resolvedMaxDate),
            minDate: @js($resolvedMinDate),
            monthSelectorType: 'dropdown',
            onReady: (selectedDates, dateStr, instance) => {
                this.setupYearDropdown(instance);
                this.trimTrailingNextMonthRows(instance);
            },
            onOpen: (selectedDates, dateStr, instance) => {
                this.setupYearDropdown(instance);
                this.trimTrailingNextMonthRows(instance);
            },
            onMonthChange: (selectedDates, dateStr, instance) => {
                this.$nextTick(() => {
                    this.setupYearDropdown(instance);
                    this.trimTrailingNextMonthRows(instance);
                });
            },
            onYearChange: (selectedDates, dateStr, instance) => {
                this.$nextTick(() => {
                    this.setupYearDropdown(instance);
                    this.trimTrailingNextMonthRows(instance);
                });
            },
            onChange: (selectedDates, dateStr) => {
                this.value = dateStr;
            }
        });

        this.$watch('value', (newVal) => {
            if (this.picker) {
                if (!newVal) {
                    this.picker.clear(false);
                } else if (newVal !== this.picker.input.value) {
                    this.picker.setDate(newVal, false);
                }
            }
        });
    },
    destroy() {
        if (this.picker) {
            this.picker.destroy();
        }
    }
}" class="relative w-full">
    <input x-ref="input"
           type="text"
           id="{{ $id }}"
           placeholder="{{ $placeholder }}"
           {{ $attributes->whereDoesntStartWith('wire:model')->class([
               'w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition font-mono pr-10'
           ]) }} />
    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-teal-400/70">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
    </div>
</div>
