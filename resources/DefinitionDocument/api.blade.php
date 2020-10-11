- controller_name: '{{ Str::studly($attribute->sheetName()) }}'
  route_prefix: '{{ Str::snake($attribute->spreadsheetCategoryName()) }}'
  http_method: '{{ Str::studly($attribute->attributes()['HttpMethod']) }}'
  name: '{{ Str::studly($attribute->mainKeyName()) }}'
  description: '{{ $attribute->attributes()['HttpDescription'] }}'
  request:
    columns:
@foreach($attribute->requestAttributes() as $request_attribute)
      - name: '{{ $request_attribute->attributes()['ColumnName'] }}'
        description: '{{ $request_attribute->attributes()['ColumnDescription'] }}'
        data_type: '{{ $request_attribute->attributes()['DataType'] }}'
        default_value: '{{ $request_attribute->attributes()['DefaultValue'] }}'
        rules: '{{ $request_attribute->attributes()['RequestRule'] }}'
        messages: {!! $request_attribute->ruleMessage() !!}
@endforeach
  response:
    columns:
@foreach($attribute->responseAttributes() as $response_attribute)
      - name: '{{ $response_attribute->attributes()['ColumnName'] }}'
        description: '{{ $response_attribute->attributes()['ColumnDescription'] }}'
        data_type: '{{ $response_attribute->attributes()['DataType'] }}'
@endforeach
