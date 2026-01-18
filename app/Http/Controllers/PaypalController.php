<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{
    public function payWithPaypal($order_id)
    {
        $order = Order::findOrFail($order_id);

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

        // Create PayPal Order
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('user.paypal.success', ['order_id' => $order->id]),
                "cancel_url" => route('user.paypal.cancel'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "GBP",
                        "value" => number_format($order->total, 2, '.', '')
                    ]
                ]
            ]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }
        }

        return redirect()->route('cart.index')->with('error', 'Something went wrong with the payment gateway.');
    }

    public function paypalSuccess(Request $request, $order_id)
    {
        $user_id = Auth::user()->id;
        $provider = new \Srmklive\PayPal\Services\PayPal;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {

            // Save Transaction
            $transaction = new \App\Models\Transaction();
            $transaction->user_id = $user_id;
            $transaction->order_id = $order_id;
            $transaction->mode = 'paypal';
            $transaction->status = "approved";
            $transaction->save();

            // Clear Cart and Sessions
            Cart::instance('cart')->destroy();
            session()->forget(['coupon', 'checkout', 'discounts']);
            session()->put('order_id', $order_id);

            return redirect()->route('cart.order.confirmation')->with('success', 'Payment was successful!');
        }

        return redirect()->route('cart.index')->with('error', 'Payment was cancelled or failed.');
    }
}
