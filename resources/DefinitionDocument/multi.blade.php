- controller_name: '{{ Str::studly($parentAttribute->sheetName()) }}'
  route_prefix: '{{ Str::snake($parentAttribute->spreadsheetCategoryName()) }}'
  http_method: '{{ Str::studly($parentAttribute->parentAttributeDetails()['HttpMethod']) }}'
  name: '{{ Str::studly($parentAttribute->parentAttributeDetails()['ApiName']) }}'
  description: '{{ $parentAttribute->parentAttributeDetails()['HttpDescription'] }}'
  request:
    columns:
@foreach($parentAttribute->getAttributesGroupByKeyName('Request') as $request_attribute)
      - name: '{{ $request_attribute->attributeDetails()['ColumnName'] }}'
        description: '{{ $request_attribute->attributeDetails()['ColumnDescription'] }}'
        data_type: '{{ $request_attribute->attributeDetails()['DataType'] }}'
        default_value: '{{ $request_attribute->attributeDetails()['DefaultValue'] }}'
        rules: '{{ $request_attribute->attributeDetails()['RequestRule'] }}'
        messages: {!! $request_attribute->ruleMessage() !!}
@endforeach
  response:
    columns:
@foreach($parentAttribute->getAttributesGroupByKeyName('Response') as $response_attribute)
      - name: '{{ $response_attribute->attributeDetails()['ColumnName'] }}'
        description: '{{ $response_attribute->attributeDetails()['ColumnDescription'] }}'
        data_type: '{{ $response_attribute->attributeDetails()['DataType'] }}'
@endforeach
