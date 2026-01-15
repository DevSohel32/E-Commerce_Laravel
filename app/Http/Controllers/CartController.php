<?php

namespace App\Http\Controllers;


use Surfsidemedia\Shoppingcart\Facades\Cart;
use App\Models\Product;
use Illuminate\Http\Request;



class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('fontend.carts.index', compact('items'));
    }




    public function add_to_cart(Request $request)
    {

        $product = Product::findOrFail($request->id);
      if ($product->stock_status === 'outofstock' || $product->quantity < $request->quantity) {
        return redirect()->back()->with('error', 'Insufficient stock available!');
    }
    $price = $product->sale_price ? $product->sale_price : $product->regular_price;
        Cart::instance('cart')->add(
            $product->id,
            $product->name,
            $request->quantity,
            $price
        )->associate('App\Models\Product');
        return redirect()->back()->with('status', 'Item added to cart!');
    }


    public function increase_cart_quantity($rowId){

        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty+1;
        Cart::instance('cart')->update($rowId,$qty);
        return redirect()->back();
    }

        public function decrease_cart_quantity($rowId){
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty-1;
        Cart::instance('cart')->update($rowId,$qty);
        return redirect()->back();

        }
        public function remove_item($rowId){
            Cart::instance('cart')->remove( $rowId );
            return redirect()->back();
        }
}
