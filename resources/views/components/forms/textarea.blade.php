@props([
    'name', 
    'label', 
    'required' => false, 
    'placeholder' => null, 
    'value' => null, 
    'rows' => 4, 
    'class' => null,
    'labelAfter' => false
])

@unless ($labelAfter)
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
@endunless

<textarea
    class="form-control @error($name) is-invalid @enderror {{ $class }}"
    id="{{ $name }}" 
    name="{{ $name }}"
    rows="{{ $rows }}"
    placeholder="{{ $placeholder ?: ($labelAfter ? ' ' : '') }}"
    @if ($required) required @endif
    {{ $attributes }}>{{ old($name, $value) }}</textarea>

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