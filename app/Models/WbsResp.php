<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WbsResp extends Model
{
    protected $table = 'wbs_respond';
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

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function orderItem()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function totalWeight()
    {
        $total = 0;
        foreach ($this->orderItem as $value) {
            $total += $value->weight();
        }
        return $total;
    }

    public function courier()
    {
        return $this->hasOne(Courier::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_id', 'id');
    }

    public function totalOrder()
    {
        $total = 0;
        if ($this->orderItem != null) {
            foreach ($this->orderItem as $i) {
                $total += $i->total();
            }
        }
        return $total;
    }

    public function truePercent()
    {
        if ($this->coupon_id == null) {
            return 0;
        }
        if ($this->totalOrder() * $this->coupon->amount / 100 < $this->coupon->max) {
            return $this->coupon->ammount;
        } elseif ($this->totalOrder() * $this->coupon->amount / 100 > $this->coupon->max) {
            return $this->coupon->max * 100 / $this->totalOrder();
        }
    }

    public function quantity()
    {
        $total = 0;
        foreach ($this->orderItem as $i) {
            $total += $i->quantity;
        }
        return $total;
    }

    // public function afterCoupon()
    // {
    //     if (is_null($this->coupon)) {
    //         return null;
    //     } else {
    //         $piece = $this->totalOrder() * ($this->coupon->amount / 100);
    //         $max = $this->coupon->max;
    //         if ($this->totalOrder() != 0) {
    //             return $piece >= $max ? $this->totalOrder() - $max : $this->totalOrder() - $piece;
    //         }
    //         return 0;
    //     }
    // }

    public function afterCoupon()
    {
        if (is_null($this->coupon)) {
            return null;
        } else {
            $total = 0;
            if ($this->orderItem != null) {
                foreach ($this->orderItem as $i) {
                    $total += $i->totalAfterCoupon();
                }
            }
            return $total;
        }
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
