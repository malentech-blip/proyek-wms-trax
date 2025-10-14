<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LabelTemplateController extends Controller
{
    public function index()
    {
        // Data dummy untuk 3 kartu template
        $templates = [
            ['name' => 'Label Item Inbound'],
            ['name' => 'Label Produksi Jadi'],
            ['name' => 'Label Pallet Gudang'],
        ];

        return view('super-admin.label-template.index', compact('templates'));
    }
}