<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    use HasFactory;
    protected $table = 'pokemon';

    //Relación muchos es a uno
    public function region(){
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function tipoPrimario(){
        return $this->belongsTo(TipoPokemon::class, 'tipo_uno_id');
    }

    public function tipoSecundario(){
        return $this->belongsTo(TipoPokemon::class, 'tipo_dos_id');
    }
}
