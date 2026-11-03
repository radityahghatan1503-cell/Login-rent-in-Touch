@props(['name', 'label', 'options', 'value' => null, 'placeholder' => null])
<div class="field @error($name) has-error @enderror">
    <label for="{{ $name }}">{{ $label }}</label>
    <select id="{{ $name }}" name="{{ $name }}" {{ $attributes }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $k => $v)
            <option value="{{ $k }}" @selected((string) old($name, $value) === (string) $k)>{{ $v }}</option>
        @endforeach
    </select>
    @error($name) <div class="error-text">{{ $message }}</div> @enderror
</div>
