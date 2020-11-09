<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model{

    protected $primaryKey = 'id';

    public function sector()
    {
        return $this->belongsTo('App\Models\Sector');
    }

    public function employee()
    {
        return $this->belongsTo('App\Models\Empleado');
    }

    public function estado()
    {
        return $this->belongsTo('App\Models\EstadoPedido');
    }
}