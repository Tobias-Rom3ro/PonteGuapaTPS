<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'imagen_id',
        'imagen_tag'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock' => 'integer',
    ];
    public function getImagenUrlAttribute()
    {
        return $this->imagen_id ?
            Cloudinary::getUrl($this->imagen_id) :
            null;
    }

    public function setImagen($imagen)
    {
        if ($this->imagen_id) {
            Cloudinary::destroy($this->imagen_id);
        }

        $tag = 'producto_' . $this->id . '_' . time();

        $resultado = Cloudinary::upload($imagen->getRealPath(), [
            'folder' => 'productos',
            'tags' => [$tag],
            'resource_type' => 'image'
        ]);

        $this->imagen_id = $resultado->getPublicId();
        $this->imagen_tag = $tag;
        $this->save();
    }

    public function getImagenUrl()
    {
        return $this->imagen_id
            ? 'https://res.cloudinary.com/' . env('CLOUDINARY_CLOUD_NAME') . '/image/upload/' . $this->imagen_id
            : null;
    }
    public function getSearchableAttributes(): array
    {
        return [
            'nombre' => $this->nombre,
            'precio' => $this->precio,
        ];
    }

}
