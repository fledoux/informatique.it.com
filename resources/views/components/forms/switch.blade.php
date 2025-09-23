@props([
    'name' => '',
    'id' => $name,
    'label' => '',
    'checked' => false,
    'valueOn' => '1',
    'valueOff' => '0',
    'required' => false,
    'disabled' => false,
    'help' => null,
])

<div class="mb-3">
    {{-- Champ hidden pour envoyer valueOff quand le switch n'est pas coché --}}
    <input type="hidden" name="{{ $name }}" value="{{ $valueOff }}">
    
    <div class="form-check form-switch">
        <input 
            class="form-check-input @error($name) is-invalid @enderror" 
            type="checkbox" 
            role="switch" 
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ $valueOn }}"
            @if($checked || old($name) == $valueOn || old($name) === true || old($name) === 1) checked @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes }}
        >
        <label class="form-check-label" for="{{ $id }}">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
        
        @if($help)
            <div class="form-text">{{ $help }}</div>
        @endif
        
        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>