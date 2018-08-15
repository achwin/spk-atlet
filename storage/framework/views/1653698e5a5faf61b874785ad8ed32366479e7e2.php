<?php $__env->startSection('htmlheader_title'); ?>
Input data toko
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contentheader_title'); ?>
Input data toko
<?php $__env->stopSection(); ?>

<?php $__env->startSection('code-header'); ?>

<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.js"></script> 
<link rel="stylesheet" href="<?php echo e(asset('/css/dropzone.css')); ?>">

<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<style>
	.form-group label{
		text-align: left !important;
	}
</style>

	<?php $__currentLoopData = ['danger', 'warning', 'success', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	<?php if(Session::has('alert-' . $msg)): ?>
<div class="alert alert-<?php echo e($msg); ?>">
	<p class="" style="border-radius: 0"><?php echo e(Session::get('alert-' . $msg)); ?></p>
</div>
	<?php echo Session::forget('alert-' . $msg); ?>

	<?php endif; ?>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


<div class="row">
	<div class="col-md-12">
		<div class="">

			<?php if(count($errors) > 0): ?>
			<div class="alert alert-danger">
				<ul>
					<?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<li><?php echo e($error); ?></li>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</ul>
			</div>
			<?php endif; ?>
			<br>
			<form id="inputData" method="post" action="<?php echo e(url('shops/add-shop')); ?>" enctype="multipart/form-data"  class="form-horizontal">
				<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
				<div class="form-group">
					<label for="nama" class="col-sm-2 control-label">Nama toko</label>
					<div class="col-md-8">
						<input type="text" class="form-control input-lg" id="nama" name="nama" placeholder="Contoh: Toko Rizqy" required>
					</div>
				</div>
				<div class="form-group">
					<label for="distrik" class="col-sm-2 control-label">Distrik</label>
					<div class="col-md-8">
						<select class="form-control input-lg" name="distrik">
							<option value="Surabaya Timur">Surabaya Timur</option>
							<option value="Surabaya Utara">Surabaya Utara</option>
							<option value="Surabaya Barat">Surabaya Barat</option>
							<option value="Surabaya Selatan">Surabaya Selatan</option>
							<option value="Surabaya Pusat">Surabaya Pusat</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="lamaCust" class="col-sm-2 control-label">Lama menjadi customer</label>
					<div class="col-md-8">
						<select class="form-control input-lg" name="lamaCust">
							<option value="0-3 bln">0-3 bulan</option>
							<option value="4-6 bln">4-6 bulan</option>
							<option value="7-9 bln">7-9 bln</option>
							<option value="10-12 bln">10-12 bulan</option>
							<option value="1-2 th">1-2 tahun</option>
							<option value="2-3 th">2-3 tahun</option>
							<option value=">3 th">>3 tahun</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="tipeToko" class="col-sm-2 control-label">Tipe toko</label>
					<div class="col-md-8">
						<select class="form-control input-lg" name="tipeToko">
							<option value="Toko kelontong">Toko kelontong</option>
							<option value="Off-price Retailer">Off-price Retailer</option>
							<option value="Toko Khusus">Toko Khusus</option>
							<option value="Grosir">Grosir</option>
							<option value="Minimarket">Minimarket</option>
							<option value="Supermarket">Supermarket</option>
							<option value="Hypermarket">Hypermarket</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="avgSales" class="col-sm-2 control-label">Rata-rata penjualan</label>
					<div class="col-md-8">
						<input type="text" class="form-control input-lg" id="avgSales" name="avgSales" placeholder="Contoh: 1000000" onkeypress="var key = event.keyCode || event.charCode; return ((key  >= 48 && key  <= 57) || key == 8);"; required>
					</div>
				</div>

				<div class="form-group">
					<label for="pembayaran" class="col-sm-2 control-label">Pembayaran</label>
					<div class="col-md-8">
						<select class="form-control input-lg" name="pembayaran">
							<option value="Lancar">Lancar</option>
							<option value="Tidak">Tidak lancar</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="npwporktp" class="col-sm-2 control-label">NPWP atau KTP</label>
					<div class="col-md-8">
						<select class="form-control input-lg" name="npwporktp">
							<option value="Ada">Ada</option>
							<option value="Tidak ada">Tidak ada</option>
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label for="aksesKirim" class="col-sm-2 control-label">Akses kirim</label>
					<div class="col-md-8">
						<select class="form-control input-lg" name="aksesKirim">
							<option value="Tidak bisa dilalui kendaraan">Tidak bisa dilalui kendaraan</option>
							<option value="Roda 2">Roda 2</option>
							<option value="Motor Roda 3">Motor Roda 3</option>
							<option value="Mobil Box">Mobil Box</option>
							<option value="Truck Box">Truck Box</option>
							<option value="Wing Box">Wing Box</option>
							<option value="Truck Container">Truck Container</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="kategori" class="col-sm-2 control-label">Kategori</label>
					<div class="col-md-8">
						<select class="form-control input-lg" name="kategori">
							<option value="Bumbu">Bumbu</option>
							<option value="Snack">Snack</option>
							<option value="Bhn Kue">Bahan Kue</option>
							<option value="Tk Obat">Toko Obat</option>
							<option value="Kosmetik">Kosmetik</option>
							<option value="Klontong">Klontong</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="produkMasuk" class="col-sm-2 control-label">Produk yang sudah masuk</label>
					<div class="col-md-8">
						<select class="form-control input-lg" name="produkMasuk">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="6">6</option>
							<option value="7">7</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="potensi" class="col-sm-2 control-label">Potensi outlet bisa jual</label>
					<div class="col-md-8">
						<select class="form-control input-lg" name="potensi">
							<option value="Besar">Besar</option>
							<option value="Tidak">Tidak</option>
						</select>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('code-footer'); ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('adminlte::layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>