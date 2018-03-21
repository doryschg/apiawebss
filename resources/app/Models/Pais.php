<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
        
protected $table="_pais";

protected $primaryKey='nac_id';

protected $fillable = array('nac_nombre','nac_capital','nac_continenete');
	
protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

protected $dates=['deleted_at'];

}
