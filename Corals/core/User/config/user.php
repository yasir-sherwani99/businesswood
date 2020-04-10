<?php

return [
    'models' => [
        'user' => [
            'presenter' => \Corals\User\Transformers\UserPresenter::class,
            'resource_url' => 'users',
            'default_picture' => 'assets/corals/images/avatars/',
            'translatable' => ['name'],
            'csv_config' => [
                'unique_columns' => ['email'],
                'validation_rules' => [
                    'name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|email',
                    'address' => 'required',
                    'job_title' => 'required',
                    'phone_country_code' => 'required',
                    'phone_number' => 'required',
                ],
                'csv_files' => [
                    'valid_entities' => 'import/valid_entities.csv',
                    'invalid_entities' => 'import/invalid_entities.csv',
                ]
            ],
        ],
        'role' => [
            'presenter' => \Corals\User\Transformers\RolePresenter::class,
            'resource_url' => 'roles'
        ],
    ]
];