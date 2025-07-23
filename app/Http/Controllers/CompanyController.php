<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CompanyController extends Controller
{

    public function index(Request $request)
    {
        $pageSize = (int) $request->get('page_size', 10);

        return Inertia::render('Company/Index', [
            'datas' => Company::paginate($pageSize)
        ]);
    }

    public function getData(Request $request)
    {
        $search = strtolower($request->get('search', ''));

        $data = Company::query()
            ->when($search, function ($q) use ($search) {
                return $q->where(DB::raw('lower(name)'), 'like', "%{$search}%");
            })
            ->limit(50)
            ->get();

        return response()->json($data);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_account_holder' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'code' => 'required',
            'email' => 'nullable|email|max:255',
        ]);

        DB::beginTransaction();
        try {
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
            }

            Company::create([
                'name' => $request->name,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_account_holder' => $request->bank_account_holder,
                'logo' => $logoPath,
                'code' => $request->code,
                'email' => $request->email,
            ]);

            DB::commit();
            return redirect()->route('company.index')->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('company.index')->with('error', 'Data gagal ditambahkan.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_account_holder' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'email' => 'nullable|email|max:255', // Added email validation
            'code' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $company = Company::findOrFail($id);
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('uploads/logos', 'public');
                $company->logo = $logoPath;
            }

            $company->update([
                'name' => $request->name,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_account_holder' => $request->bank_account_holder,
                'address' => $request->address,
                'code' => $request->code,
                'email' => $request->email,
            ]);

            DB::commit();
            return redirect()->route('company.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('company.index')->with('error', 'Data gagal diperbarui.');
        }
    }
}
