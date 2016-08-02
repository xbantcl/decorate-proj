<?php namespace Decorate\Models;

class ShopWorks extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shop_works';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'works_id' => 'int',
        'shop_id' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];

    public static $rules = [
        'id' => 'int',
        'works_id' => 'int',
        'shop_id' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];
}
