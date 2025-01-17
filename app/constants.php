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

const ENVIRONMENT_UPDATE = 'production';
const ENVIRONMENT_UPDATE_RDS = 'production';
const RDS_LOCAL = 0;

// instance_id of servers need to connect
const INSTANCE_ID_240 = 'i-0553d99830b279164';
const INSTANCE_ID_142 = 'i-04239f041017b687f';
const INSTANCE_ID_176 = 'i-0c46b59f952de209c';
const INSTANCE_ID_235 = 'i-0345e00fe812449e5';
const INSTANCE_ID_22 = 'i-046740df69ece8b0f';
const INSTANCE_ID_223 = 'i-0ab035dce3943efab';

const TEXT_NAME_SERVER = [
    INSTANCE_ID_240 => '18.180.33.240',
    INSTANCE_ID_142 => '18.176.73.142',
    INSTANCE_ID_176 => '35.79.42.176',
    INSTANCE_ID_235 => '52.68.0.235',
    INSTANCE_ID_22 => '35.73.104.22',
    INSTANCE_ID_223 => '54.92.112.223',
];

const TYPE_RDS_PERMISSION_DATA = 1;
const TYPE_RDS_PERMISSION_STRUCTURE = 2;
const TYPE_RDS_PERMISSION_ADMINISTRATION = 3;
const TYPE_RDS_PERMISSION_ALL = 4;

const TYPE_RDS_PERMISSION = [
    TYPE_RDS_PERMISSION_DATA => 'data',
    TYPE_RDS_PERMISSION_STRUCTURE => 'structure',
    TYPE_RDS_PERMISSION_ADMINISTRATION => 'administration',
    TYPE_RDS_PERMISSION_ALL => 'all_privileges'
];

const PERMISSION_GRANT = 'GRANT';
const PERMISSION_ALL_PRIVILEGES = 'ALL PRIVILEGES';

const UPDATE_PAID_OFF_AUTO_MONTH = 1;
const UPDATE_PAID_OFF_AUTO_YEAR = 2;
const UPDATE_PAID_OFF_REGISTER_REPORT = 3;



