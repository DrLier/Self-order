<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $keyType = 'string';

    protected $fillable = [
        'invoice_number',
        'done_at',
        'paid_amount'
    ];

    public function orderMenus()
    {
        return $this->hasMany(OrderMenu::class);
    }

    public function scopeSearch($query, $value)
    {
        $query->where('invoice_number', 'like', "%{$value}%");
    }

    public function getTotalPriceAttribute()
    {
        $orderMenus = $this->orderMenus;
        $totalPrice = 0;

        foreach ($orderMenus as $orderMenu) {
            $totalPrice += $orderMenu->unit_price * $orderMenu->quantity;
        }

        return $totalPrice;
    }

    public function getDoneAtForHumanAttribute()
    {
        return $this->done_at ? Carbon::parse($this->done_at)->diffForHumans() : null;
    }

    public function getPaidAmountFormattedAttribute()
    {
        return 'Rp ' .number_format($this->paid_amount, 0, ',', '.');
    }

    public function getTotalPriceFormattedAttribute()
    {
        return 'Rp ' . number_format($this->totalPrice, 0, ',', '.');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid()->toString();
        });
    }
}