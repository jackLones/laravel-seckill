<?php

namespace App\Models;

use App\Exceptions\InternalException;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class OrderRecord extends Authenticatable
{
    use HasFactory;
    /**
     * 与模型关联的数据表.
     *
     * @var string
     */
    protected $table = 't_order_record';
//    public $timestamps = false;

    protected $fillable = ['order_id', 'product_id', 'user_id', 'buy_num', 'activity_id','amount','status' ];

    /**
     * 不会被包含在模型的结果集中
     * @var string[]
     */
    protected $hidden = ['id', 'created_at', 'updated_at', 'is_enable'];
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

}
