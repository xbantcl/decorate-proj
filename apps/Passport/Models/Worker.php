<?php namespace Passport\Models;

class Worker extends BaseModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'worker';
    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'uid' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int',
        'verify_status' => 'int',
        'area_id' => 'int',
        'experience' => 'int',
        'star_level' => 'int'
    ];

    public static $rules = [
        'uid' => 'int',
        'area_id' => 'int',
        'name' => 'string',
        'work_type' => 'string',
        'experience' => 'int',
        'star_level' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int',
        'verify_status' => 'int',
    ];
}
