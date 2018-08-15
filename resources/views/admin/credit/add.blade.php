@extends('adminlte::layouts.app')

@section('htmlheader_title')
Olah pemberian kredit
@endsection

@section('contentheader_title')
Olah pemberian kredit
@endsection

@section('code-header')
@endsection

@section('main-content')
<link rel="stylesheet" href="{{ asset('select2.css') }}">
<style>
	.form-group label{
		text-align: left !important;
	}
	.select2-container--default .select2-selection--multiple .select2-selection__choice{
		background-color: teal;
	}
</style>

	@foreach (['danger', 'warning', 'success', 'info'] as $msg)
	@if(Session::has('alert-' . $msg))
<div class="alert alert-{{ $msg }}">
	<p class="" style="border-radius: 0">{{ Session::get('alert-' . $msg) }}</p>
</div>
	{!!Session::forget('alert-' . $msg)!!}
	@endif
	@endforeach


<div class="row">
	<div class="col-md-12">
		<div class="">

			@if (count($errors) > 0)
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
			@endif
			<br>
			<form id="inputData" method="post" action="{{url('kelola-pemberian-kredit/add')}}" enctype="multipart/form-data"  class="form-horizontal">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<div class="form-group">
					<label for="tipeToko" class="col-sm-2 control-label">Calon penerima kredit</label>
					<div class="col-md-8">
						 <select class="form-control input-lg js-example-basic-multiple" name="nama_toko[]" multiple="multiple">
						  @foreach($shops as $shop)
							<option value="{{$shop->id}}">{{$shop->nama}}</option>
						  @endforeach
						</select>
					</div>
				</div>
				<h3>Zona Toleransi</h3>
				<br>
				<div class="form-group">
					<label for="lamaCust" class="col-sm-2 control-label">Lama menjadi customer</label>
					<div class="col-md-4">
						<select class="form-control input-lg" name="zt_lamaCust[]">
							<option value="SR">0-3 bulan</option>
							<option value="R">4-6 bulan</option>
							<option value="RS">7-9 bln</option>
							<option value="S">10-12 bulan</option>
							<option value="BS">1-2 tahun</option>
							<option value="B">2-3 tahun</option>
							<option value="SB">>3 tahun</option>
						</select>
					</div>
					<div class="col-md-4">
						<select class="form-control input-lg" name="zt_lamaCust[]">
							<option value="SR">0-3 bulan</option>
							<option value="R">4-6 bulan</option>
							<option value="RS">7-9 bln</option>
							<option value="S">10-12 bulan</option>
							<option value="BS">1-2 tahun</option>
							<option value="B">2-3 tahun</option>
							<option value="SB">>3 tahun</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="tipeToko" class="col-sm-2 control-label">Tipe toko</label>
					<div class="col-md-4">
						<select class="form-control input-lg" name="zt_tipeToko[]">
							<option value="SR">Toko kelontong</option>
							<option value="R">Off-price Retailer</option>
							<option value="RS">Toko Khusus</option>
							<option value="S">Grosir</option>
							<option value="BS">Minimarket</option>
							<option value="B">Supermarket</option>
							<option value="SB">Hypermarket</option>
						</select>
					</div>
					<div class="col-md-4">
						<select class="form-control input-lg" name="zt_tipeToko[]">
							<option value="SR">Toko kelontong</option>
							<option value="R">Off-price Retailer</option>
							<option value="RS">Toko Khusus</option>
							<option value="S">Grosir</option>
							<option value="BS">Minimarket</option>
							<option value="B">Supermarket</option>
							<option value="SB">Hypermarket</option>
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label for="avgSales" class="col-sm-2 control-label">Rata-rata penjualan</label>
					<div class="col-md-4">
						<select class="form-control input-lg" name="zt_avgSales[]">
							<option value="SR">0-199.999</option>
							<option value="R">200.000-499.999</option>
							<option value="RS">500.000-999.999</option>
							<option value="S">1.000.000-2.999.999</option>
							<option value="BS">3.000.000-4.999.999</option>
							<option value="B">5.000.000-9.999.999</option>
							<option value="SB">>10.000.000</option>
						</select>
					</div>
					<div class="col-md-4">
						<select class="form-control input-lg" name="zt_avgSales[]">
							<option value="SR">0-199.999</option>
							<option value="R">200.000-499.999</option>
							<option value="RS">500.000-999.999</option>
							<option value="S">1.000.000-2.999.999</option>
							<option value="BS">3.000.000-4.999.999</option>
							<option value="B">5.000.000-9.999.999</option>
							<option value="SB">>10.000.000</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="aksesKirim" class="col-sm-2 control-label">Akses kirim</label>
					<div class="col-md-4">
						<select class="form-control input-lg" name="zt_aksesKirim[]">
							<option value="SR">Tidak bisa dilalui kendaraan</option>
							<option value="R">Roda 2</option>
							<option value="RS">Motor Roda 3</option>
							<option value="S">Mobil Box</option>
							<option value="BS">Truck Box</option>
							<option value="B">Wing Box</option>
							<option value="SB">Truck Container</option>
						</select>
					</div>
					<div class="col-md-4">
						<select class="form-control input-lg" name="zt_aksesKirim[]">
							<option value="SR">Tidak bisa dilalui kendaraan</option>
							<option value="R">Roda 2</option>
							<option value="RS">Motor Roda 3</option>
							<option value="S">Mobil Box</option>
							<option value="BS">Truck Box</option>
							<option value="B">Wing Box</option>
							<option value="SB">Truck Container</option>
						</select>
					</div>

				</div>

				<div class="form-group">
					<label for="produkMasuk" class="col-sm-2 control-label">Produk yang sudah masuk</label>
					<div class="col-md-4">
						<select class="form-control input-lg" name="zt_produkMasuk[]">
							<option value="SR">1</option>
							<option value="R">2</option>
							<option value="RS">3</option>
							<option value="S">4</option>
							<option value="BS">5</option>
							<option value="B">6</option>
							<option value="SB">7</option>
						</select>
					</div>
					<div class="col-md-4">
						<select class="form-control input-lg" name="zt_produkMasuk[]">
							<option value="SR">1</option>
							<option value="R">2</option>
							<option value="RS">3</option>
							<option value="S">4</option>
							<option value="BS">5</option>
							<option value="B">6</option>
							<option value="SB">7</option>
						</select>
					</div>
				</div>
				<br>
				<h3>Derajat Kepentingan</h3>
				<div class="form-group">
					<label for="tipeToko" class="col-sm-2 control-label">Validasi</label>
					<div class="col-md-8">
						<select class="form-control input-lg" name="dk_validasi">
							<option value="Tidak penting sekali">Tidak penting sekali</option>
							<option value="Tidak penting">Tidak penting</option>
							<option value="Sedikit tidak penting">Sedikit tidak penting</option>
							<option value="Sedang">Sedang</option>
							<option value="Sedikit penting">Sedikit penting</option>
							<option value="Penting">Penting</option>
							<option value="Penting sekali">Penting sekali</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="tipeToko" class="col-sm-2 control-label">Lama menjadi customer</label>
					<div class="col-md-8">
						<select class="form-control input-lg" name="dk_lamaCust">
							<option value="Tidak penting sekali">Tidak penting sekali</option>
							<option value="Tidak penting">Tidak penting</option>
							<option value="Sedikit tidak penting">Sedikit tidak penting</option>
							<option value="Sedang">Sedang</option>
							<option value="Sedikit penting">Sedikit penting</option>
							<option value="Penting">Penting</option>
							<option value="Penting sekali">Penting sekali</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="tipeToko" class="col-sm-2 control-label">Tipe toko</label>
					<div class="col-md-8">
						<select class="form-control input-lg" name="dk_tipeToko">
							<option value="Tidak penting sekali">Tidak penting sekali</option>
							<option value="Tidak penting">Tidak penting</option>
							<option value="Sedikit tidak penting">Sedikit tidak penting</option>
							<option value="Sedang">Sedang</option>
							<option value="Sedikit penting">Sedikit penting</option>
							<option value="Penting">Penting</option>
							<option value="Penting sekali">Penting sekali</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="tipeToko" class="col-sm-2 control-label">Rata-rata penjualan</label>
					<div class="col-md-8">
						<select class="form-control input-lg" name="dk_avgSales">
							<option value="Tidak penting sekali">Tidak penting sekali</option>
							<option value="Tidak penting">Tidak penting</option>
							<option value="Sedikit tidak penting">Sedikit tidak penting</option>
							<option value="Sedang">Sedang</option>
							<option value="Sedikit penting">Sedikit penting</option>
							<option value="Penting">Penting</option>
							<option value="Penting sekali">Penting sekali</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="tipeToko" class="col-sm-2 control-label">Akses kirim</label>
					<div class="col-md-8">
						<select class="form-control input-lg" name="dk_aksesKirim">
							<option value="Tidak penting sekali">Tidak penting sekali</option>
							<option value="Tidak penting">Tidak penting</option>
							<option value="Sedikit tidak penting">Sedikit tidak penting</option>
							<option value="Sedang">Sedang</option>
							<option value="Sedikit penting">Sedikit penting</option>
							<option value="Penting">Penting</option>
							<option value="Penting sekali">Penting sekali</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="tipeToko" class="col-sm-2 control-label">Produk yang sudah masuk</label>
					<div class="col-md-8">
						<select class="form-control input-lg" name="dk_produkMasuk">
							<option value="Tidak penting sekali">Tidak penting sekali</option>
							<option value="Tidak penting">Tidak penting</option>
							<option value="Sedikit tidak penting">Sedikit tidak penting</option>
							<option value="Sedang">Sedang</option>
							<option value="Sedikit penting">Sedikit penting</option>
							<option value="Penting">Penting</option>
							<option value="Penting sekali">Penting sekali</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="toko_diambil" class="col-sm-2 control-label">Toko yang diambil</label>
					<div class="col-md-8">
						<input type="text" class="form-control input-lg" id="toko_diambil" name="toko_diambil" placeholder="Contoh: 2" onkeypress="var key = event.keyCode || event.charCode; return ((key  >= 48 && key  <= 57) || key == 8);"; required>
					</div>
				</div>
				<div class="form-group text-center">
					<div class="col-md-8 col-md-offset-2">
					<button type="submit" class="btn btn-primary btn-lg">
							Confirm
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@section('code-footer')
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.js"></script> 
<script type="text/javascript" src="{{ asset('select2.js') }}"></script>
 <script>
    $(document).ready(function() {
    $('.js-example-basic-multiple').select2();
});
 </script>
@endsection