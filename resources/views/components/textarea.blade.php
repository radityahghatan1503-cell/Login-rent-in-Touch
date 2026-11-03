@props(['name', 'label', 'value' => null])
<div class="field @error($name) has-error @enderror">
    <label for="{{ $name }}">{{ $label }}</label>
    <textarea id="{{ $name }}" name="{{ $name }}" rows="3" {{ $attributes }}>{{ old($name, $value) }}</textarea>
    @error($name) <div class="error-text">{{ $message }}</div> @enderror
</div>
