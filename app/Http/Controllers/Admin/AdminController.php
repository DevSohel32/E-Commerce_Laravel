<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Enums\Position;


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
            'name' => 'required|unique:brands,name',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2040'
        ]);
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_ext = $image->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_ext;
        $this->generateBrandThumbnailImage($image, $file_name);
        $brand->image = $file_name;
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been add successfully');
    }


    public function generateBrandThumbnailImage($image, $imageName)
{
    $destinationPath = public_path('uploads/brands');
    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }
    $manager = new ImageManager(new Driver());
    $img = $manager->read($image->getRealPath());

    $img->cover(124, 124, 'center')->save($destinationPath . '/' . $imageName);
}
}
