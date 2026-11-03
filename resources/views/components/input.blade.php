@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null])
<div class="field @error($name) has-error @enderror">
    <label for="{{ $name }}">{{ $label }}</label>
    <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}"
           @if ($type !== 'file' && $type !== 'password') value="{{ old($name, $value) }}" @endif
           {{ $attributes }}>
    @error($name)
        <div class="error-text">{{ $message }}</div>
    @elseif ($hint)
        <div class="hint">{{ $hint }}</div>
    @enderror
</div>
