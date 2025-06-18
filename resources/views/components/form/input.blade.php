@props(['label', 'name', 'value' => '', 'placeholder' => '', 'type' => 'text', 'width' => '220px'])

<div class="col">
    <label class="form-label">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" class="form-control" value="{{ $value }}"
        placeholder="{{ $placeholder }}" style="width: {{ $width }}" />
</div>