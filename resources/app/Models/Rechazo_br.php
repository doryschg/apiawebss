<?php


namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Rechazo_br extends Model
{
    
    use SoftDeletes;
    protected $table='rechazo_br';

	protected $primaryKey = 'rb_id';

	protected $fillable = array('rb_motivo');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates=['deleted_at'];

	 protected $softDelete = true;


	public function boleta_referencia()
	{
		
		return $this->belongsTo('awebss\Models\Boleta_referencia','br_id');
	}
}
