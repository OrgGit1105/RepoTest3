<?php

define('DEFAULT_ZERO_PAD',  5);
define('DEFAULT_STR_ZERO',  '0');

define('CODE_SUCCESS', 200);
define('CODE_CREATE_FAILED', 201);
define('CODE_DELETE_FAILED', 202);
define('CODE_MULTI_STATUS', 207);
define('CODE_NO_ACCESS', 403);
define('CODE_NOT_FOUND', 404);
define('CODE_ERROR_SERVER', 500);
define('CODE_UNAUTHORIZED', 401);

define('IMAGE', 'upload/image');

const POLICY_TYPE = [
    "V_FACE" => 1,
    "AWS" => 2,
    "EC2_admin" => 3,
    "EC2_deploy" => 4,
    "Git" => 5
];

const POLICY_V_FACE_NAME = [
    "Admin" => "Admin",
    "Normal" => "Normal"
];

const POLICY_V_FACE_ID = [
    "Admin" => 1,
    "Normal" => 2
];

const TYPE_DATE = [
    1 => 'Work',
    2 => 'Remote',
    3 => 'Take off',
    4 => 'Special day off'
];
