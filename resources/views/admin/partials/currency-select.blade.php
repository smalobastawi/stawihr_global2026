@props([
    'name' => 'currency',
    'id' => null,
    'selected' => null,
    'required' => true,
    'class' => 'form-control',
])

<select name="{{ $name }}" @if($id) id="{{ $id }}" @endif class="{{ $class }}" @if($required) required @endif>
    @foreach (\App\Lib\Enumerations\Currency::groupedForSelect() as $groupLabel => $currencies)
        <optgroup label="{{ $groupLabel }}">
            @foreach ($currencies as $code => $label)
                <option value="{{ $code }}" @selected(old($name, $selected ?? \App\Lib\Enumerations\Currency::DEFAULT) === $code)>
                    {{ $label }}
                </option>
            @endforeach
        </optgroup>
    @endforeach
</select>
