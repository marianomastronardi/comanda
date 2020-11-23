<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Mesa extends Model{

    public $timestamps = false;

    public function estadoMesa()
    {
        return $this->hasOne('App\Models\EstadoMesa');
    }
}