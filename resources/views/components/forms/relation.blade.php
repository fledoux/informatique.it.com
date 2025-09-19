<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    
    <select name="{{ $name }}" 
            id="{{ $name }}" 
            class="form-select @error($name) is-invalid @enderror"
            @if($required) required @endif>
        
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @php
            $modelClass = "\\App\\Models\\{$model}";
            $records = $modelClass::orderBy($displayField)->get();
        @endphp
        
        @foreach($records as $record)
            <option value="{{ $record->id }}" 
                    @if(old($name, $value) == $record->id) selected @endif>
                {{ $record->$displayField }}
            </option>
        @endforeach
    </select>
    
    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>