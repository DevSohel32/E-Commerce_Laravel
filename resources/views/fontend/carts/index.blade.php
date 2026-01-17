@extends('layouts.app')
@section('content')
    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="shop-checkout container">
            <h2 class="page-title">Cart</h2>
            <div class="checkout-steps">
                <a href="javascript:void(0)" class="checkout-steps__item active">
                    <span class="checkout-steps__item-number">01</span>
                    <span class="checkout-steps__item-title">
                        <span>Shopping Bag</span>
                        <em>Manage Your Items List</em>
                    </span>
                </a>
                <a href="javascript:void(0)" class="checkout-steps__item">
                    <span class="checkout-steps__item-number">02</span>
                    <span class="checkout-steps__item-title">
                        <span>Shipping and Checkout</span>
                        <em>Checkout Your Items List</em>
                    </span>
                </a>
                <a href="javascript:void(0)" class="checkout-steps__item">
                    <span class="checkout-steps__item-number">03</span>
                    <span class="checkout-steps__item-title">
                        <span>Confirmation</span>
                        <em>Review And Submit Your Order</em>
                    </span>
                </a>
            </div>
            <div class="shopping-cart">

                @if ($items->count() > 0)
                    <div class="cart-table__wrapper">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th></th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td>
                                            <div class="shopping-cart__product-item">
                                                <img loading="lazy"
                                                    src="{{ asset('uploads/products') }}/{{ $item->model->image }}"
                                                    width="120" height="120" alt="{{ $item->name }}" />
                                            </div>
                                        </td>
                                        <td>
                                            <div class="shopping-cart__product-item__detail">
                                                <h4>{{ $item->name }}</h4>
                                                <ul class="shopping-cart__product-item__options">
                                                    <li>Color: Yellow</li>
                                                    <li>Size: L</li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="shopping-cart__product-price">$ {{ $item->price }}</span>
                                        </td>
                                        <td>
                                            <div class="qty-control position-relative">
                                                <input type="number" name="quantity" value="{{ $item->qty }}"
                                                    min="1" class="qty-control__number text-center">
                                                <form method="POST"
                                                    action="{{ route('cart.qty.decrease', ['rowId' => $item->rowId]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="qty-control__reduce"
                                                        onclick="this.closest('form').submit();">-
                                                    </div>
                                                </form>

                                                <form method="POST"
                                                    action="{{ route('cart.qty.increase', ['rowId' => $item->rowId]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="qty-control__increase"
                                                        onclick="this.closest('form').submit();">+
                                                    </div>
                                                </form>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="shopping-cart__subtotal">${{ $item->subTotal() }}</span>
                                        </td>
                                        <td>

                                            <form method="POST"
                                                action="{{ route('cart.item.remove', ['rowId' => $item->rowId]) }}"
                                                id="remove-form-{{ $item->rowId }}">
                                                @csrf
                                                @method('DELETE')
                                                <a href="javascript:void(0)" class="remove-cart"
                                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="#767676"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M0.259435 8.85506L9.11449 0L10 0.885506L1.14494 9.74056L0.259435 8.85506Z" />
                                                        <path
                                                            d="M0.885506 0.0889838L9.74057 8.94404L8.85506 9.82955L0 0.97449L0.885506 0.0889838Z" />
                                                    </svg>
                                                </a>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="cart-table-footer">
                            <form action="{{ route('cart.apply.coupon') }}" method="post"
                                class="position-relative bg-body">
                                @csrf
                                <input class="form-control" type="text" name="coupon_code" placeholder="Coupon Code"
                                    {{ Session::has('coupon') ? 'readonly' : '' }}>

                                @if (Session::has('coupon'))
                                    <div
                                        class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4 d-flex align-items-center bg-light">
                                        <span class="text-success me-2">{{ Session::get('coupon')['code'] }} Applied</span>
                                        <a href="{{ route('cart.remove.coupon') }}" class="text-danger small">Remove</a>
                                    </div>
                                @else
                                    <input class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4"
                                        type="submit" value="APPLY COUPON">
                                @endif
                            </form>

                            <form method="post" action="{{ route('cart.item.empty') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light">UPDATE CART</button>
                            </form>
                        </div>
                    </div>
                    <div class="shopping-cart__totals-wrapper">
                        <div class="sticky-content">
                            <div class="shopping-cart__totals">
                                <h3>Cart Totals</h3>
                                <table class="cart-totals">
                                    <tbody>
                                        @if (Session::has('discounts'))
                                            <tr>
                                                <th>Subtotal</th>
                                                <td>${{ Cart::instance('cart')->subtotal() }}</td>
                                            </tr>
                                            <tr>
                                                <th>Discount ({{ Session::get('coupon')['code'] }})</th>
                                                <td class="text-success">-${{ Session::get('discounts')['discount'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Subtotal after Discount</th>
                                                <td>${{ Session::get('discounts')['subtotal'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Shipping</th>
                                                <td>Free</td>
                                            </tr>
                                            <tr>
                                                <th>VAT</th>
                                                <td>${{ Session::get('discounts')['tax'] }}</td>
                                            </tr>
                                            <tr class="cart-total-row">
                                                <th>Total</th>
                                                <td class="h4 fw-bold">${{ Session::get('discounts')['total'] }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <th>Subtotal</th>
                                                <td>${{ Cart::instance('cart')->subtotal() }}</td>
                                            </tr>
                                            <tr>
                                                <th>Shipping</th>
                                                <td>Free</td>
                                            </tr>
                                            <tr>
                                                <th>VAT</th>
                                                <td>${{ Cart::instance('cart')->tax() }}</td>
                                            </tr>
                                            <tr>
                                                <th>Total</th>
                                                <td class="h4 fw-bold">${{ Cart::instance('cart')->total() }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="mobile_fixed-btn_wrapper">
                                <div class="button-wrapper container">
                                    <a href="{{ route('cart.checkout.index') }}"
                                        class="btn btn-primary btn-checkout">PROCEED TO CHECKOUT</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-12 text-center pt-5 pb-5">
                            <p class="mb-4">No items found in your cart.</p>
                            <a href="{{ route('home.index') }}" class="btn btn-info">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </main>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            // Note the () after function
            $('.qty-control__reduce').on('click', function() {
                $(this).closest('form').submit();
            });
            $('.qty-control__increase').on('click', function() {
                $(this).closest('form').submit();
            });
            $('.remove-cart').on('click', function() {
                $(this).closest('form').submit();
            });
        });
    </script>
@endpush
