<?php
return
  [
    'range'=> [
      'A' => 5,
      'B' => 4,
      'C' => 3,
      'D' => 2,
      'E' => 1
    ],
    'github' =>[
        'github_token' => env('GITHUB_TOKEN'),
        'veho_develop' => [
            'api_issues' => env('GITHUB_API_ISSUES', 'https://api.github.com/repos/TeckVeho/VEHO-Develop/issues')
        ],
    ],
  ];
