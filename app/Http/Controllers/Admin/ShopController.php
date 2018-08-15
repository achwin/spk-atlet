<?php 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Shop;
use Excel;
use Session;
use Response;
use DB;

class ShopController extends Controller
{

    public function index()
    {
        $shops = DB::table('shops')->get();
        $data = [
            'page'      => 'shops',
            'shops'     => $shops,
        ];
        return view('admin.shop.index',$data);
    }

    public function indexExport()
    {
        $periods = DB::table('periods')->get();
        $data = [
            'page'      => 'export',
            'periods'  => $periods,
        ];
        return view('admin.data.export',$data);
    }

    public function detail($period_id)
    {
        $students = DB::table('students')->where('period_id','=',$period_id)->get();
        $data = [
            'page'      => 'data-siswa',
            'students'  => $students,
        ];
        return view('admin.data.detail',$data);
    }

    public function addShop()
    {
        $data = [
            'page' => 'shops',
        ];
        return view('admin.shop.add',$data);
    }

    public function postAddShop(Request $request)
    {   
        $attributes = $request->all();
        Shop::create($attributes);
        Session::put('alert-success', 'Toko berhasil ditambahkan.');
        return Redirect::to('shops');
    }

    public function delete($period_id)
    {
        $period = Period::find($period_id);
        $students = Student::where('period_id',$period_id);
        $students->delete();
        $period->delete();
        Session::put('alert-success', 'Data periode '.$period->tahun.' berhasil dihapus.');
        return Redirect::to('data-siswa');

    }

    public function export($period_id)
    {
        $period = Period::find($period_id);
        $students = Student::where('period_id',$period_id)->get();
        \Excel::create('Data siswa periode '.str_replace('/', '_', $period->tahun).' ('.date('d-m-Y').')', function($excel) use($students){
                $excel->sheet('sheet', function($sheet) use($students){
                    $studentData = array();
                    $no = 0;
                    foreach ($students as $student) {
                        $studentData[] = array(
                            ++$no,
                            $student->nama,
                            $student->nilai_un,
                            $student->nilai_test_penempatan,
                            $student->nilai_ujian_sekolah,
                            $student->hasil,
                            $student->jurusan,
                        );
                    }
                    $sheet->fromArray($studentData, null, 'A1', false, false);
                    $headings = array('No','Nama','Nilai UN','Nilai Test Penempatan','Nilai Ujian Sekolah','Hasil Perhitungan','Jurusan');
                    $sheet->prependRow(1, $headings);
                });
            })->store('xlsx',public_path('excel/'));
        return response()->download(public_path('excel/Data siswa periode '.str_replace('/', '_', $period->tahun).' ('.date('d-m-Y').').xlsx'));
    }

    public function tfn($criterias)
    {
        $sumDefuzzied = 0;
        foreach ($criterias as $criteriaName => $criteria) {
            if ($criteria == 'VL') {
                $criterias[$criteriaName] = [0,0,0.1];
            }
            elseif ($criteria == 'L') {
                $criterias[$criteriaName] = [0,0.1,0.3];
            }
            elseif ($criteria == 'ML') {
                $criterias[$criteriaName] = [0.1,0.3,0.5];
            }
            elseif ($criteria == 'M') {
                $criterias[$criteriaName] = [0.3,0.5,0.7];
            }
            elseif ($criteria == 'MH') {
                $criterias[$criteriaName] = [0.5,0.7,0.9];
            }
            elseif ($criteria == 'H') {
                $criterias[$criteriaName] = [0.7,0.9,1];
            }
            elseif ($criteria == 'VH') {
                $criterias[$criteriaName] = [0.9,1,1];
            }
            $criterias[$criteriaName] = array_sum($criterias[$criteriaName])/3;
            $sumDefuzzied = $sumDefuzzied + $criterias[$criteriaName];
        }
        foreach ($criterias as $criteriaName => $criteria) {
            $criterias[$criteriaName] = $criterias[$criteriaName] / $sumDefuzzied;
        }
        return $criterias;
    }
}