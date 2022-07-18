- names:
@foreach($parentAttribute->getAttributesGroupByKeyName('*') as $attribute)
    - name: '{{ $attribute->attributeDetails()['ColumnName'] }}'
      description: '{{ $attribute->attributeDetails()['TableName'] }}'
      description: '{{ $attribute->attributeDetails()['TableDescription'] }}'
      description: '{{ $attribute->attributeDetails()['ColumnDescription'] }}'
      data_type: '{{ $attribute->attributeDetails()['DataType'] }}'
@endforeach
