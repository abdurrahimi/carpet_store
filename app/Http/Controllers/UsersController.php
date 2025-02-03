<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\User;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $page_size = $request->get('page_size', 10);
        $search = $request->get('search', '');
        $users = Karyawan::with([
            'leader' => function($q){
                //return $q;
                return $q->select('id', 'name');
            },
            'creator' => function($q){
                //return $q;
                return $q->select('id', 'name');
            },
            'account' => function($q){
                return $q->select('id', 'karyawan_id','email');
            },
            'store' => function($q){
                return $q->select('id', 'name');
            }
        ]);

        if($search){
            $users = $users->where('name', 'like', DB::raw("'%".$search."%'"));
        }

        return Inertia::render('Users/Karyawan', [
            'users' => $users->paginate($page_size),
        ]);
    }

    public function getDataUsers(Request $request)
    {
        $name = $request->get('name', '');
        $id = $request->get('name', null);
        $karyawan = Karyawan::query()->select('id','name');
        if($name){
            $karyawan = $karyawan->where('name', 'like', DB::raw("'%".$name."%'"));
        }

        if($id) {
            $karyawan = $karyawan->orWhere('id', '=', $id);
        }

        $karyawan = $karyawan->orWhereRaw('1 = 1')->limit((int) $request->get('limit', 10))->get();
        return response()->json($karyawan);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:100',
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('users')->whereNull('deleted_at')
            ],
            'phone' => 'nullable|string|max:100',
            'npwp' => 'nullable|string|max:100',
            'join_date' => 'nullable|date',
            'leader_id' => 'nullable|integer|exists:users,id',
            'password' => 'nullable|string|min:6|max:16',
        ]);

        DB::beginTransaction();
        try {
            $karyawan = new Karyawan();
            $karyawan->name = $request->name;
            $karyawan->nik = $request->nik;
            $karyawan->email = $request->email;
            $karyawan->phone = $request->phone;
            $karyawan->npwp = $request->npwp;
            $karyawan->join_date = $request->join_date;
            $karyawan->leader_id = $request->leader_id['id'] ?? null;
            $karyawan->toko_id = $request->toko_id['id'] ?? null;
            $karyawan->jabatan_id = $request->jabatan['id'] ?? null;
            $karyawan->save();

            if($request->is_account){
                $user = new User();
                $user->karyawan_id = $karyawan->id;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->save();
            }
            DB::commit();
            return redirect()->route('users.index')->with('success', 'Karyawan berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('users.index')->with('error', 'Karyawan gagal ditambahkan.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:100',
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('users')->whereNull('deleted_at')
            ],
            'phone' => 'nullable|string|max:100',
            'npwp' => 'nullable|string|max:100',
            'join_date' => 'nullable|date',
            'leader_id' => 'nullable|integer|exists:users,id',
            'password' => 'nullable|string|min:6|max:16',
        ]);

        DB::beginTransaction();
        try {
            $karyawan = Karyawan::findOrFail($id);
            $karyawan->name = $request->name;
            $karyawan->nik = $request->nik;
            $karyawan->email = $request->email;
            $karyawan->phone = $request->phone;
            $karyawan->npwp = $request->npwp;
            $karyawan->join_date = $request->join_date;
            $karyawan->leader_id = $request->leader_id['id'] ?? null;
            $karyawan->toko_id = $request->toko_id['id'] ?? null;
            $karyawan->jabatan_id = $request->jabatan['id'] ?? null;
            $karyawan->save();

            if($request->is_account){
                $user = User::where('karyawan_id', $karyawan->id)->first();

                if(!$user){
                    $user = new User();
                }

                $user->name = $request->name;
                $user->email = $request->email;
                $user->karyawan_id = $karyawan->id;
                if($request->password !== "" && $request->password !== null){
                    $user->password = Hash::make($request->password);
                }
                $user->save();
            }
            DB::commit();
            return redirect()->route('users.index')->with('success', 'Karyawan berhasil diperbarui.');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->route('users.index')->with('error', 'Karyawan gagal diperbarui.');
        }

    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $karyawan = Karyawan::findOrFail($id);
            $karyawan->delete();

            $user = User::where('karyawan_id', $karyawan->id)->first();
            if($user){
                $user->delete();
            }
            DB::commit();
            return redirect()->route('users.index')->with('success', 'Karyawan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('users.index')->with('error', 'Karyawan gagal dihapus.');
        }
    }

    public function massDestroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids = $request->input('ids', []);
            $karyawan = Karyawan::whereIn('id', $ids);
            $karyawan->delete();

            $user = User::whereIn('karyawan_id', $ids)->first();
            if($user){
                $user->delete();
            }
            DB::commit();
            return redirect()->route('users.index')->with('success', 'Karyawan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('users.index')->with('error', 'Karyawan gagal dihapus.');
        }
    }
}
