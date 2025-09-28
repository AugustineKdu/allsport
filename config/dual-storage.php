<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 이중 저장 시스템 설정
    |--------------------------------------------------------------------------
    |
    | 데이터베이스와 JSON 파일을 동시에 사용하는 설정입니다.
    |
    */

    'json_backup_enabled' => env('JSON_BACKUP_ENABLED', true),

    'storage_path' => env('JSON_STORAGE_PATH', 'json_data'),

    'backup_frequency' => env('JSON_BACKUP_FREQUENCY', 'daily'), // daily, weekly, monthly

    'max_backup_files' => env('JSON_MAX_BACKUP_FILES', 30),

    'auto_backup_on_changes' => env('JSON_AUTO_BACKUP_ON_CHANGES', true),

    'models_to_backup' => [
        'users' => \App\Models\User::class,
        'teams' => \App\Models\Team::class,
        'matches' => \App\Models\GameMatch::class,
        'team_members' => \App\Models\TeamMember::class,
        'regions' => \App\Models\Region::class,
        'sports' => \App\Models\Sport::class,
    ],
];
