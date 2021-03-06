<?php namespace Passport\Models;

class Seller extends BaseModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'seller';
    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'uid' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int',
        'verify_status' => 'int',
        'area_id' => 'int'
    ];

    public static $rules = [
        'uid' => 'int',
        'area_id' => 'int',
        'name' => 'string',
        'insert_time' => 'int',
        'modify_time' => 'int',
        'verify_status' => 'int',
    ];
}
