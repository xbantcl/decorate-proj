<?php namespace Decorate\Models;

class Shop extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shop';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'uid' => 'int',
        'works_id' => 'int',
        'goods_id' => 'int',
        'area_id' => 'int',
        'status' => 'int',
        'longitude' => 'float',
        'latitude' => 'float',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];

    public static $rules = [
        'id' => 'int',
        'uid' => 'int',
        'works_id' => 'int',
        'goods_id' => 'int',
        'avatar' => 'string',
        'name' => 'string',
        'intr' => '',
        'area_id' => 'int',
        'status' => 'int',
        'longitude' => 'float',
        'latitude' => 'float',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];
}
