<?php
return [
    "useredit"=>[
        'action_name' => 'handleEditData',
        'param_list'=>[
            "name"=>"name/s",
            "contacts"=>"contacts/s",
            "jobs"=>"jobs/s",
            "mobile"=>"mobile/s",
            "tencent_code"=>"tencent_code/s",
        ],
        'model_name' => "base/AdminUser",
        'validate_name' => 'base/AdminUser.edit',
    ],
];