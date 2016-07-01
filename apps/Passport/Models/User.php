<?php namespace Passport\Models;

class User extends BaseModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'user_type' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int',
        'cellphone' => 'int',
        'sex' => 'int',
        'decorate_style' => 'int',
        'decorate_type' => 'int',
        'dec_fund' => 'int',
        'decorate_progress' => 'int'
    ];

    public static $rules = [
        'user_type' => 'int',
        'account' => 'string',
        'nick_name' => 'string',
        'avatar' => 'string',
        'password' => 'string',
        'salt' => 'string',
        'cellphone' => 'string',
        'sex' => 'int',
        'invite_code' => 'string',
        'invited_code' => 'string',
        'isPush' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int',
        'reg_platform' => 'int',
        'deviceId' => 'int',
        'reg_ip' => 'string',
        'app_version' => 'string',
        'sys_v' => 'string',
        'sys_m' => 'string',
        'sys_d' => 'string',
        'sys_p' => 'string',
        'cli_v' => 'string',
        'cli_p' => 'string',
    ];

    public static $updateRules = [
        'nick_name' => 'string',
        'avatar' => 'string',
        'cellphone' => 'string',
        'sex' => 'int',
    ];
}
