@props([
    'name', 'label', 'type' => 'text', 'required' => false, 'placeholder' => null, 
    'value' => null, 'rows' => null, 'class' => null, 'labelAfter' => false
])

@unless ($labelAfter)
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
@endunless

@if ($type === 'textarea')
    <textarea
        class="form-control @error($name) is-invalid @enderror {{ $class }}"
        id="{{ $name }}" 
        name="{{ $name }}"
        placeholder="{{ $placeholder ?: ($labelAfter ? ' ' : '') }}"
        @if ($required) required @endif
        @if ($rows) rows="{{ $rows }}" @endif
        {{ $attributes }}>{{ old($name, $value) }}</textarea>
@else
    <input 
        type="{{ $type }}"
        class="form-control @error($name) is-invalid @enderror {{ $class }}"
        id="{{ $name }}" 
        name="{{ $name }}" 
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder ?: ($labelAfter ? ' ' : '') }}"
        @if ($required) required @endif
        {{ $attributes }}>
@endif

@if ($labelAfter)
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
@endif

@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
