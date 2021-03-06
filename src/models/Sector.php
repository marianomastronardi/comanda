<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model{

    protected $primaryKey = 'id';
    public $timestamps = false;

    public function employee()
    {
        return $this->belongsTo('App\Models\Empleado');
    }

}