<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\Image;
use App\Http\Controllers\Controller;


class AdminController extends Controller
{
    public function index()
    {
        return view("backend.admin.index");
    }
    public function brands()
    {
        $brands = Brand::orderBy('id', 'desc')->paginate(10);
        return view('backend.admin.brands.index', compact('brands'));
    }
    public function add_brand()
    {
        return view('backend.admin.brands.create');
    }

    public function brand_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2040'
        ]);
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $file_ext = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_ext;

            $brand->image = $file_name;
        }

        $brand->save();
        return redirect('admin.brands')->with('status', 'Brand has been add successfully');
    }

    public function generateBrandThumbnailImage($image, $imageName)
    {
        $destinationPath = public_path('upload/brands');
        $destinationPath = public_path('uploads/brands');
        $img = Image::make($image->path());
        $img->cover(124, 124, 'center');
        $img->save($destinationPath . '/' . $imageName);
    }
}
