<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;


class ReserveUser extends Authenticatable
{
    use HasFactory;


    /**
     * 与模型关联的数据表.
     *
     * @var string
     */
    protected $table = 't_reserve_user';
//    public $timestamps = false;

    protected $fillable = [
        'reserve_info_id',
        'sku_id',
        'user_id',
        'reserve_time',
        'yn',
    ];

    public function reserveInfo()
    {
        return $this->belongsTo(ReserveInfo::class, 'reserve_info_id');
    }

}
