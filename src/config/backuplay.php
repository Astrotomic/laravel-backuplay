<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Strict Mode
    |--------------------------------------------------------------------------
    |
    | If the strict mode is enabled we will throw an exception
    | instead of just outputting an error on the command-line.
    |
    */
    'strict' => false,

    /*
    |--------------------------------------------------------------------------
    | Folder List
    |--------------------------------------------------------------------------
    |
    | The list of folders that should be backuped.
    |
    */
    'folders' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | File List
    |--------------------------------------------------------------------------
    |
    | The list of files that should be backuped.
    |
    */
    'files' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Disk name
    |--------------------------------------------------------------------------
    |
    | The name of the configured disk to store the backups.
    | Use `false` to disable storing backups.
    |
    */
    'disk' => 'local',
];
