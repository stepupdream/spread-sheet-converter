- names:
@foreach($parentAttribute->getAttributesGroupByKeyName('*') as $attribute)
    - id: '{{ $attribute->getAttributeDetailByKey('id') }}'
      name: '{{ $attribute->getAttributeDetailByKey('name') }}'
      name_detail: '{{ $attribute->getAttributeDetailByKey('name_detail') }}'
@endforeach
