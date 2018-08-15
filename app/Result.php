<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Result extends Model
{
	protected $table = 'results';    
	protected $primaryKey = 'id';   
   	public $timestamps = true;
	protected $guarded = ['id'];

	public function toko()
	{
		return $this->belongsTo('App\Shop','shop_id');
	}
}