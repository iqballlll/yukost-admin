@props(['label', 'name', 'options' => [], 'selected' => '', 'placeholder' => '- Pilih -'])

<div class="col">
    <label class="form-label">{{ $label }}</label>
    <select name="{{ $name }}" id="{{ $name }}" class="form-control">
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $val => $text)
        <option value="{{ $val }}" {{ $selected==$val ? 'selected' : '' }}>{{ $text }}</option>
        @endforeach
    </select>
</div>