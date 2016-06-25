<?php namespace Passport\Models;

class OrdUser extends BaseModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ord_user';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'uid' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int',
        'decorate_progress' => 'int',
        'verify_status' => 'int',
        'decorate_area' => 'int'
    ];

    public static $rules = [
        'uid' => 'int',
        'area_id' => 'int',
        'decorate_style' => 'string',
        'decorate_type' => 'string',
        'insert_time' => 'int',
        'modify_time' => 'int',
        'decorate_progress' => 'int',
        'verify_status' => 'int',
        'decorate_area' => 'int',
        'dec_fund' => 'int',
        'districts' => 'string',
    ];
}
