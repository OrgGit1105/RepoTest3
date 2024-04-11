<?php
return [
    'user.login.false' => 'User not found',
    'login.false' => 'ID or password is incorrect',
    'logout.success' => 'Logout success',
    'model.nonexistent' => 'Can not find id in form request',

    'image_face' => [
        'user_id_not_found' => 'user_id not found',
        'id_not_found' => 'Image face not found',
        'must_one_person' => 'Invalid image. Image must one person',
        'face_compare_not_found' => 'Face compare not found',
    ],
    'user' => [
        'login.false' => 'user login false',
        'login_not_granted' => 'user is not granted access',
        'not' => [
            'found' => 'User not found',
            'permission' => 'User does not have the right roles',
        ],
        'email' => [
            'exist' => 'User email exist'
        ],
        'name_existed' => 'User existed',
        'name_regex' => 'Username must start with a letter, can only contain letters, numbers and underscores (_), periods (.), dashes (-) and cannot contain spaces or special characters special',
        'ssh_key' => 'Please enter the ssh public key value',
        'ssh_key_and_gmail_github' => 'The two values gmail Github and Ssh public key are required'
    ],
    'token' => [
        'false' => 'Token not provided',
    ],
    'arriving_report' => [
        'time_in_more_than_time_out' => 'Time in not more than time out',
        'need_check_time_in' => 'Please check time in first',
        'must_same_date' => 'check in, check out must same date',
        'time_in_is_check' => 'Today, this employee checked in and checked out',
        'time_out_is_check' => 'Employee was check out',
        'time_must_today' => 'Please time_in must today',
        'in_time_exist' => 'This in time data already exists',
        'out_time_exist' => 'This out time data already exists',
        'go_out' => 'Start calculating break time',
        'go_into' => 'End of break time'
    ],
    'viam_user' => [
        'policy_id_v-face' => 'V-face admin and V-face normal cannot be selected at the same time',
        'policy_id_ec2' => 'On an EC2 instance, deploy policy and admin policy cannot be selected at the same time',
        'cannot_delete' => 'This user cannot be deleted because there are already employees belonging to this user',
    ],
    'policy' => [
        'name_existed' => 'Please enter another name',
        'name_regex' => 'The name must start with a letter or an underscore character (_), can only contain letters, numbers and underscores (_), cannot contain spaces or special characters',
        'instance_id' => 'The instance id field is required with types are EC2',
        'project_name' => 'The project name field is required with types are EC2 deploy',
        'project_do_not_existed' => 'The project name does not exist',
        'policy_existed' => 'On an EC2 instance, only one admin policy can be created and only one deploy policy can be created for each project',
        'instance_id_not_found' => 'Error accessing EC2 instance',
        'aws_existed' => 'This AWS already exists'
    ],
    'working_time.out_time' => 'The out time field is required when type field other value working',
    'rds_manager' => [
        'connect_failed' => 'Connection to RDS failed',
        'action_error' => 'Cannot :action because this connection already has data',
        'key_file.extension' => 'File extension is not valid'
    ],
    'viam_rds' => [
        'database_exist' => 'The user account data on this database has been created previously.',
    ]
];
