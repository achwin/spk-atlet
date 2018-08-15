@extends('adminlte::layouts.app')

@section('code-header')


@endsection

@section('htmlheader_title')
Data toko
@endsection

@section('contentheader_title')
Data toko
@endsection

@section('main-content')

<br>
<!-- include summernote css/js-->
<div class="flash-message" style="margin-left: -16px;margin-right: -16px; margin-top: 13px;">
  @foreach (['danger', 'warning', 'success', 'info'] as $msg)
  @if(Session::has('alert-' . $msg))
<div class="alert alert-{{ $msg }}">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <p class="" style="border-radius: 0">{{ Session::get('alert-' . $msg) }}</p>
</div>
  {!!Session::forget('alert-' . $msg)!!}
  @endif
  @endforeach
</div>
<div style="margin-bottom: 10px">
  <a href="{{url('shops/add-shop')}}" type="button" class="btn btn-success btn-md" >
    <i class="fa fa-plus-square"></i> Tambah toko</a>
</div>
<div style="overflow: auto">
<table id="myTable" class="table table-striped table-bordered" cellspacing="0">
  <thead>
    <tr>
      <th>No.</th>
      <th>Nama</th>
      <th>Distrik</th>
      <th>Lama customer</th>
      <th>Tipe toko</th>
      <th>Rata2 penjualan</th>
      <th>Pembayaran</th>
      <th>NPWP/KTP</th>
      <th>Akses kirim</th>
      <th>Kategori</th>
      <th>Produk masuk</th>
      <th>Potensi</th>
      {{--<th>Aksi</th>--}}
    </tr>
</thead>
  <tbody>
   @forelse($shops as $i => $shop) 
    <tr>
      <td>{{ $i+1 }}</td>
      <td>{{$shop->nama}}</td>
      <td>{{$shop->distrik}}</td>
      <td>{{$shop->lamaCust}}</td>
      <td>{{$shop->tipeToko}}</td>
      <td>Rp. {{number_format($shop->avgSales, 0, ',', '.')}}</td>
      <td>{{$shop->pembayaran}}</td>
      <td>{{$shop->npwporktp}}</td>
      <td>{{$shop->aksesKirim}}</td>
      <td>{{$shop->kategori}}</td>
      <td>{{$shop->produkMasuk}}</td>
      <td>{{$shop->potensi}}</td>
      {{--<td style="width: 10%;">
        <a style="margin-bottom: 5px;" href="{{'data-siswa/detail/'.$period->id}}" class="btn btn-sm btn-info">Detail</a>
        <a style="margin-bottom: 5px;" onclick="return confirm('Apakah anda yakin untuk menghapus?')" href="{{'data-siswa/delete/'.$period->id}}" class="btn btn-sm btn-danger">Edit</a>
      </td>--}}
    </tr>
     @empty
        <tr>
          <td colspan="8"><center>Belum ada data</center></td>
        </tr>
    @endforelse
  </tbody>
</table>
</div>
  
@endsection

@section('code-footer')
<script type="text/javascript">
  $(document).ready(function(){
      $('#myTable').DataTable({
        destroy: true,
        "ordering": true
      });
    });
</script>
@endsection