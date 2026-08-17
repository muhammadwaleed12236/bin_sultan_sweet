<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'size_value'      => 'float',
        'price'           => 'float',
        'wholesale_price' => 'float',
        'cost_price'      => 'float',
        'stock_qty'       => 'float',
        'is_default'      => 'boolean',
        'is_active'       => 'boolean',
    ];

    protected $hidden = [
        'product',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get size in KG equivalent, with intelligent fallback if size_value is 0 or empty
     */
    public function getKgSizeAttribute()
    {
        $val = floatval($this->size_value);
        if ($val > 0) {
            $unit = strtolower($this->size_unit ?? '');
            if ($unit === 'kg') {
                return $val;
            } elseif (in_array($unit, ['g', 'gm', 'gram', 'grams'])) {
                return $val / 1000;
            } elseif ($unit === 'pound') {
                return $val * 0.453592;
            }
            return $val;
        }

        // Automatic fallback based on name or label
        $str = strtolower(($this->variant_name ?? '') . ' ' . ($this->size_label ?? ''));
        if (preg_match('/(\d+(?:\.\d+)?)\s*(?:kg|kilo)/i', $str, $m)) {
            return floatval($m[1]);
        }
        if (preg_match('/(\d+(?:\.\d+)?)\s*(?:g|gm|gram)/i', $str, $m)) {
            return floatval($m[1]) / 1000;
        }
        if (preg_match('/half\s*kg/i', $str)) {
            return 0.5;
        }
        if (preg_match('/pao|pow/i', $str)) {
            return 0.25;
        }
        if (preg_match('/(\d+(?:\.\d+)?)\s*(?:pound|lb)/i', $str, $m)) {
            return floatval($m[1]) * 0.453592;
        }

        return 1.0; // Default fallback to 1 KG
    }

    /**
     * Convert kg to grams automatically
     * If size_unit is "kg", return grams equivalent
     */
    public function getGramsAttribute()
    {
        return $this->kg_size * 1000;
    }

    /**
     * Get display label automatically
     * e.g. "1 Pound - Rs 1000" or "500g - Rs 800"
     */
    public function getDisplayLabelAttribute()
    {
        $label = $this->size_label ?? $this->variant_name;
        return $label . ' - Rs ' . number_format($this->price);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class, 'variant_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'variant_id');
    }

    public function getStockQtyAttribute($value)
    {
        if ($this->relationLoaded('stocks')) {
            $qty = $this->stocks->sum('qty');
            if ($this->relationLoaded('product')) {
                if ($this->is_default || ($this->product->variants->first() && $this->product->variants->first()->id == $this->id)) {
                    if ($this->product->relationLoaded('stocks')) {
                        $qty += $this->product->stocks->where('variant_id', null)->sum('qty');
                    }
                }
            }
            return $qty;
        }
        return $value;
    }
}

