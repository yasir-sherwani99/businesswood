<?php

return [
    'models' => [
        'rating' => [
            'presenter' => \Corals\Modules\Utility\Transformers\Rating\RatingPresenter::class,
            'resource_url' => 'utilities/ratings',
        ],
        'comment' => [
            'presenter' => \Corals\Modules\Utility\Transformers\Comment\CommentPresenter::class,
            'resource_url' => 'utilities/comments',
        ],
        'wishlist' => [
            'resource_url' => 'utilities/wishlist',
        ],
        'location' => [
            'presenter' => \Corals\Modules\Utility\Transformers\Address\LocationPresenter::class,
            'resource_url' => 'utilities/address/locations',
        ],
        'tag' => [
            'presenter' => \Corals\Modules\Utility\Transformers\Tag\TagPresenter::class,
            'resource_url' => 'utilities/tags',
            'translatable' => ['name']
        ],
        'category' => [
            'presenter' => \Corals\Modules\Utility\Transformers\Category\CategoryPresenter::class,
            'resource_url' => 'utilities/categories',
            'default_image' => 'assets/corals/images/default_product_image.png',
            'translatable' => ['name']
        ],
        'attribute' => [
            'presenter' => \Corals\Modules\Utility\Transformers\Category\AttributePresenter::class,
            'resource_url' => 'utilities/attributes',
        ],
        'model_option' => [
        ],
        'invite_friends' => [
            'resource_url' => 'utilities/invite-friends',
        ],
        'seo_item' => [
            'resource_url' => 'utilities/seo-items',
        ]
    ],
    'content_consent_settings' => [
        'utility_content_consent_enabled' => [
            'label' => 'Utility::labels.content_consent.enabled',
            'type' => 'boolean',
            'validation' => '',
            'settings_type' => 'BOOLEAN',
        ],
        'utility_content_consent_popup_content' => [
            'label' => 'Utility::labels.content_consent.popup_content',
            'type' => 'textarea',
            'validation' => 'required',
            'settings_type' => 'TEXTAREA',
            'attributes' => [
                'class' => 'ckeditor-simple'
            ]
        ],
        'utility_content_consent_popup_title' => [
            'label' => 'Utility::labels.content_consent.popup_title',
            'type' => 'text',
            'validation' => 'required',
            'settings_type' => 'TEXT',
        ],
        'utility_content_consent_accept_button_text' => [
            'label' => 'Utility::labels.content_consent.accept_button_text',
            'type' => 'text',
            'validation' => 'required',
            'settings_type' => 'TEXT',
            'default' => 'I Agree',
        ],
        'utility_content_consent_reject_button_text' => [
            'label' => 'Utility::labels.content_consent.reject_button_text',
            'type' => 'text',
            'validation' => 'required',
            'settings_type' => 'TEXT',
            'default' => 'I Decline',
        ],
        'utility_content_consent_rejected_redirect_url' => [
            'label' => 'Utility::labels.content_consent.rejected_redirect_url',
            'type' => 'text',
            'validation' => 'required',
            'settings_type' => 'TEXT',
        ],
        'utility_content_consent_ask_every' => [
            'label' => 'Utility::labels.content_consent.ask_every',
            'type' => 'number',
            'default' => 30,
            'validation' => 'required',
            'settings_type' => 'NUMBER',
        ],
    ]
];
