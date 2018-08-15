<?php 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Shop;
use App\Credit;
use App\Result;
use Excel;
use Session;
use Response;
use DB;

class PemberianKreditController extends Controller
{

    public function index()
    {
        $credits = DB::table('credits')->get();
        $data = [
            'page'      => 'kelola-pemberian-kredit',
            'credits'     => $credits,
        ];
        return view('admin.credit.index',$data);
    }

    public function add()
    {
        $shops = Shop::all();
        $data = [
            'shops' => $shops,
            'page' => 'kelola-pemberian-kredit',
        ];
        return view('admin.credit.add',$data);
    }

    public function postAdd(Request $request)
    {   
        $attributes = $request->all();
        $data = [];
        $credit = Credit::create([
            'zt_lamaCust' => $attributes['zt_lamaCust'][0].'-'.$attributes['zt_lamaCust'][1],
            'zt_tipeToko' => $attributes['zt_tipeToko'][0].'-'.$attributes['zt_tipeToko'][1],
            'zt_avgSales' => $attributes['zt_avgSales'][0].'-'.$attributes['zt_avgSales'][1],
            'zt_produkMasuk' => $attributes['zt_produkMasuk'][0].'-'.$attributes['zt_produkMasuk'][1],
            'zt_aksesKirim' => $attributes['zt_aksesKirim'][0].'-'.$attributes['zt_aksesKirim'][1],
            'dk_validasi' => $attributes['dk_validasi'],
            'dk_lamaCust' => $attributes['dk_lamaCust'],
            'dk_tipeToko' => $attributes['dk_tipeToko'],
            'dk_avgSales' => $attributes['dk_avgSales'],
            'dk_produkMasuk' => $attributes['dk_produkMasuk'],
            'dk_aksesKirim' => $attributes['dk_aksesKirim'],
            'toko_diambil' => $attributes['toko_diambil']
        ]);
        foreach ($attributes['nama_toko'] as $toko) {
            $shop = Shop::find($toko);
            $array = [$shop->nama => [
                'lamaCust' => $shop->lamaCust,
                'tipeToko' => $shop->tipeToko,
                'avgSales' => $shop->avgSales,
                'aksesKirim' => $shop->aksesKirim,
                'produkMasuk' => $shop->produkMasuk
            ]];
            $data = array_merge($data,$array);
        }
        $criteria = [
            'validasi' => $attributes['dk_validasi'],
            'lamaCust' => $attributes['dk_lamaCust'],
            'tipeToko' => $attributes['dk_tipeToko'],
            'avgSales' => $attributes['dk_avgSales'],
            'aksesKirim' => $attributes['dk_aksesKirim'],
            'produkMasuk' => $attributes['dk_produkMasuk'],
        ];
        $criteria = $this->dkToTfn($criteria);
        $zonaToleransi = [
            'lamaCust' => [$attributes['zt_lamaCust'][0],$attributes['zt_lamaCust'][1]],
            'tipeToko' => [$attributes['zt_tipeToko'][0],$attributes['zt_tipeToko'][1]],
            'avgSales' => [$attributes['zt_avgSales'][0],$attributes['zt_avgSales'][1]],
            'aksesKirim' => [$attributes['zt_aksesKirim'][0],$attributes['zt_aksesKirim'][1]],
            'produkMasuk' => [$attributes['zt_produkMasuk'][0],$attributes['zt_produkMasuk'][1]],
        ];
        // Konversi kriteria,zona toleransi dan alternatif
        $criteriaTfn = $this->tfn($data);
        $zonaToleransiTfn =  $this->zonaToleransiTfn($zonaToleransi);

        // Alfa 0
        // Derajat kepentingan
        $dkCriteriaToko0 = $this->derajatKepentingan($criteriaTfn,'toko',0);
        $dkCriteria0 = $this->derajatKepentingan($criteria,'criteria',0);
        $dkZonaToleransi0 = $this->derajatKepentingan($zonaToleransiTfn,'zona toleransi',0);

        // Normalisasi
        $normalisasiToko0 = $this->normalisasi($dkCriteriaToko0,$dkZonaToleransi0);
        $normalisasiToko0 = $this->lambdaSubKriteria($normalisasiToko0,$dkCriteria0);

        // Lambda
        $result0 = $this->lambdaKriteria($normalisasiToko0,$dkCriteria0);

        // Alfa 1
        // Derajat kepentingan
        $dkCriteriaToko1 = $this->derajatKepentingan($criteriaTfn,'toko',1);
        $dkCriteria1 = $this->derajatKepentingan($criteria,'criteria',1);
        $dkZonaToleransi1 = $this->derajatKepentingan($zonaToleransiTfn,'zona toleransi',1);
        // Normalisasi
        $normalisasiToko1 = $this->normalisasi($dkCriteriaToko1,$dkZonaToleransi1);
        $normalisasiToko1 = $this->lambdaSubKriteria($normalisasiToko1,$dkCriteria1);

        // Lambda
        $result1 = $this->lambdaKriteria($normalisasiToko1,$dkCriteria1);

        // Hasil akhir (Menggabungkan array dan meranking)
        $mergeResult = $this->mergeResult($result0,$result1,$attributes['toko_diambil']);
        foreach ($attributes['nama_toko'] as $id) {
            $shop = Shop::find($id);
            Result::create([
                'shop_id' => $id,
                'credit_id' => $credit->id,
                'score' => $mergeResult[$shop->nama]['skor'],
                'status' => $mergeResult[$shop->nama]['status'],
            ]);
        }
        // Shop::create($attributes);
        // Session::put('alert-success', 'Toko berhasil ditambahkan.');
        // return Redirect::to('shops');
    }

        public function tfn($data)
    {
        $tfn = [];
        foreach ($data as $i => $d) {
            foreach ($d as $namaCriteria => $valueCriteria) {
                switch ($namaCriteria) {
                    case 'lamaCust':
                        if ($valueCriteria == '0-3 bln') {
                            $tfn[$i]['lamaCust'] = [0,0,0.1,0.2];
                        }
                        elseif ($valueCriteria == '4-6 bln') {
                            $tfn[$i]['lamaCust'] = [0.1,0.2,0.2,0.3];
                        }
                        elseif ($valueCriteria == '7-9 bln') {
                            $tfn[$i]['lamaCust'] = [0.2,0.3,0.4,0.5];
                        }
                        elseif ($valueCriteria == '10-12 bln') {
                            $tfn[$i]['lamaCust'] = [0.4,0.5,0.5,0.6];
                        }
                        elseif ($valueCriteria == '1-2 th') {
                            $tfn[$i]['lamaCust'] = [0.5,0.6,0.7,0.8];
                        }
                        elseif ($valueCriteria == '2-3 th') {
                            $tfn[$i]['lamaCust'] = [0.7,0.8,0.8,0.9];
                        }
                        elseif ($valueCriteria == '>3 th') {
                            $tfn[$i]['lamaCust'] = [0.8,0.9,1.0,1.0];
                        }
                        break;
                    case 'tipeToko':
                        if ($valueCriteria == 'Toko kelontong') {
                            $tfn[$i]['tipeToko'] = [0,0,0.1,0.2];       
                        }
                        elseif ($valueCriteria == 'Off-price Retailer') {
                            $tfn[$i]['tipeToko'] = [0.1,0.2,0.2,0.3];
                        }
                        elseif ($valueCriteria == 'Toko Khusus') {
                            $tfn[$i]['tipeToko'] = [0.2,0.3,0.4,0.5];
                        }
                        elseif ($valueCriteria == 'Grosir') {
                            $tfn[$i]['tipeToko'] = [0.4,0.5,0.5,0.6];
                        }
                        elseif ($valueCriteria == 'Minimarket') {
                            $tfn[$i]['tipeToko'] = [0.5,0.6,0.7,0.8];
                        }
                        elseif ($valueCriteria == 'Supermarket') {
                            $tfn[$i]['tipeToko'] = [0.7,0.8,0.8,0.9];
                        }
                        elseif ($valueCriteria == 'Hypermarket') {
                            $tfn[$i]['tipeToko'] = [0.8,0.9,1.0,1.0];
                        }
                    case 'avgSales':
                        if ($valueCriteria >= 0 && $valueCriteria <= 199999) {
                            $tfn[$i]['avgSales'] = [0,0,0.1,0.2];       
                        }
                        elseif ($valueCriteria >= 200.000 && $valueCriteria <= 499.999) {
                            $tfn[$i]['avgSales'] = [0.1,0.2,0.2,0.3];
                        }
                        elseif ($valueCriteria >= 500.000 && $valueCriteria <= 999999) {
                            $tfn[$i]['avgSales'] = [0.2,0.3,0.4,0.5];
                        }
                        elseif ($valueCriteria >= 1000000 && $valueCriteria <= 2999999) {
                            $tfn[$i]['avgSales'] = [0.4,0.5,0.5,0.6];
                        }
                        elseif ($valueCriteria >= 3000000 && $valueCriteria <= 4999999) {
                            $tfn[$i]['avgSales'] = [0.5,0.6,0.7,0.8];
                        }
                        elseif ($valueCriteria >= 5000000 && $valueCriteria <= 9999999) {
                            $tfn[$i]['avgSales'] = [0.7,0.8,0.8,0.9];
                        }
                        elseif ($valueCriteria >= 10000000) {
                            $tfn[$i]['avgSales'] = [0.8,0.9,1.0,1.0];
                        }
                        break;
                    case 'aksesKirim':
                        if ($valueCriteria == 'Tidak bisa dilalui kendaraan') {
                            $tfn[$i]['aksesKirim'] = [0,0,0.1,0.2];     
                        }
                        elseif ($valueCriteria == 'Roda 2') {
                            $tfn[$i]['aksesKirim'] = [0.1,0.2,0.2,0.3];
                        }
                        elseif ($valueCriteria == 'Motor Roda 3') {
                            $tfn[$i]['aksesKirim'] = [0.2,0.3,0.4,0.5];
                        }
                        elseif ($valueCriteria == 'Mobil Box') {
                            $tfn[$i]['aksesKirim'] = [0.4,0.5,0.5,0.6];
                        }
                        elseif ($valueCriteria == 'Truck Box') {
                            $tfn[$i]['aksesKirim'] = [0.5,0.6,0.7,0.8];
                        }
                        elseif ($valueCriteria == 'Wing Box') {
                            $tfn[$i]['aksesKirim'] = [0.7,0.8,0.8,0.9];
                        }
                        elseif ($valueCriteria == 'Truck Container') {
                            $tfn[$i]['aksesKirim'] = [0.8,0.9,1.0,1.0];
                        }
                        break;
                    case 'produkMasuk':
                        if ($valueCriteria == 1) {
                            $tfn[$i]['produkMasuk'] = [0,0,0.1,0.2];        
                        }
                        elseif ($valueCriteria == 2) {
                            $tfn[$i]['produkMasuk'] = [0.1,0.2,0.2,0.3];
                        }
                        elseif ($valueCriteria == 3) {
                            $tfn[$i]['produkMasuk'] = [0.2,0.3,0.4,0.5];
                        }
                        elseif ($valueCriteria == 4) {
                            $tfn[$i]['produkMasuk'] = [0.4,0.5,0.5,0.6];
                        }
                        elseif ($valueCriteria == 5) {
                            $tfn[$i]['produkMasuk'] = [0.5,0.6,0.7,0.8];
                        }
                        elseif ($valueCriteria == 6) {
                            $tfn[$i]['produkMasuk'] = [0.7,0.8,0.8,0.9];
                        }
                        elseif ($valueCriteria == 7) {
                            $tfn[$i]['produkMasuk'] = [0.8,0.9,1.0,1.0];
                        }
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        return $tfn;
    }

    public function zonaToleransiTfn($zonaToleransi)
    {
        $tfn = [];
        foreach ($zonaToleransi as $i => $criteria) {
            foreach ($criteria as $j => $label) {
                switch ($j) {
                    case 0:
                        if ($label == 'SR') {
                            $tfn[$i] = [0,0];
                        }
                        elseif ($label == 'R') {
                            $tfn[$i] = [0.1,0.2];
                        }
                        elseif ($label == 'RS') {
                            $tfn[$i] = [0.2,0.3];
                        }
                        elseif ($label == 'S') {
                            $tfn[$i] = [0.4,0.5];
                        }
                        elseif ($label == 'BS') {
                            $tfn[$i] = [0.5,0.6];
                        }
                        elseif ($label == 'B') {
                            $tfn[$i] = [0.7,0.8];
                        }
                        elseif ($label == 'SB') {
                            $tfn[$i] = [0.8,0.9];
                        }
                        break;
                    case 1:
                        if ($label == 'SR') {
                            $tfn[$i] = array_merge($tfn[$i],[0.1,0.2]);
                        }
                        elseif ($label == 'R') {
                            $tfn[$i] = array_merge($tfn[$i],[0.2,0.3]);
                        }
                        elseif ($label == 'RS') {
                            $tfn[$i] = array_merge($tfn[$i],[0.4,0.5]);
                        }
                        elseif ($label == 'S') {
                            $tfn[$i] = array_merge($tfn[$i],[0.5,0.6]);
                        }
                        elseif ($label == 'BS') {
                            $tfn[$i] = array_merge($tfn[$i],[0.7,0.8]);
                        }
                        elseif ($label == 'B') {
                            $tfn[$i] = array_merge($tfn[$i],[0.8,0.9]);
                        }
                        elseif ($label == 'SB') {
                            $tfn[$i] = array_merge($tfn[$i],[1,1]);
                        }
                        break;
                }
            }
        }
        return $tfn;
    }

    public function derajatKepentingan($data,$type,$alfa)
    {
        $derajatKepentingan = [];
        if ($alfa == 0) {
            $pos0 = 0;
            $pos1 = 3;
        }
        elseif ($alfa == 1) {
            $pos0 = 1;
            $pos1 = 2;
        }
        if ($type == 'criteria' || $type == 'zona toleransi') {
            foreach ($data as $i => $tfn) {
                $derajatKepentingan[$i][0] = $tfn[$pos0];
                $derajatKepentingan[$i][1] = $tfn[$pos1];              
            }
        }
        elseif ($type == 'toko') {
            foreach ($data as $namaToko => $criteria) {
                foreach ($criteria as $i => $tfn) {
                    $derajatKepentingan[$namaToko][$i][0] = $tfn[$pos0];
                    $derajatKepentingan[$namaToko][$i][1] = $tfn[$pos1];
                }
            }
        }
        return $derajatKepentingan;
    }

    public function normalisasi($dkCriteriaToko,$dkZonaToleransi)
    {
        $normalisasi = [];
        foreach ($dkCriteriaToko as $namaToko => $toko) {
            foreach ($toko as $namaCriteria => $value) {
                $normalisasi[$namaToko][$namaCriteria][0] = ($dkCriteriaToko[$namaToko][$namaCriteria][0]-$dkZonaToleransi[$namaCriteria][1]+1)/2;
                $normalisasi[$namaToko][$namaCriteria][1] = ($dkCriteriaToko[$namaToko][$namaCriteria][1]-$dkZonaToleransi[$namaCriteria][0]+1)/2;
            }
        }
        return $normalisasi;
    }

    public function lambdaSubKriteria($normalisasiToko,$dkCriteria)
    {
        foreach ($normalisasiToko as $namaToko => $toko) {
            if ((bccomp($toko['lamaCust'][0], $toko['tipeToko'][0],3))&&($toko['lamaCust'][0] > $toko['tipeToko'][0])) {
                $fc11 = $toko['tipeToko'][0];
                $fc12 = $toko['lamaCust'][0];
                $g11 = $dkCriteria['tipeToko'][0];
                $g12 = $dkCriteria['lamaCust'][0];
            }
            else{
                $fc11 = $toko['lamaCust'][0];
                $fc12 = $toko['tipeToko'][0];
                $g11 = $dkCriteria['lamaCust'][0];
                $g12 = $dkCriteria['tipeToko'][0];
            }
            if ((bccomp($toko['lamaCust'][1], $toko['tipeToko'][1],3))&&($toko['lamaCust'][1] > $toko['tipeToko'][1])) {
                $fc21 = $toko['tipeToko'][1];
                $fc22 = $toko['lamaCust'][1];
                $g21 = $dkCriteria['tipeToko'][1];
                $g22 = $dkCriteria['lamaCust'][1];
            }
            else{
                $fc21 = $toko['lamaCust'][1];
                $fc22 = $toko['tipeToko'][1];
                $g21 = $dkCriteria['lamaCust'][1];
                $g22 = $dkCriteria['tipeToko'][1];
            }
            $a1 = (1-($g11+$g12))/($g11*$g12);
            $a2 = (1-($g21+$g22))/($g21*$g22);

            $ga11 = $g11+$g12+($a1*$g11*$g12);
            $ga21 = $g21+$g22+($a2*$g21*$g22);
            $ga12 = $g12;
            $ga22 = $g22;
            $fdg1 = ($ga11*$fc11)+($ga12*($fc12-$fc11));
            $fdg2 = ($ga21*$fc21)+($ga22*($fc22-$fc21));
            $normalisasiToko[$namaToko]['validasi'][0] = $fdg1;
            $normalisasiToko[$namaToko]['validasi'][1] = $fdg2;
            unset($normalisasiToko[$namaToko]['lamaCust']);
            unset($normalisasiToko[$namaToko]['tipeToko']);
        }
        return $normalisasiToko;
    }

    public function lambdaKriteria($normalisasiToko,$dkCriteria)
    {
        foreach ($normalisasiToko as $namaToko => $toko) {
            $sortToko0 = $toko;
            uasort($sortToko0, function($a, $b) {
                return $a[0] > $b[0];
            });
            $i = 1;
            foreach ($sortToko0 as $criteria => $st0) {
                ${"fc1".$i} = $st0[0];
                ${"g1".$i} = $dkCriteria[$criteria][0];
                $i++;
            }
            $sortToko1 = $toko;
            uasort($sortToko1, function($a, $b) {
                return $a[1] > $b[1];
            });
            $i = 1;
            foreach ($sortToko1 as $criteria => $st1) {
                ${"fc2".$i} = $st1[1];
                ${"g2".$i} = $dkCriteria[$criteria][1];
                $i++;
            }

            $d = $g11*$g12*$g13*$g14;
            $c = ($g11+$g12+$g13+$g14-1)/$d;
            $b = (($g11*$g12)+(($g11+$g12)*($g13+$g14))+($g13*$g14))/$d;
            $a = ((($g11*$g12)*($g13+$g14))+(($g13*$g14)*($g11+$g12)))/$d;
            
            $p = $b - ((pow($a,2))/3);
            $q = ((2*pow($a, 3))/27)-(($a*$b)/3)+$c;
            $diskriminan = (pow($q, 2)/4)+(pow($p, 3)/27);

            if ($diskriminan > 0) {
                $lambda = pow(((-$q/2)+sqrt($diskriminan)), 1/3) + -pow((($q/2)+sqrt($diskriminan)), 1/3) - ($a/3);
            }
            else{
                $lambda = -1;
            }
            $ga14 = $g14;
            $ga13 = $g13+$ga14+($lambda*$g13*$ga14);
            $ga12 = $g12+$ga13+($lambda*$g12*$ga13);
            $ga11 = $g11+$ga12+($lambda*$g11*$ga12);
            $fdg1 = ($ga11*$fc11)+($ga12*($fc12-$fc11))+($ga13*($fc13-$fc12))+($ga14*($fc14-$fc13));

            $d = $g21*$g22*$g23*$g24;
            $c = ($g21+$g22+$g23+$g24-1)/$d;
            $b = (($g21*$g22)+(($g21+$g22)*($g23+$g24))+($g23*$g24))/$d;
            $a = ((($g21*$g22)*($g23+$g24))+(($g23*$g24)*($g21+$g22)))/$d;
            
            $p = $b - ((pow($a,2))/3);
            $q = ((2*pow($a, 3))/27)-(($a*$b)/3)+$c;
            $diskriminan = (pow($q, 2)/4)+(pow($p, 3)/27);

            if ($diskriminan > 0) {
                $lambda = pow(((-$q/2)+sqrt($diskriminan)), 1/3) + -pow((($q/2)+sqrt($diskriminan)), 1/3) - ($a/3);
            }
            else{
                $lambda = -1;
            }
            $ga24 = $g24;
            $ga23 = $g23+$ga24+($lambda*$g23*$ga24);
            $ga22 = $g22+$ga23+($lambda*$g22*$ga23);
            $ga21 = $g21+$ga22+($lambda*$g21*$ga22);
            $fdg2 = ($ga21*$fc21)+($ga22*($fc22-$fc21))+($ga23*($fc23-$fc22))+($ga24*($fc24-$fc23));
            $result[$namaToko]['fdg1'] = $fdg1;
            $result[$namaToko]['fdg2'] = $fdg2;
        }
        return $result;
    }

    public function mergeResult($result0,$result1,$pick)
    {
        $result = [];
        foreach ($result0 as $namaToko => $toko) {
            $result[$namaToko]['skor'] = ($result0[$namaToko]['fdg1'] + $result1[$namaToko]['fdg1'] +$result1[$namaToko]['fdg2'] + $result0[$namaToko]['fdg2'])/4;
        }
        $rank = $result;
        // Ranking
        // rsort($rank);
        usort($rank, function($a, $b) {
                return $a['skor'] < $b['skor'];
            });
        foreach ($result as $namaToko => $toko) {
            $result[$namaToko]['status'] = array_search($result[$namaToko]['skor'], array_column($rank, 'skor'))+1;
        }
        foreach ($result as $namaToko => $toko) {
            if ($result[$namaToko]['status'] <= $pick) {
                $result[$namaToko]['status'] = 'Kredit';
            }
            else{
                $result[$namaToko]['status'] = 'Tunai';
            }
        }
        return $result;
    }

    public function dkToTfn($criterias)
    {
        foreach ($criterias as $namaCriteria => $criteria) {
            if ($criteria == 'Tidak penting sekali') {
                $criterias[$namaCriteria] = [0,0,0.1,0.2];
            }
            elseif ($criteria == 'Tidak penting') {
                $criterias[$namaCriteria] = [0.1,0.2,0.2,0.3];
            }
            elseif ($criteria == 'Sedikit tidak penting') {
                $criterias[$namaCriteria] = [0.2,0.3,0.4,0.5];
            }
            elseif ($criteria == 'Sedang') {
                $criterias[$namaCriteria] = [0.4,0.5,0.5,0.6];
            }
            elseif ($criteria == 'Sedikit penting') {
                $criterias[$namaCriteria] = [0.5,0.6,0.7,0.8];
            }
            elseif ($criteria == 'Penting') {
                $criterias[$namaCriteria] = [0.7,0.8,0.8,0.9];
            }
            elseif ($criteria == 'Penting sekali') {
                $criterias[$namaCriteria] = [0.8,0.9,1,1];
            }
        }
        return $criterias;
    }

    public function detail($credit_id)
    {
        $results = Result::where('credit_id',$credit_id)->get();
        $data = [
            'results' => $results,
            'page' => 'kelola-pemberian-kredit'
        ];
        return view('admin.credit.detail',$data);
    }
}