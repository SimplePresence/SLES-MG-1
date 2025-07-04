<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MilkrunController extends Controller
{
    public function milkrunIndex() 
    {
        return view('psx_security.milkrun.milkrun_view');
    }

    public function getSuppliers(Request $request)
    {
        $suppliers = DB::table('master_supplier')
        ->select('vendor', 'supplier_name', 'street')
        ->where('category', 'MILKRUN')
        ->get();

        return response()->json($suppliers);
    }
    public function getMilkrunData()
    {
        $entries = DB::table('milkrun')
            ->select('id', 'dnsj_number', 'no_pol', 'status', 'vendor_name', 'vendor_id', 'date')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($entries);
    }


    public function saveBarang(Request $request)
    {
    $request->validate([
        'data' => 'required|array',
        'data.*.no_pol' => 'required|string',
        'data.*.driver' => 'required|string',
        'data.*.vendor_name' => 'required|string',
        'data.*.vendor_id' => 'required|string',
        'data.*.date' => 'required|date',
        'data.*.dnsj_number' => 'required|string',
        'data.*.date_sj' => 'required|date',
        'data.*.status' => 'required|in:BAWA_BARANG,KOSONG',
    ]);

    $insertData = [];

    foreach ($request->data as $item) {
        $insertData[] = [
            'no_pol' => $item['no_pol'],
            'driver' => $item['driver'],
            'vendor_name' => $item['vendor_name'],
            'vendor_id' => $item['vendor_id'],
            'date' => substr($item['date'], 0, 10),
            'dnsj_number' => $item['dnsj_number'],
            'date_sj' => substr($item['date_sj'], 0, 10),
            'status' => $item['status'],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('milkrun')->insert($insertData);

    return response()->json(['message' => 'Data inserted successfully']);
}


public function saveKosong(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
            'data.*.no_pol' => 'required|string',
            'data.*.driver' => 'required|string',
            'data.*.vendor_name' => 'required|string',
            'data.*.vendor_id' => 'required|string',
            'data.*.date' => 'required|date',
            'data.*.status' => 'required|in:BAWA_BARANG,KOSONG',
        ]);

        $insertData = [];

        foreach ($request->data as $item) {
            $insertData[] = [
                'no_pol'       => $item['no_pol'],
                'driver'       => $item['driver'],
                'vendor_name'  => $item['vendor_name'],
                'vendor_id'    => $item['vendor_id'],
                'date'         => substr($item['date'], 0, 10),
                'dnsj_number'  => null, // Kosong doesn't use this
                'date_sj'      => null, // Kosong doesn't use this
                'status'       => $item['status'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }

        DB::table('milkrun')->insert($insertData);

        return response()->json(['message' => 'Kosong data inserted successfully']);
    }

}
