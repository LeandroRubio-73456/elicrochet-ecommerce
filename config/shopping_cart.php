<?php
return [
    'format_numbers' => true,
    'decimals' => 2,
    'dec_point' => '.',
    'thousands_sep' => ',',
    
    // Use DBStorage for persistence
    'storage' => \App\Storage\DBStorage::class,
    'use_database' => true,
    
    // Config de BD
    'session_key' => 'cart_session',
    
    'events' => null,
];