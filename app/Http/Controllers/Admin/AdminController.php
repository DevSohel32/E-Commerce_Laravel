<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
   public function index(){
        return view("backend.admin.index");
    }
   public function brands(){
        $brands = Brand::orderBy('id','desc')->paginate(10);
        return view('backend.admin.brands.index', compact('brands'));
    }
    public function addBrand(Request $request){
        return view('backend.admin.brands.create');
    }
}
