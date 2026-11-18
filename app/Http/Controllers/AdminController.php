<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Carbon\Carbon;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;


class AdminController extends Controller
{
////////// THE DASHBOARD PAGE    ///////////////////////////////////////////////////////
    public function index(){
        return view('admin.index');
    }
///////////// THE BRANDS FUNCTIONS //////////////////////////////////////////////////
    public function brands(){
        $brands = Brand::orderBy('id', 'DESC')->paginate(10);
        return view('admin.brands', compact('brands'));
    }

    public function new_brand(){

        return view('admin.new_brand');
    }

    public function edit_brand($id){
        $brand = Brand::find($id);
        return view('admin.edit_brand', compact('brand'));
    }

    public function delete_brand($id){
        $brand = Brand::find($id);

        if(File::exists(public_path('uploads/brands').'/'.$brand->image)){
                File::delete(public_path('uploads/brands').'/'.$brand->image);
        }
        $brand->delete();

        return redirect()->route('admin.brands')->with('status','Brand has been deleted successfully');
    }

    public function create_brand(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        echo("The slug is: $brand->slug");
        

        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extension;
        $this->BrandThumbnails($image, $file_name);
        $brand->image = $file_name;
        $brand->save();

        return redirect()->route('admin.brands')->with('status','The brand has been created succesifully');
    }

    public function update_brand(Request $request){
            $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/brands').'/'.$brand->image)){
                File::delete(public_path('uploads/brands').'/'.$brand->image);
            }

            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->BrandThumbnails($image, $file_name);

            $brand->image = $file_name;
        }
        $brand->save();

        return redirect()->route('admin.brands')->with('status','The brand has been updated succesifully');
 
    }

    public function BrandThumbnails($image, $imageName){
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->getRealPath());
        $img->cover(124,124,"top");
        $img->resize(124,124, function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

///////////// THE CATEGORY FUNCTIONS //////////////////////////////////////////////////
    public function categories(){
        $categories = Category::orderBy('id', 'DESC')->paginate(10);

        return view('admin.categories', compact('categories'));
    }

    public function new_category(){

        return view('admin.new_category');
    }

    public function create_category(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
 
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extension;
        $this->CategoryThumbnails($image, $file_name);
        $category->image = $file_name;
        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category has been created successfully');
    }

        public function CategoryThumbnails($image, $imageName){
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->getRealPath());
        $img->cover(124,124,"top");
        $img->resize(124,124, function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function edit_cateogry($id){
        $category = Category::find($id);
        return view('admin.edit_category', compact('category'));
    }

    public function update_category(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/categories').'/'.$category->image)){
                File::delete(public_path('uploads/categories').'/'.$category->image);
            }

            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->CategoryThumbnails($image, $file_name);

            $category->image = $file_name;
        }
        $category->save();

        return redirect()->route('admin.categories')->with('status','The category  has been updated succesifully');
 
    }

    public function delete_category($id){
        $category = Category::find($id);

        if(File::exists(public_path('uploads/categories').'/'.$category->image)){
                File::delete(public_path('uploads/categories').'/'.$category->image);
        }
        $category->delete();

        return redirect()->route('admin.categories')->with('status','Category has been deleted successfully');
    
    }

///////////// THE PRODUCT FUNCTIONS //////////////////////////////////////////////////

    public function products(){
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);

        return view('admin.products', compact('products'));
    }

    public function new_product(){
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
    return view('admin.new_product', compact('categories','brands'));
    }

    public function create_product(Request $request){

            // echo($request->short_description);
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
        'image' => 'required|mimes:png,jpg,jpeg | max:2048',
        'category_id' => 'required',
        'brand_id' => 'required',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_desc = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = Carbon::now()->timestamp.'.'.$image->extension();
            $this->ProductThumbnails($image, $imageName);
            $product->image = $imageName;
        }

        $gallery = array();
        $gallery_images = "";
        $counter = 1;

        if($request->hasFile('images')){
            $allowedExtension = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach($files as $file){
                $iextension = $file->getClientOriginalExtension();
                $icheck = in_array($iextension, $allowedExtension);
                if($icheck){
                    $ifileName = Carbon::now()->timestamp."-".$counter.".".$iextension;
                    $this->ProductThumbnails($file, $ifileName);
                    array_push($gallery, $ifileName);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',',$gallery);
        }

        $product->images = $gallery_images;
        $product->save();

        return redirect()->route('admin.products')->with('status', 'Product has been created successifully');
    }

    public function ProductThumbnails($image, $imageName){
    $destinationPathThumbnail = public_path('uploads/products/thumbnails');
    $destinationPath = public_path('uploads/products');
    $img = Image::read($image->getRealPath());

    $img->cover(540,689,"top");

    $img->resize(540,689, function($constraint){
        $constraint->aspectRatio();
    })->save($destinationPath.'/'.$imageName);
 
    $img->resize(124,124, function($constraint){
        $constraint->aspectRatio();
    })->save($destinationPathThumbnail.'/'.$imageName);
    }

    public function edit_product($id){
        $product = Product::find($id);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        
        return view('admin.edit_product', compact('product', 'categories', 'brands'));
    }

    public function update_product(Request $request){
        $request -> validate([
        'name' => 'required',
        'slug' => 'required|unique:products,slug,'.$request->id,
        'short_description' => 'required',
        'description' => 'required',
        'regular_price' => 'required',
        'sale_price' => 'required',
        'SKU' => 'required',
        'stock_status' => 'required',
        'featured' => 'required',
        'quantity' => 'required',
        'image' => 'mimes:png,jpg,jpeg | max:2048',
        'category_id' => 'required',
        'brand_id' => 'required', 
        ]);

        $product = Product::find($request->id);

        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_desc = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/products').'/'.$product->image)){
                File::delete(public_path('uploads/products').'/'.$product->image);
            }
            if(File::exists(public_path('uploads/products/thumbnails').'/'.$product->image)){
                File::delete(public_path('uploads/products/thumbnails').'/'.$product->image);
            }            
            $image = $request->file('image');
            $imageName = Carbon::now()->timestamp.'.'.$image->extension();
            $this->ProductThumbnails($image, $imageName);
            $product->image = $imageName;
        }

        $gallery = array();
        $gallery_images = "";
        $counter = 1;

        if($request->hasFile('images')){

            foreach(explode(',', $product->images) as $imfile ){
                if(File::exists(public_path('uploads/products').'/'.$imfile)){
                    File::delete(public_path('uploads/products').'/'.$imfile);
                }
                if(File::exists(public_path('uploads/products/thumbnails').'/'.$imfile)){
                    File::delete(public_path('uploads/products/thumbnails').'/'.$imfile);
                }                  
            }

            $allowedExtension = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach($files as $file){
                $iextension = $file->getClientOriginalExtension();
                $icheck = in_array($iextension, $allowedExtension);
                if($icheck){
                    $ifileName = Carbon::now()->timestamp."-".$counter.".".$iextension;
                    $this->ProductThumbnails($file, $ifileName);
                    array_push($gallery, $ifileName);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',',$gallery);
        $product->images = $gallery_images;

        }

        $product->save();

        return redirect()->route('admin.products')->with('status', 'Product has been updated successifully');
    }

    public function delete_product($id){
        $product = Product::find($id);
    }

}
