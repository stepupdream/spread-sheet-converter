- name: '{{ $attribute->attributes()['TableName'] }}'
  database_directory_name: '{{ $attribute->spreadsheetCategoryName() }}'
  connection_name: '{{ $attribute->attributes()['ConnectionName'] }}'
  domain_group: '{{ $attribute->sheetName() }}'
  description: '{{ $attribute->attributes()['TableDescription'] }}'
  columns:
@foreach($attribute->SubAttributes() as $sub_attribute)
    - name: '{{ $sub_attribute->attributes()['ColumnName'] }}'
      description: '{{ $sub_attribute->attributes()['ColumnDescription'] }}'
      data_type: '{{ $sub_attribute->attributes()['DataType'] }}'
      is_real_column: {{ $sub_attribute->attributes()['IsRealColumn'] }}
      is_unsigned: {{ $sub_attribute->attributes()['IsUnsigned'] }}
      is_nullable: {{ $sub_attribute->attributes()['IsNullable'] }}
@endforeach
