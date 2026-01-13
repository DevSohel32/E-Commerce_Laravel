<?php

namespace App\Http\Controllers\Admin;
use Carbon\Carbon;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Enums\Position;
use Intervention\Image\Drivers\Gd\Driver;


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
    public function brand_edit($id)
    {
        $brand = Brand::find($id);

        return view('backend.admin.brands.update', compact('brand'));
    }

    public function brand_update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:brands,id',
            'name' => 'required|unique:brands,name,' . $request->id,
            'slug' => 'required|unique:brands,slug,' . $request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2040'
        ]);

        $brand = Brand::findOrFail($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        if ($request->hasFile('image')) {
            // Delete old image file
            if (File::exists(public_path('uploads/brands/' . $brand->image))) {
                File::delete(public_path('uploads/brands/' . $brand->image));
            }
            // Process new image
            $image = $request->file('image');
            $file_ext = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_ext;

            $this->generateBrandThumbnailImage($image, $file_name);
            $brand->image = $file_name;
        }
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been update successfully');
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

    public function brand_delete($id)
    {
        $brand = Brand::findOrFail($id);
        if (!empty($brand->image)) {
            $imagePath = public_path('uploads/brands/' . $brand->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $brand->delete();
        return redirect()->route('admin.brands')->with('status', 'Brand has been deleted successfully');
    }


    public function categories(){
        $category = Category::orderBy('name','asc')->paginate(10);
        return view('backend.admin.categories.index', compact('categories'));
    }
}
