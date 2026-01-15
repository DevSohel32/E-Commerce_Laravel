@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Coupon infomation</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <a href="{{ route('admin.coupons.index') }}">
                            <div class="text-tiny">Coupons</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">New Coupon</div>
                    </li>
                </ul>
            </div>
            <div class="wg-box">
                <form class="form-new-product form-style-1" method="POST" action="{{ route('admin.coupon.update') }}">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" value="{{ $coupon->id }}" />
                    <fieldset class="name">
                        <div class="body-title">Coupon Code <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Coupon Code" name="code"
                            value="{{ old('code', $coupon->code) }}" required="">
                        @error('code')
                            <span class="text-danger mt-2">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="category">
                        <div class="body-title">Coupon Type</div>
                        <div class="select flex-grow">
                            <select name="type">
                                <option value="fixed" @selected(old('type', $coupon->type) == 'fixed')>Fixed</option>
                                <option value="percent" @selected(old('type', $coupon->type) == 'percent')>Percent</option>
                            </select>
                        </div>
                        @error('type')
                            <span class="text-danger mt-2">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title">Value <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Coupon Value" name="value"
                            value="{{ old('value', $coupon->value) }}" required="">
                        @error('value')
                            <span class="text-danger mt-2">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title">Cart Value <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Cart Value" name="cart_value"
                            value="{{ old('cart_value', $coupon->cart_value) }}" required="">
                        @error('cart_value')
                            <span class="text-danger mt-2">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="name">
                        <div class="body-title">Expiry Date <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="date" name="expiry_date"
                            value="{{ old('expiry_date', $coupon->expiry) }}" required="">
                        @error('expiry_date')
                            <span class="text-danger mt-2">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <div class="bot">
                        <div></div>
                        <button class="tf-button w208" type="submit">Update Coupon</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
