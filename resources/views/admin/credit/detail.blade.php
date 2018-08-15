@extends('adminlte::layouts.app')

@section('code-header')


@endsection

@section('htmlheader_title')
Detail Pemberian Kredit
@endsection

@section('contentheader_title')
Detail Pemberian Kredit
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
<table id="myTable" class="table table-striped table-bordered" cellspacing="0">
  <thead>
    <tr>
      <th>No.</th>
      <th>Nama toko</th>
      <th>Skor</th>
      <th>Status</th>
    </tr>
</thead>
  <tbody>
   @forelse($results as $i => $result) 
    <tr>
      <td style="width: 10px">{{ $i+1 }}</td>
      <td>{{$result->toko->nama}}</td>
      <td>
        {{$result->score}}
      </td>
      <td style="width: 15px">
        @if($result->status == 'Kredit')
          <span class="label bg-blue">{{$result->status}}</span>
        @else
          <span class="label bg-red">{{$result->status}}</span>
        @endif
      </td>
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