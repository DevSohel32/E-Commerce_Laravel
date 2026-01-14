<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AdminController extends Controller
{
    public function index()
    {
        return view('backend.admin.index');
    }

    public function brands()
    {
        $brands = Brand::orderBy('id', 'desc')->paginate(10);

        return view('backend.admin.brands.index', compact('brands'));
    }

    public function brand_create()
    {
        return view('backend.admin.brands.create');
    }

    public function brand_store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:brands,name',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2040',
        ]);
        $brand = new Brand;
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_ext = $image->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_ext;
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
            'name' => 'required|unique:brands,name,'.$request->id,
            'slug' => 'required|unique:brands,slug,'.$request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2040',
        ]);

        $brand = Brand::findOrFail($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        if ($request->hasFile('image')) {
            // Delete old image file
            if (File::exists(public_path('uploads/brands/'.$brand->image))) {
                File::delete(public_path('uploads/brands/'.$brand->image));
            }
            // Process new image
            $image = $request->file('image');
            $file_ext = $image->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_ext;

            $this->generateBrandThumbnailImage($image, $file_name);
            $brand->image = $file_name;
        }
        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Brand has been update successfully');
    }

    public function generateBrandThumbnailImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/brands');
        if (! file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        $manager = new ImageManager(new Driver);
        $img = $manager->read($image->getRealPath());
        $img->cover(124, 124, 'center')->save($destinationPath.'/'.$imageName);
    }

    public function brand_delete($id)
    {
        $brand = Brand::findOrFail($id);
        if (! empty($brand->image)) {
            $imagePath = public_path('uploads/brands/'.$brand->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $brand->delete();

        return redirect()->route('admin.brands')->with('status', 'Brand has been deleted successfully');
    }

    public function categories()
    {
        $categories = Category::orderBy('id', 'desc')->paginate(10);

        return view('backend.admin.categories.index', compact('categories'));
    }

    public function category_create()
    {
        return view('backend.admin.categories.create');
    }

    public function category_store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2040',
        ]);

        $category = new Category;
        $category->name = $request->name;
        $category->slug = Str::slug($request->slug);
        $image = $request->file('image');
        $file_ext = $image->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_ext;
        $this->generateCategoryThumbnailImage($image, $file_name);
        $category->image = $file_name;
        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category has been add successfully');
    }

    public function category_edit($id)
    {
        $category = Category::find($id);

        return view('backend.admin.categories.update', compact('category'));
    }

    public function category_update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:categories,id',
            'name' => 'required|unique:categories,name,'.$request->id,
            'slug' => 'required|unique:categories,slug,'.$request->id,
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2040', // Added nullable
        ]);

        $category = Category::findOrFail($request->id);
        $category->name = $request->name;
        $category->slug = $request->slug; // Save the actual input from the form

        if ($request->hasFile('image')) {
            // Delete old image file if it exists and isn't empty
            if (! empty($category->image) && File::exists(public_path('uploads/categories/'.$category->image))) {
                File::delete(public_path('uploads/categories/'.$category->image));
            }

            // Process new image
            $image = $request->file('image');
            $file_ext = $image->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_ext;

            // Ensure this method saves the file to public/uploads/categories
            $this->generateCategoryThumbnailImage($image, $file_name);

            $category->image = $file_name;
        }

        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category has been updated successfully!');
    }

    public function generateCategoryThumbnailImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/categories');
        if (! file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        $manager = new ImageManager(new Driver);
        $img = $manager->read($image->getRealPath());
        $img->cover(124, 124, 'center')->save($destinationPath.'/'.$imageName);
    }

    public function category_delete($id)
    {
        $category = Category::findOrFail($id);
        if (! empty($category->image)) {
            $imagePath = public_path('uploads/categories/'.$category->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $category->delete();

        return redirect()->route('admin.brands')->with('status', 'Category has been deleted successfully');
    }

    public function products()
    {
        $products = Product::orderBy('id', 'desc')->paginate(10);

        return view('backend.admin.products.index', compact('products'));
    }

    public function product_create()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();

        return view('backend.admin.products.create', compact('categories', 'brands'));
    }

    public function product_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png|max:2040',
            'images.*' => 'mimes:jpg,jpeg,png|max:2040',
            'category_id' => 'required|integer|exists:categories,id',
            'brand_id' => 'required|integer|exists:brands,id',
        ]);

        $product = new Product;
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $destinationPath = public_path('uploads/products');
        if (! File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        // 1. Handle Main Thumbnail
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'product-'.time().'.'.$image->getClientOriginalExtension();

            $manager = new ImageManager(new Driver);
            $img = $manager->read($image->getRealPath());
            $img->cover(540, 689, 'center')->save($destinationPath.'/'.$imageName);
            $product->image = $imageName;
        }

        // 2. Handle Gallery Images
        $gallery = '';
        if ($request->hasFile('images')) {
            $gallery_img = [];
            $count = 1;
            $files = $request->file('images');

            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gfilename = Carbon::now()->timestamp.'-'.$count.'.'.$gextension;

                $manager = new ImageManager(new Driver);
                $img = $manager->read($file->getRealPath());
                $img->cover(540, 689, 'center')->save($destinationPath.'/'.$gfilename);

                $gallery_img[] = $gfilename;
                $count++;
            }
            $gallery = implode(',', $gallery_img);
        }

        $product->images = $gallery; // No longer undefined
        $product->save();

        return redirect()->route('admin.products')->with('status', 'Product has been added successfully!');
    }
   public function product_edit($id)
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        $product = Product::find($id);

        return view('backend.admin.products.update', compact('product','categories','brands'));
    }
    public function product_update(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,'.$product->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required|integer',
            'image' => 'mimes:jpg,jpeg,png|max:2040',
            'images.*' => 'mimes:jpg,jpeg,png|max:2040',
            'category_id' => 'required|integer|exists:categories,id',
            'brand_id' => 'required|integer|exists:brands,id',
        ]);

        $product->name = $request->name;
        $product->slug = $request->slug; // Request theke neoya bhalo karon validate korecho
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $destinationPath = public_path('uploads/products');

        // --- Main Thumbnail Update ---
        if ($request->hasFile('image')) {
            if (File::exists($destinationPath.'/'.$product->image)) {
                File::delete($destinationPath.'/'.$product->image);
            }

            $image = $request->file('image');
            $imageName = 'product-'.time().'.'.$image->getClientOriginalExtension();
            $manager = new ImageManager(new Driver);
            $img = $manager->read($image->getRealPath());
            $img->cover(540, 689, 'center')->save($destinationPath.'/'.$imageName);
            $product->image = $imageName;
        }

        // --- Gallery Images Update ---
        if ($request->hasFile('images')) {

            if (! empty($product->images)) {
                $old_images = explode(',', $product->images);
                foreach ($old_images as $old_img) {
                    if (File::exists($destinationPath.'/'.$old_img)) {
                        File::delete($destinationPath.'/'.$old_img);
                    }
                }
            }

            $gallery_img = [];
            foreach ($request->file('images') as $key => $file) {
                $gfilename = Carbon::now()->timestamp.'-'.$key.'.'.$file->getClientOriginalExtension();
                $manager = new ImageManager(new Driver);
                $img = $manager->read($file->getRealPath());
                $img->cover(540, 689, 'center')->save($destinationPath.'/'.$gfilename);
                $gallery_img[] = $gfilename;
            }
            $product->images = implode(',', $gallery_img);
        }

        $product->save();

        return redirect()->route('admin.products')->with('status', 'Product has been updated successfully!');
    }
    public function product_delete($id)
    {
        $product = Product::findOrFail($id);
        if (! empty($product->image)) {
            $imagePath = public_path('uploads/products/'.$product->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
         if (! empty($product->images)) {
                $old_images = explode(',', $product->images);
                foreach ($old_images as $old_img) {
                    if (File::exists('uploads/products/'.$old_img)) {
                        File::delete('uploads/products/'.$old_img);
                    }
                }
            }
         $product->delete();

        return redirect()->route('admin.products')->with('status', 'Product has been deleted successfully');
    }
}
