- controller_name: '{{ Str::studly($parentAttribute->sheetName()) }}'
  route_prefix: '{{ Str::snake($parentAttribute->spreadsheetCategoryName()) }}'
  http_method: '{{ Str::studly($parentAttribute->getParentAttributeDetailByKey('HttpMethod')) }}'
  name: '{{ Str::studly($parentAttribute->getParentAttributeDetailByKey('ApiName')) }}'
  description: '{{ $parentAttribute->getParentAttributeDetailByKey('HttpDescription') }}'
  request:
    columns:
@foreach($parentAttribute->getAttributesGroupByKeyName('Request') as $requestAttribute)
      - name: '{{ $requestAttribute->getAttributeDetailByKey('ColumnName') }}'
        description: '{{ $requestAttribute->getAttributeDetailByKey('ColumnDescription') }}'
        data_type: '{{ $requestAttribute->getAttributeDetailByKey('DataType') }}'
        default_value: '{{ $requestAttribute->getAttributeDetailByKey('DefaultValue') }}'
        rules: '{{ $requestAttribute->getAttributeDetailByKey('RequestRule') }}'
        messages: {!! $requestAttribute->ruleMessage() !!}
@endforeach
  response:
    columns:
@foreach($parentAttribute->getAttributesGroupByKeyName('Response') as $responseAttribute)
      - name: '{{ $responseAttribute->getAttributeDetailByKey('ColumnName') }}'
        description: '{{ $responseAttribute->getAttributeDetailByKey('ColumnDescription') }}'
        data_type: '{{ $responseAttribute->getAttributeDetailByKey('DataType') }}'
@endforeach
