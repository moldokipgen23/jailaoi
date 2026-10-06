<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
class OperationsController extends Controller
{
    public function index()
    {
        $path=storage_path('app/private/operations/health.json');
        $report=is_file($path)?json_decode(file_get_contents($path),true):null;
        return view('admin.operations.index',['report'=>$report]);
    }
}
