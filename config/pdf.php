<?php

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4',
	'author'                => '',
	'subject'               => '',
	'keywords'              => '',
	'creator'               => 'HamyaranShomalShargh',
	'display_mode'          => 'fullpage',
	'tempDir'               => public_path('/temp/'),
	'pdf_a'                 => false,
	'pdf_a_auto'            => false,
	'icc_profile_path'      => '',
    'font_path' =>base_path('/storage/fonts/'),
    'font_data' => [
        'mitra' => [
            'R'  => 'mitra.ttf',
            'useOTL' => 0xFF,
            'useKashida' => 75,
        ],
        'iransans' => [
            'R'  => 'iransans.ttf',
            'useOTL' => 0xFF,
            'useKashida' => 75,
        ],
        'iranyekan' => [
            'R'  => 'iranyekan.ttf',
            'useOTL' => 0xFF,
            'useKashida' => 75,
        ],
        'nastaliq' => [
            'R'  => 'nastaliq.ttf',
            'useOTL' => 0xFF,
            'useKashida' => 75,
        ],
        'nazanin' => [
            'R'  => 'nazanin.ttf',
            'useOTL' => 0xFF,
            'useKashida' => 75,
        ],
        'nazanin_bold' => [
            'R'  => 'nazanin_bold.ttf',
            'useOTL' => 0xFF,
            'useKashida' => 75,
        ],
        'titr' => [
            'R'  => 'titr.ttf',
            'useOTL' => 0xFF,
            'useKashida' => 75,
        ],
    ],
];
