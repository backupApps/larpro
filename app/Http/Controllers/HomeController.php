<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator as Validator;

class HomeController extends Controller
{
    public function dashboard() {
        return view ('dashboard');
    }

    public function index(Request $request) {
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

        return view('index', compact('data', 'request'));
    }

    public function create() {
        return view ('create');
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'foto' => 'required|mimes:png,jpg,jpeg|max:2048',
            'nama' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()) return redirect()->back()->withInput()->withErrors($validator);

        $foto = $request->file('foto');
        $filename = date('Y-m-d').$foto->getClientOriginalName(); //
        $path = 'foto-user/'.$filename;

        Storage::disk('public')->put($path,file_get_contents($foto));

        // memasukkan data ke database
        $data['name'] = $request->nama;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);
        $data['image'] = $filename;

        User::create($data);

        return redirect()->route('admin.index');
    }

    public function edit(Request $request, $id) {
        $data = User::find($id);

        return view('edit', compact('data'));
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|email',
            'password' => 'nullable',
        ]);

        if($validator->fails()) return redirect()->back()->withInput()->withErrors($validator);

        // memasukkan data ke database
        $data['name'] = $request->nama;
        $data['email'] = $request->email;

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        User::whereId($id)->update($data);

        return redirect()->route('admin.index');
    }

    public function delete(Request $request, $id) {
        $data = User::find($id);

        if ($data) {
            $data->delete();
        }

        return redirect()->route('admin.index');
    }
}
