<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    protected $table="_zona";

    protected $primaryKey = 'zon_id';

	protected $fillable = array('zon_nombre','zon_macrodistrito','zon_distrito');

	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

}
