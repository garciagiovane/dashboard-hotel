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

    public function liberarQuarto() {
        $this->status = 'ATIVO';
        $this->data_prevista_checkin = null;
        $this->reservation_id = null;
        $this->save();
    }

    public function limparQuarto()
    {
        $this->status = "AGUARDANDO_LIMPEZA";
        $this->data_prevista_checkin = null;
        $this->reservation_id = null;
        $this->save();
    }
}
