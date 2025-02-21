<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Service\ApprovalService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApprovalController extends Controller
{

    private $service;
    public function __construct()
    {
        $this->service = new ApprovalService();
    }
    public function index(Request $request)
    {
        $pageSize = (int) $request->get('page_size', 10);
        $data = Approval::query()
            ->with('requestor', function ($q) {
                return $q->select('id', 'name');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($pageSize);
        return Inertia::render('Approval/Index', [
            'datas' => $data
        ]);
    }

    public function approvalAction(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'type' => 'required|in:1,2'
        ]);

        //validate requestor
        $validate = $this->service->validateApproval($request->id);
        //if(!$validate) return redirect()->route('approval.index')->with('error', 'Anda tidak memiliki akses untuk menyetujui data ini');

        $this->service->approve($request->id, $request->type);
        return redirect()->route('approval.index')->with('success', $request->type == 0 ? 'Data berhasil Di Reject' : 'Data berhasil Di Approve');
    }
}
