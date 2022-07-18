<?php

return [
    'credentials_path'         => storage_path('app/json/credentials.json'),
    'request_rule_column_name' => 'RequestRule',
    'request_rule_sheet_name'  => 'RequestRule',
    'read_spread_sheets'       => [
        [
            'sheet_id'                    => '***************************',
            'category_name'               => 'MasterData',
            'read_type'                   => 'SingleGroup',
            'use_blade'                   => 'single',
            'output_directory_path'       => base_path('definition_document/database/master_data'),
            'separation_key'              => 'ColumnName',
            'attribute_group_column_name' => null,
        ],
        [
            'sheet_id'                    => '***************************',
            'category_name'               => 'Api',
            'read_type'                   => 'MultiGroup',
            'use_blade'                   => 'multi',
            'output_directory_path'       => base_path('definition_document/http/api'),
            'separation_key'              => 'ColumnType',
            'attribute_group_column_name' => 'ColumnType',
        ],
        [
            'sheet_id'                    => '***************************',
            'category_name'               => 'OtherData',
            'read_type'                   => 'Other',
            'use_blade'                   => 'other',
            'output_directory_path'       => base_path('definition_document/database/other'),
            'separation_key'              => '',
            'attribute_group_column_name' => null,
        ],
    ],
];
