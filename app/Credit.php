<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credit extends Model
{
	protected $table = 'credits';    
	protected $primaryKey = 'id';   
   	public $timestamps = true;
	protected $guarded = ['id'];
}