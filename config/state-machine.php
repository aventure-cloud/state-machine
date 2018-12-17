<?php

return [

    /*
    |--------------------------------------------------------------------------
    | State graphs
    |--------------------------------------------------------------------------
    |
    | List here all graphs that you want use in your application.
    | In the example below you can see the main structure of a graph,
    | for more information read the doc.
    |
    */

    // This is just an example
    'order' => [
        // Property path (based on Symfony property accessor) in the object actually holding the state
        'property_path' => 'status',

        // Available states
        'states' => [
            'pending',
            'accepted',
        ],

        // Transitions rules
        'transitions' => [
            'accept' => [
                'from' => 'pending', // also supported array format for multiple from ['from-1', 'from-2', ...]
                'to'   => 'accepted'
            ],
            'rollback' => [
                'from' => 'accepted',
                'to' => 'pending'
            ]
        ]
    ]

];