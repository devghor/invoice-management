<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{

    protected $fillable = [
        'invoice_id',
        'rq_sl',
        'product_id',
        'qty',
        'price_rate',
        'total_price',
        'brand'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
