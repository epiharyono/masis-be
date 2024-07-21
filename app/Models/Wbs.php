<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wbs extends Model
{
    protected $table = 'wbs';
    // protected $connection = 'JTM_KEDOI';

    protected $guarded = ['id', 'created_at', 'updated_at'];


    protected $dates = ['created_at', 'updated_at', 'confirmed_at', 'arrival_date'];
    //
    public static function boot()
    {

        parent::boot();

        static::saving(function ($model) {
            $already = null;
            $already = $model->merchant_ref;
            if ($already == null) {
                try {
                    $order = $model->get()->last();
                    $last_merchant = (string)$order->merchant_ref;
                    // get 4 digits start from index 10
                    $last_value = (int)substr($last_merchant, 10, 4);
                    $last_time = (string)substr($last_merchant, 4, 6);

                    if ($last_time !== date('ymd')) {
                        $model->merchant_ref =  'INV-' . date('ymd') . '1001';
                    } else {
                        $last_value += 1;
                        $model->merchant_ref =  'INV-' . $last_time . $last_value;
                    }
                } catch (\Exception $e) {
                    $model->merchant_ref = 'INV-' . date('ymd') . '1001';
                }
            }
        });
    }

    public function respond()
    {
        return $this->hasMany(WbsResp::class, 'wbs_id', 'id')->orderBy('tanggal','desc');;
    }

    public function timeStamp()
    {
        return array(
            "created_at" => is_null($this->created_at) ? null : getTimeStampsAttribute($this->created_at),
            "updated_at" => is_null($this->updated_at) ? null : getTimeStampsAttribute($this->updated_at),
            "arrival_date" => is_null($this->arrival_date) ? null : getTimeStampsAttribute($this->arrival_date)
        );
    }
}
