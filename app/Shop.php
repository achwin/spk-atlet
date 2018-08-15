<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
	protected $table = 'shops';    
	protected $primaryKey = 'id';   
   	public $timestamps = true;
	protected $fillable = [
		'nama',
		'distrik',
		'lamaCust',
		'tipeToko',
		'avgSales',
		'pembayaran',
		'npwporktp',
		'aksesKirim',
		'kategori',
		'produkMasuk',
		'potensi'
	]; 	
}