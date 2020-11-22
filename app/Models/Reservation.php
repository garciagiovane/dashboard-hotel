<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    public function servicos() {
        return $this->belongsToMany('App\Models\Servico', 'reservas_quartos', 'reservation_id', 'servico_id');
    }

    public static function cancelarReserva($codigoReserva) {
        $reserva = Reservation::find($codigoReserva);
        $reserva->status = 'CANCELADA';
        $reserva->save();
    }   
}
