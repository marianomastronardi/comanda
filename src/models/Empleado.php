<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model{

    protected $primaryKey = 'id';

    public function sector()
    {
        return $this->belongsTo('App\Models\Sector');
    }

    public function puesto()
    {
        return $this->belongsTo('App\Models\Puesto');
    }

    public function estado()
    {
        return $this->belongsTo('App\Models\EstadoEmpleado');
    }
}