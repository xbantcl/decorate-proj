<?php namespace Decorate\Models;

class Area extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'area';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int'
    ];

}
