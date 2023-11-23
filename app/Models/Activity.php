<?php

namespace App\Models;

use App\Exceptions\InternalException;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Activity extends Authenticatable
{
    use HasFactory;
    /**
     * 与模型关联的数据表.
     *
     * @var string
     */
    protected $table = 't_activity';
//    public $timestamps = false;

    protected $fillable = ['activity_name', 'product_id', 'activity_start_date', 'activity_end_date', 'limit_num', 'stock_num', 'status', 'activity_picture_url', 'activity_price'];

    /**
     * 不会被包含在模型的结果集中
     * @var string[]
     */
    protected $hidden = ['id', 'created_at', 'updated_at', 'is_enable'];
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function updateStockNum($amount)
    {
        if ($amount < 0) {
            throw new InternalException('更新库存不可小于0');
        }

        return $this->where('id', $this->id)
            ->where('stock_num', '>=', $amount)
            ->decrement('stock_num', $amount);
    }

}
