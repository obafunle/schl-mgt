<?php

return [
    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ],
    'column_names' => [
        'model_morph_key' => 'model_id',
        'team_foreign_key' => 'team_id',
    ],
    'teams' => false,
    'display_permission_in_exception' => false,
    'cache' => [
        'expiration_time' => 60 * 24 * 30,
        'key' => 'spatie.permission.cache',
        'store' => 'default',
    ],
];
