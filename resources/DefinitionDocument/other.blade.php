- database_directory_name: '{{ $attribute->spreadsheetCategoryName() }}'
  domain_group: '{{ $attribute->sheetName() }}'
  columns:
@foreach($attribute->SubAttributes() as $sub_attribute)
    - name: '{{ $sub_attribute->attributes()['ColumnName'] }}'
      description: '{{ $sub_attribute->attributes()['ColumnDescription'] }}'
@endforeach
