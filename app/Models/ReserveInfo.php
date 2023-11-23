<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class ReserveInfo extends Authenticatable
{
    use HasFactory;
    /**
     * 与模型关联的数据表.
     *
     * @var string
     */
    protected $table = 't_reserve_info';
//    public $timestamps = false;

    protected $fillable = ['sku_id', 'reserve_start_time', 'reserve_end_time', 'seckill_start_time', 'seckill_end_time'];

    /**
     * 不会被包含在模型的结果集中
     * @var string[]
     */
    protected $hidden = ['id', 'creator', 'created_at', 'updated_at', 'yn'];
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

}
