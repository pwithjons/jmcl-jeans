<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Contracts\View\View;

class AdminManagementController extends Controller
{
    /**
     * Super-Admin-only: view the list of admin accounts. Full create/
     * edit/deactivate CRUD lands in Phase 3 (Settings section) — this
     * exists in Phase 2 to prove the role:super_admin middleware and the
     * manage-admins Gate actually restrict access end to end.
     */
    public function index(): View
    {
        return view('admin.admins.index', [
            'admins' => Admin::orderBy('name')->get(),
        ]);
    }
}
