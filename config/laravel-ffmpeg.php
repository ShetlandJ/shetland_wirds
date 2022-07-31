<?php

return [
    'ffmpeg' => [
        'binaries' => env('FFMPEG_BINARIES', 'usr/bin/ffmpeg'),

        'threads' => 12,   // set to false to disable the default 'threads' filter
    ],

    'ffprobe' => [
        'binaries' => env('FFPROBE_BINARIES', 'usr/bin/ffprobe'),
    ],

    'timeout' => 3600,

    'enable_logging' => true,
    'log_channel' => env('LOG_CHANNEL', 'stack'),   // set to false to completely disable logging

    'set_command_and_error_output_on_exception' => false,

    'temporary_files_root' => env('FFMPEG_TEMPORARY_FILES_ROOT', sys_get_temp_dir()),
    'temporary_files_encrypted_hls' => env('FFMPEG_TEMPORARY_ENCRYPTED_HLS', env('FFMPEG_TEMPORARY_FILES_ROOT', sys_get_temp_dir())),
];