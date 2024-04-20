<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator as Validator;
use Yajra\DataTables\Facades\DataTables;

class DataTableController extends Controller
{
    public function clientside(Request $request) {
      $data = new User;

      if($request->get('search')) {
         $data = $data->where('name', 'LIKE', '%'.$request->get('search').'%')
         ->orWhere('email', 'LIKE', '%'.$request->get('search').'%');
      }

      // contoh
      if($request->get('tanggal')) {
         $data = $data->where('name', 'LIKE', '%'.$request->get('search').'%')
         ->orWhere('email', 'LIKE', '%'.$request->get('search').'%');
      }

      $data = $data->get();

      return view('datatable.clientside', compact('data', 'request'));
   }

   public function serverside(Request $request) {
      
      if($request->ajax()) {
         $data = new User;
         
         $data = $data->latest();
         return DataTables::of($data)
         ->addColumn('no', function($data) {
            return 'nomor';
         })
         ->addColumn('foto', function($data) {
            return '<img src="'.asset('storage/foto-user/'.$data->image).'" alt="" width="90">';
         })
         ->addColumn('nama', function($data) {
            return $data->name;
         })
         ->addColumn('email', function($data) {
            return $data->email;
         })
         ->addColumn('aksi', function($data) {
            return '<a href="'.route('admin.user.edit', ['id' => $data->id]).'" class="btn btn-primary"><i class="fas fa-pen"></i> Edit</a>
            <a class="btn btn-danger" data-toggle="modal" data-target="#modal-hapus'.$data->id.'"><i class="fas fa-trash-alt"></i> Hapus</a>';
         })
         ->rawColumns(['foto', 'aksi']) // untuk merender HTML
         ->make(true);
      }

      return view('datatable.serverside', compact('request'));
   }
}
