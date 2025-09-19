<div>
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($required)
        <span class="text-danger">*</span>
        @endif
    </label>

    @if($type === 'textarea')
    <textarea class="form-control @error($name) is-invalid @enderror"
        id="{{ $name }}"
        name="{{ $name }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($required) required @endif
        @if($rows) rows="{{ $rows }}" @endif>{{ old($name, $value) }}</textarea>
    @else
    <input type="{{ $type }}"
        class="form-control @error($name) is-invalid @enderror"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($required) required @endif>
    @endif

    @error($name)
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>