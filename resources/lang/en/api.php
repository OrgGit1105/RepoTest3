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
        'name_regex' => 'Name must contain only alphanumeric characters and/or the following: +=,.@_-',
        'ssh_key' => 'Please enter the ssh public key value'
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
    ],
    'viam_user' => [
        'policy_id' => 'Policies are overlapping. Please select again',
        'cannot_delete' => 'This user cannot be deleted because there are already employees belonging to this user',
    ],
    'policy' => [
        'name_existed' => 'Policy name existed',
        'instance_id' => 'The instance id field is required with types are EC2',
        'project_name' => 'The project name field is required with types are EC2',
        'project_do_not_existed' => 'The project name does not exist',
        'policy_existed' => 'On the same EC2 instance, only one deploy policy and one admin policy can be created for each project'
    ],
    'working_time.out_time' => 'The out time field is required when type field other value working'
];
