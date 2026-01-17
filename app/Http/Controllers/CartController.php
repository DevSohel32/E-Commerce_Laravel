<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Coupon;
use App\Models\Address;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;



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
    public function increase_cart_quantity($rowId)
    {

        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }
    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }
    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function apply_coupon_code(Request $request)
    {
        $coupon_code = $request->coupon_code;

        if ($coupon_code) {
            // 1. Added ->first() to execute the query
            $coupon = Coupon::where('code', $coupon_code)
                ->where('expiry_date', '>=', Carbon::today())
                ->where('cart_value', '<=', (float)Cart::instance('cart')->subtotal())
                ->first();

            if (!$coupon) {
                return redirect()->back()->with('error', 'Invalid or expired coupon code');
            } else {
                // 2. Used => for associative arrays
                Session::put('coupon', [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'cart_value' => $coupon->cart_value
                ]);

                $this->calculateDiscount();
                return redirect()->back()->with('success', 'Coupon applied!');
            }
        } else {
            return redirect()->back()->with('error', 'Please enter a coupon code');
        }
    }
    public function remove_coupon()
    {
        Session::forget('coupon');
        Session::forget('discounts');

        return redirect()->back()->with('success', 'Coupon has been removed.');
    }
    public function calculateDiscount()
    {
        if (Session::has('coupon')) {
            $coupon = Session::get('coupon');
            $subtotal = (float)Cart::instance('cart')->subtotal();
            $discount = 0;

            // Logic for Fixed vs Percentage
            if ($coupon['type'] == 'fixed') {
                $discount = $coupon['value'];
            } else {
                $discount = ($subtotal * $coupon['value']) / 100;
            }

            $discountAfterSubtotal = $subtotal - $discount;
            $taxRate = config('cart.tax');
            $discountAfterTax = ($discountAfterSubtotal * $taxRate) / 100;
            $totalAfterDiscount = $discountAfterSubtotal + $discountAfterTax;

            // 3. Updated to store the actual individual values
            Session::put('discounts', [
                'discount' => number_format($discount, 2, '.', ''),
                'tax'      => number_format($discountAfterTax, 2, '.', ''),
                'subtotal' => number_format($discountAfterSubtotal, 2, '.', ''),
                'total'    => number_format($totalAfterDiscount, 2, '.', ''),
            ]);
        }
    }

    public function checkout()
    {
        if (Auth::check()) {
            $address = Address::where('user_id', Auth::user()->id)->where('isdefault', 1)->first();
            return view('fontend.checkouts.index', compact('address'));
        } else {
            return redirect()->route('login');
        }
    }


    public function place_an_order(Request $request)
    {
        $user_id = Auth::user()->id;
        $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();

        if (!$address) {
            $request->validate([
                'name' => 'required|max:100',
                'phone' => 'required|numeric|digits:10',
                'zip' => 'required|numeric|digits:5',
                'state' => 'required',
                'city' => 'required',
                'address' => 'required',
                'locality' => 'required',
                'landmark' => 'required',
            ]);

            $address = new Address();
            $address->user_id  = $user_id;
            $address->name     = $request->name;
            $address->phone    = $request->phone;
            $address->locality = $request->locality;
            $address->address  = $request->address;
            $address->city     = $request->city;
            $address->state    = $request->state;
            $address->country  = 'Bangladesh';
            $address->landmark = $request->landmark;
            $address->zip      = $request->zip;
            $address->type     = 'home';
            $address->isdefault = true;
            $address->save();
        }

        $this->setAmountForCheckout();

        if (!Session::has('checkout')) {
            return redirect()->route('cart.index')->with('error', 'Checkout session expired.');
        }

        $order = new Order();
        $order->user_id = $user_id;
        $order->subtotal = Session::get('checkout')['subtotal'];
        $order->discount = Session::get('checkout')['discount'];
        $order->tax = Session::get('checkout')['tax'];
        $order->total = Session::get('checkout')['total'];
        $order->name = $address->name;
        $order->phone = $address->phone;
        $order->locality = $address->locality;
        $order->address = $address->address;
        $order->city = $address->city;
        $order->state = $address->state;
        $order->country = $address->country;
        $order->landmark = $address->landmark;
        $order->zip = $address->zip;
        $order->save();
        foreach (Cart::instance('cart')->content() as $item) {
            $orderItem = new OrderItem();
            $orderItem->product_id = $item->id;
            $orderItem->order_id = $order->id;
            $orderItem->price = $item->price;
            $orderItem->quantity = $item->qty;
            $orderItem->save();
        }

        // ট্রানজ্যাকশন সেভ করা
        if ($request->mode == 'cod') {
            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->order_id = $order->id;
            $transaction->mode = $request->mode;
            $transaction->status = "pending";
            $transaction->save();
        }
        // googlepay বা paypal এর লজিক এখানে আসবে...


        Cart::instance('cart')->destroy();
        Session::forget('coupon');
        Session::forget('checkout');
        Session::forget('discounts');
        Session::put('order_id', $order->id);

        return redirect()->route('cart.order.confirmation');
    }

    public function setAmountForCheckout()
    {
        if (Cart::instance('cart')->content()->count() == 0) {
            Session::forget('checkout');
            return;
        }

        if (Session::has('coupon')) {
            $discounts = Session::get('discounts');
            Session::put('checkout', [
                'discount' => (float) str_replace(',', '', $discounts['discount']),
                'subtotal' => (float) str_replace(',', '', $discounts['subtotal']),
                'tax'      => (float) str_replace(',', '', $discounts['tax']),
                'total'    => (float) str_replace(',', '', $discounts['total']),
            ]);
        } else {
            Session::put('checkout', [
                'discount' => 0,
                'subtotal' => (float) str_replace(',', '', Cart::instance('cart')->subtotal()),
                'tax'      => (float) str_replace(',', '', Cart::instance('cart')->tax()),
                'total'    => (float) str_replace(',', '', Cart::instance('cart')->total()),
            ]);
        }
    }

   public function orderConfirmation()
{
    if (Session::has('order_id')) {
        // Eager load the orderItems relationship
        $order = Order::with('orderItems')->find(Session::get('order_id'));

        if ($order) {
            return view('fontend.checkouts.confirmation', compact('order'));
        }
    }
    return redirect()->route('cart.index')->with('error', 'Order not found.');
}
}
