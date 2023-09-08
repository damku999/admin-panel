<table class="table">
    <thead>
        <tr>
            <th>Display Name</th>
            <th>Checkbox</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($INSURANCE_DETAIL as $column)
            <tr>
                <td>{{ $column['display_name'] }}</td>
                <td>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="{{ $column['table_column_name'] }}"
                            value="1" {{ $column['default_visible'] === 'Yes' ? 'checked disabled' : ($column['selected_column'] === 'Yes' ? 'checked' : '') }}>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
