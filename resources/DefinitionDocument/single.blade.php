- name: '{{ $parentAttribute->getParentAttributeDetailByKey('TableName') }}'
  database_directory_name: '{{ $parentAttribute->spreadsheetTitle() }}'
  connection_name: '{{ $parentAttribute->getParentAttributeDetailByKey('ConnectionName') }}'
  domain_group: '{{ $parentAttribute->sheetName() }}'
  description: '{{ $parentAttribute->getParentAttributeDetailByKey('TableDescription') }}'
  columns:
@foreach($parentAttribute->getAttributesGroupByKeyName('*') as $attribute)
    - name: '{{ $attribute->getAttributeDetailByKey('ColumnName') }}'
      description: '{{ $attribute->getAttributeDetailByKey('ColumnDescription') }}'
      data_type: '{{ $attribute->getAttributeDetailByKey('DataType') }}'
      migration_data_type: '{{ $attribute->getAttributeDetailByKey('MigrationDataType') }}'
      is_unsigned: {{ $attribute->getAttributeDetailByKey('IsUnsigned') }}
      is_nullable: {{ $attribute->getAttributeDetailByKey('IsNullable') }}
      version: {{ $attribute->getAttributeDetailByKey('Version') }}
@endforeach
