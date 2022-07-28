# Spreadsheet Converter
[![Laravel 9](https://img.shields.io/badge/Laravel-9-orange.svg)](http://laravel.com)
[![testing](https://github.com/stepupdream/spread-sheet-converter/actions/workflows/testing.yml/badge.svg?branch=develop)](https://github.com/stepupdream/spread-sheet-converter/actions/workflows/testing.yml)

## Introduction

You can read the information in Google Sheets and convert it to a Yaml file.  
It's hard to handwrite Yaml files, so it's perfect for people who want to manage with Google Spreadsheet.  
You can freely change the character string and count of the header line of the Google Spreadsheet to be read.  
The contents of the exported yaml file can be freely formatted with the Laravel blade file.  

## Features

- Supports 3 types of pattern input methods
- Execution time is short because the number of accesses to Google Spreadsheet is minimal
- You can freely perform the output location and processing after output
- You can also perform input validation of Google Spreadsheet by replacing the class using the service provider
  
## Requirements 
The requirements to Spreadsheet converter application is:
- PHP - Supported Versions: >= 8.0
- Laravel - Supported Versions: >= 9.0

## Installation 
Require this package with composer using the following command:

```bash 
composer require --dev stepupdream/spread-sheet-converter
```

## Arrangement
1. Please log in to your Google account. Then, while logged in, access the [Google API Console](https://console.cloud.google.com)
2. Create a new project. Feel free to decide the project name
3. Click APIs and Services in the menu on the left side of the page. Enable APIs and services
4. Search for the Google Sheets API and enable its use. Then click "Create Credentials" on the right side of the page
   - API to use : Google Sheets API
   - Where to call the API : Web server
   - Types of data to be accessed : Application data
   - Are you planning to use this API on App Engine or Compute Engine? : No
5. Enter the content of "Add Credentials to Project"
   - Service account name: Owner
   - Key type : JSON
6. The JSON file required for authentication will be downloaded
7. Rename it credentials.json and put it in the "storage/app/json" folder
8. Create a Google Spreadsheet
9. Add shared members to the created Google Spreadsheet
   - json file Add the members listed in "dev-test@****************.iam.gserviceaccount.com"
10. Check the file ID of the created Google Spreadsheet
    > https://docs.google.com/spreadsheets/d/（ID）/edit#gid=0

## Usage
1. You can publish the config file (php artisan vendor:publish) and set the default directories
2. Feel free to modify the config file

## Run Locally
Generate yaml files

```bash
php artisan spread-sheet-converter:create-definition-document
```

## Example Config
- credentials_path : Specify the location of the json file obtained in the preparation stage
- request_rule_column_name : Enter the name of the column that manages Laravel's validation rules
- request_rule_sheet_name : Enter the name of the sheet that manages Laravel's validation rules
- category_tag : Please enter the classification name of the corresponding Spreadsheet. Duplicate names are prohibited
- read_type : "SingleGroup" or "MultiGroup" or "Other"
- use_blade : Please enter the name of the template file to use
- output_directory_path : Enter the output destination of the Yaml file
- definition_directory_path : Specify the path of the directory where only the Yaml files you actually use are stored
- separation_key : Enter the column name that separates the parent group and the child group
- attribute_group_column_name : Enter the column name that separates the parent group and the child group

```php
return [
    'credentials_path'         => storage_path('app/json/credentials.json'),
    'request_rule_column_name' => 'RequestRule',
    'request_rule_sheet_name'  => 'RequestRule',
    'read_spread_sheets'       => [
        [
            'sheet_id'                    => '***************************',
            'category_tag'                => 'MasterData',
            'read_type'                   => 'SingleGroup',
            'use_blade'                   => 'single',
            'output_directory_path'       => base_path('definition_document/tmp/database/master_data'),
            'definition_directory_path'   => base_path('definition_document/database/master_data'),
            'separation_key'              => 'ColumnName',
            'attribute_group_column_name' => null,
        ],
    ],
];
```

## Sample
- [SingleGroup](https://docs.google.com/spreadsheets/d/118OSLkrru3fekGNy3Cq44UuqLU4gEzDpy_WTYfhgukU/edit#gid=0)  
![2021-12-31_22h07_44](https://user-images.githubusercontent.com/95772377/147825699-73056b80-921a-4237-8b40-34822f74db33.png)

- [MultiGroupパターン](https://docs.google.com/spreadsheets/d/1W1B5kFA698jV56hW-Mw0VzI8hJqkbDeBEsSXYAaaIi4/edit#gid=0)  
![2021-12-31_22h08_37](https://user-images.githubusercontent.com/95772377/147825723-b02ead73-468d-4ad0-8e67-fc52b689b6c7.png)

- [Otherパターン](https://docs.google.com/spreadsheets/d/1Qi7jiilObhcigjfrHtRWgfRkgQ58xjXxHNTsfCoOUy4/edit#gid=0)  
![2021-12-31_22h08_02](https://user-images.githubusercontent.com/95772377/147826406-cdec3adf-aa32-4e2f-837f-c06ace0bdd86.png)

## Contributing
Please see [CONTRIBUTING](https://github.com/stepupdream/spread-sheet-converter/blob/main/.github/CONTRIBUTING.md) for details.

## Important Point
Google Spreadsheet Settings Please do not publish the json file and Google Spreadsheet ID to the world.

## License

The Spreadsheet converter is open-sourced software licensed under the [MIT license](https://choosealicense.com/licenses/mit/)
