@if ($reports)
    @foreach ($reports->selected_columns as $columnName => $column)
        <tr data-id="{{ $columnName }}">
            <td>{{ $column['display_name'] }}</td>
            <td>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="{{ $column['table_column_name'] }}" value="1"
                        {{ $column['default_visible'] === 'Yes' ? 'checked readonly' : ($column['selected_column'] === 'Yes' ? 'checked' : '') }}>
                </div>
            </td>
        </tr>
    @endforeach
@else
    @foreach (config('constants.INSURANCE_DETAIL') as $columnName => $column)
        <tr data-id="{{ $columnName }}">
            <td>{{ $column['display_name'] }} </td>
            <td>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="{{ $column['table_column_name'] }}"
                        value="1"
                        {{ $column['default_visible'] === 'Yes' ? 'checked readonly' : ($column['selected_column'] === 'Yes' ? 'checked' : '') }}>
                </div>
            </td>
        </tr>
    @endforeach
@endif
