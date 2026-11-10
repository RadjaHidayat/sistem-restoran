<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'id_menu';
    public $timestamps = false;

    protected $fillable = ['id_kategori', 'nama_menu', 'deskripsi', 'harga', 'foto', 'tersedia'];

    protected $casts = [
        'harga' => 'decimal:2',
        'tersedia' => 'boolean',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }
}