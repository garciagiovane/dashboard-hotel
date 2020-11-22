<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quarto extends Model
{
    use HasFactory;

    public function servicos()
    {
        return $this->belongsToMany('App\Models\Servico', 'servicos_quartos', 'quartos_id', 'servicos_id');
    }

    public static function liberarQuarto($quarto)
    {
        $quarto->status = ATIVO;
        unset($quarto->data_prevista_checkin);
        unset($quarto->reservation_id);
        $quarto->save();
    }
}
