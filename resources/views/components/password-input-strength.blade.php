@props([
    'name' => '',
    'id' => '',
    'label' => '',
    'required' => false,
    'placeholder' => 'Enter password',
    'autocomplete' => 'new-password',
])

@php
    $id = $id ?: $name;
    $label = $label ?: ucfirst(str_replace('_', ' ', $name));
@endphp

<div>
    {{-- Label --}}
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    {{-- Password Input with Eye Icon --}}
    <div class="relative" style="position: relative;">
        <input
            type="password"
            id="{{ $id }}"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            autocomplete="{{ $autocomplete }}"
            @if($required) required @endif
            oninput="checkPasswordStrength(this.value, '{{ $id }}-strength')"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 pr-10"
        />

        {{-- Eye Toggle Button --}}
        <button
            type="button"
            onclick="togglePasswordVisibility('{{ $id }}', this)"
            class="absolute top-0 right-0 h-full flex items-center px-3 text-gray-400 hover:text-gray-600 focus:outline-none"
            style="position: absolute; right: 0; top: 0; height: 100%; display: flex; align-items: center; padding: 0 12px; background: transparent; border: none; cursor: pointer; z-index: 5;"
            tabindex="-1"
        >
            <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: block;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <svg class="w-5 h-5 eye-slash-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            </svg>
        </button>
    </div>

    {{-- Password Strength Meter --}}
    <div id="{{ $id }}-strength" class="mt-2" style="display: none;">
        <div class="flex items-center space-x-2">
            <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                <div id="{{ $id }}-strength-bar" class="h-full rounded-full transition-all duration-300" style="width: 0%;"></div>
            </div>
            <span id="{{ $id }}-strength-label" class="text-xs font-medium text-gray-500">Weak</span>
        </div>
        <p class="mt-1 text-xs text-gray-500">Minimum 8 characters with mix of letters, numbers &amp; symbols</p>
    </div>

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<script>
    function togglePasswordVisibility(inputId, button) {
        const input = document.getElementById(inputId);
        if (!input) return;

        const eyeIcon = button.querySelector('.eye-icon');
        const eyeSlashIcon = button.querySelector('.eye-slash-icon');

        if (input.type === 'password') {
            input.type = 'text';
            if (eyeIcon) eyeIcon.style.display = 'none';
            if (eyeSlashIcon) eyeSlashIcon.style.display = 'block';
        } else {
            input.type = 'password';
            if (eyeIcon) eyeIcon.style.display = 'block';
            if (eyeSlashIcon) eyeSlashIcon.style.display = 'none';
        }
    }

    function checkPasswordStrength(password, strengthContainerId) {
        const container = document.getElementById(strengthContainerId);
        if (!container) return;

        const bar = document.getElementById(strengthContainerId + '-bar');
        const label = document.getElementById(strengthContainerId + '-label');

        if (password.length === 0) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'block';

        let score = 0;
        if (password.length >= 8) score++;
        if (password.match(/[a-z]/)) score++;
        if (password.match(/[A-Z]/)) score++;
        if (password.match(/[0-9]/)) score++;
        if (password.match(/[^a-zA-Z0-9]/)) score++;

        const percentage = (score / 5) * 100;
        bar.style.width = percentage + '%';

        let color, text;
        if (score <= 1) { color = '#ef4444'; text = 'Weak'; }
        else if (score <= 3) { color = '#eab308'; text = 'Medium'; }
        else if (score <= 4) { color = '#3b82f6'; text = 'Good'; }
        else { color = '#22c55e'; text = 'Strong'; }

        bar.style.backgroundColor = color;
        label.textContent = text;
        label.style.color = color;
    }
</script>
