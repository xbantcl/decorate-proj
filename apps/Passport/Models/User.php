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
        'isPush' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int',
        'reg_platform' => 'int',
        'deviceId' => 'int',
        'reg_ip' => 'string',
        'app_version' => 'string'
    ];
}
