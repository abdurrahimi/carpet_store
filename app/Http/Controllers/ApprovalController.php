<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApprovalController extends Controller
{
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
}
