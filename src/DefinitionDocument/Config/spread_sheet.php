<?php

return [
    'credentials_path'         => storage_path('app/json/credentials.json'),
    'request_rule_column_name' => 'RequestRule',
    'request_rule_sheet_name'  => 'RequestRule',
    'read_spread_sheets'       => [
        [
            'category_name'         => 'MasterData',
            'sheet_id'              => 'sheet_id',
            'read_type'             => 'Table',
            'output_directory_path' => base_path('definition_document/database/master_data'),
        ],
        [
            'category_name'         => 'Api',
            'sheet_id'              => 'sheet_id',
            'read_type'             => 'Http',
            'output_directory_path' => base_path('definition_document/api'),
        ],
    ],
];
