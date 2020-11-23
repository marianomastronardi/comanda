<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model{

    protected $primaryKey = 'id';


    public function mesa()
    {
        return $this->belongsTo('App\Models\Mesa');
    }

    public function estado()
    {
        return $this->belongsTo('App\Models\EstadoPedido');
    }

}