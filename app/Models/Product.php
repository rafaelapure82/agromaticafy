<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Illuminate\Support\Facades\Mail;
use App\Mail\LowStockAlert;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'description',
        'image',
        'barcode',
        'price',
        'quantity',
        'status',
        'category_id',
        'committed_stock',
        'min_stock',
        'purchase_price',
        'supplier_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function priceTiers()
    {
        return $this->belongsToMany(PriceTier::class, 'product_price_tier')->withPivot('price');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Contain, 100, 100)
            ->nonQueued();

        $this->addMediaConversion('preview')
            ->fit(Fit::Contain, 400, 400)
            ->nonQueued();
    }

    public function getBarcodeHTML()
    {
        $generator = new BarcodeGeneratorHTML();
        return $generator->getBarcode($this->barcode, $generator::TYPE_CODE_128);
    }

    public function checkStockAlert()
    {
        if ($this->quantity <= $this->min_stock) {
            // Aquí puedes configurar el email del administrador en .env o settings
            $adminEmail = config('mail.from.address'); 
            Mail::to($adminEmail)->send(new LowStockAlert($this));
        }
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Análisis ABC de Pareto
     * Retorna A, B o C según su contribución al ingreso total
     */
    public function getAbcCategory()
    {
        $totalRevenue = OrderItem::sum(DB::raw('price * quantity'));
        if ($totalRevenue == 0) return 'C';

        $productRevenue = $this->orderItems()->sum(DB::raw('price * quantity'));
        $ratio = ($productRevenue / $totalRevenue) * 100;

        if ($ratio >= 10) return 'A'; // Contribuye mucho (simplificado)
        if ($ratio >= 3) return 'B';
        return 'C';
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
