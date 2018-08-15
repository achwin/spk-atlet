@extends('adminlte::layouts.app')

@section('code-header')


@endsection

@section('htmlheader_title')
Transaksi
@endsection

@section('contentheader_title')
Transaksi
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
<div style="overflow: auto">

<div style="margin-bottom: 10px">
  <a href="{{url('/transaksi/add-transaksi')}}" type="button" class="btn btn-info btn-md" >
    <i class="fa fa-plus-square"></i> Tambah Transaksi</a>
</div>

<br>

</div>
<table id="myTable" class="table table-striped table-bordered" cellspacing="0">
  <thead>
    <tr>
      <th>No.</th>
      <th style="text-align:center">Tanggal</th>
      <th style="text-align:center">Total Harga</th>
      <th style="text-align:center">Diskon</th>
      <th style="text-align:center">Laba</th>
      <th style="text-align:center">Ditambahkan oleh</th>
      <th style="text-align:center">Action</th>
    </tr> </thead>
  <tbody>
   <?php $number = 1 ?>
   @forelse($transaksi as $t) 
    <tr>
      <td width="5%" style="text-align:center">{{$number}}</td>
      <td width="15%" style="text-align:center">{!!App\Helpers\GeneralHelper::indonesianDateFormat($t->created_at)!!}</td>
      <td width="18%" style="text-align:right">Rp. {{number_format($t->total_harga, 0, ',', '.')}}</td>
      <td width="18%" style="text-align:right">Rp. {{number_format($t->diskon, 0, ',', '.')}}</td>
      <td width="18%" style="text-align:right">Rp. {{number_format($t->laba, 0, ',', '.')}}</td>
      <td width="16%" style="text-align:center">{{$t->created_by}}</td>
      <td width="16%" style="text-align:center">
        <a href="{{url('/transaksi/'.$t->id.'/view-transaksi')}}" type="button" class="btn btn-info btn-md" >
          <i class="fa fa-eye"></i> View Details
        </a>
      </td>
    </tr>
     <?php $number++ ?>
     @empty
        <tr>
          <td colspan="6"><center>Belum ada transaksi</center></td>
        </tr>
    @endforelse
  </tbody>
</table>
  
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