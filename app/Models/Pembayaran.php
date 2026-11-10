<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';
    public $timestamps = false;

    protected $fillable = ['id_pesanan', 'id_kasir', 'metode', 'jumlah_bayar', 'status', 'waktu_bayar'];

    protected $casts = [
        'waktu_bayar' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'id_kasir', 'id_user');
    }
}