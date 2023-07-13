<?php

namespace App\Http\Controllers\Landing\Bank;

use App\Http\Controllers\Controller;
use App\Models\AdditionalData;
use App\Models\NegaraMaster;
use App\Models\VariableData;
use App\Models\VariableMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class VisualizationController extends Controller
{
    private $country;
    public function __construct() {
        $this->country =  NegaraMaster::where('code', Route::current()->parameter('code'))->first();
        if (!$this->country) {
            return abort(500, 'Something went wrong');
        }
    }
    
    public function index()
    {
        $tahun = VariableData::select('tahun', 'variable_masters_id', 'negara_masters_id')
        ->where('negara_masters_id', $this->country->id)
           ->whereNotIn('variable_masters_id', [6,7,8,9,10])
            ->groupBy('tahun')
            ->get();

        $variable = VariableMaster::select('nama_variable', 'id')
            ->whereNotIn('id', [6,7,8,9,10])
            ->get()
            ->toArray();
        $avg =  AdditionalData::where([
            ['name' , '=', 'average_treshold'],
            ['negara_masters_id' , '=', $this->country->id],
            ['jenis' , '=', 'a']
        ])->first();
        $avg = @round($avg->value, 2);
        
        return view('landing.bank.visualization', compact('variable', 'tahun', 'avg'));
    }
}
