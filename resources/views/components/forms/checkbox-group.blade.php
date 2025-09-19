<div class="mb-3">
    <label class="form-label">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    
    <div class="border rounded p-3">
        @foreach($options as $key => $optionLabel)
            <div class="form-check">
                <input type="checkbox" 
                       class="form-check-input" 
                       id="{{ $name }}_{{ $key }}" 
                       name="{{ $name }}[{{ $key }}]" 
                       value="1"
                       @if(old("{$name}.{$key}", $values[$key] ?? false)) checked @endif>
                <label class="form-check-label" for="{{ $name }}_{{ $key }}">
                    {{ $optionLabel }}
                </label>
            </div>
        @endforeach
    </div>
    
    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>