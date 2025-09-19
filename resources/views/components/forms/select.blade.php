<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <select name="{{ $name }}" 
            id="{{ $name }}" 
            class="form-select @error($name) is-invalid @enderror"
            @if($required) required @endif>
        
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" 
                    @if(old($name, $value) == $optionValue) selected @endif>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
    
    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>