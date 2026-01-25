<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Categorie extends Model
{
     use HasFactory;
        protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Une catégorie possède plusieurs produits
     */

     // Générer le slug automatiquement avant la création
    protected static function booted()
    {
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
