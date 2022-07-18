- name: '{{ $parentAttribute->parentAttributeDetails()['TableName'] }}'
  database_directory_name: '{{ $parentAttribute->spreadsheetCategoryName() }}'
  connection_name: '{{ $parentAttribute->parentAttributeDetails()['ConnectionName'] }}'
  domain_group: '{{ $parentAttribute->sheetName() }}'
  description: '{{ $parentAttribute->parentAttributeDetails()['TableDescription'] }}'
  columns:
@foreach($parentAttribute->getAttributesGroupByKeyName('*') as $attribute)
    - name: '{{ $attribute->attributeDetails()['ColumnName'] }}'
      description: '{{ $attribute->attributeDetails()['ColumnDescription'] }}'
      data_type: '{{ $attribute->attributeDetails()['DataType'] }}'
      migration_data_type: '{{ $attribute->attributeDetails()['MigrationDataType'] }}'
      is_real_column: {{ $attribute->attributeDetails()['IsRealColumn'] }}
      is_unsigned: {{ $attribute->attributeDetails()['IsUnsigned'] }}
      is_nullable: {{ $attribute->attributeDetails()['IsNullable'] }}
@endforeach
