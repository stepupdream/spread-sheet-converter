# spread-sheet-converter
You can read the contents of the spreadsheet and convert it to yaml

# Usage
Hit the following command. Output blade and config.
```php
php artisan vendor:publish
```

Set the sheet ID of Spreadsheet and the reading type etc in config.
Hit the following command to read the specified Spreadsheet and convert it to a Yaml file.
```php
spread_sheet_converter:create_definition_document
```

# Spreadsheet Sample
read_type:Table

![2020-04-05_22h09_20](https://user-images.githubusercontent.com/62215023/78499226-60b6b400-778a-11ea-848f-49d10775ea37.png)
 
read_type:Api

![2020-04-05_22h15_02](https://user-images.githubusercontent.com/62215023/78499320-efc3cc00-778a-11ea-8a2c-4ab0b7149bf4.png)
 
read_type:Api(request_rule_sheet_name)

![2020-04-05_22h13_05](https://user-images.githubusercontent.com/62215023/78499279-a8d5d680-778a-11ea-8091-8696b8bf572e.png)
 
