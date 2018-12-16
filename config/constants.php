<?php

return [

    // Project related constants
    'global' => [
        'site' => [
            'name' => 'Broadway',
            'version' => '1.0', // For internal code comparison (if any)
        ],
    ],
    // Directory Constants
    'front' => [
        'dir' => [
            'profilePicPath'            => 'public/images/',
            'profilePicUploadPath'      => 'images/',
            'foodUploadPath'            => 'articles/',
            'familiesImagePath'         => 'public/images/families/',
            'postsImagePath'            => 'public/images/articles/',
            'categoryImagePath'         => 'public/images/categories/',
            'showsImagePath'            => 'public/images/shows/',
            'articlesImagePath'         => 'public/images/articles/',
            'badgesIconPath'            => 'public/images/badges/',
        ],

        'default' => [
            'profilePic'        =>  'default.jpg',
            'postPic'           =>  'default.jpg',
        ],
        
        'verificationCodeLength' => 4,
        'showListGrossAmount' => 5,
        'businessGrossDayIsMonday' => false,    // true - Monday, false - Sunday
        'businessGrossTakeFirstDay' => false,
    ],
    'back' => [
        'articleTitleMaxLength' => 75,
        'minUserAge' => 13,
    ],
];