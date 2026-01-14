@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>All Products</h3>
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
                        <div class="text-tiny">All Products</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <form class="form-search">
                            <fieldset class="name">
                                <input type="text" placeholder="Search here..." class="" name="name"
                                    tabindex="2" value="" aria-required="true" required="">
                            </fieldset>
                            <div class="button-submit">
                                <button class="" type="submit"><i class="icon-search"></i></button>
                            </div>
                        </form>
                    </div>
                    <a class="tf-button style-1 w208" href="{{ route('admin.product.create') }}"><i
                            class="icon-plus"></i>Add new</a>
                </div>
                <div class="table-responsive">
                    @push('scripts')
                        @if (Session::has('status'))
                            <script>
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: "{{ Session::get('status') }}",
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    width: 'auto',
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer)
                                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                                    },
                                    customClass: {
                                        title: 'swal-title-20px',
                                        popup: 'swal-popup-20px',
                                        icon: 'swal-icon-20px'
                                    }
                                });
                            </script>

                            <style>
                                .swal2-container.swal2-top-end>.swal2-popup {
                                    margin-top: 20px !important;
                                    margin-right: 20px !important;
                                }

                                .swal-popup-20px {
                                    padding: 12px 25px !important;
                                    display: flex !important;
                                    align-items: center !important;
                                    justify-content: center !important;
                                    background: #fff !important;
                                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
                                    border-radius: 10px !important;
                                }

                                .swal-title-20px {
                                    font-size: 18px !important;
                                    font-weight: 500 !important;
                                    color: #333 !important;
                                    margin: 0 0 0 12px !important;
                                    padding: 0 !important;
                                    white-space: nowrap;
                                }

                                .swal-icon-20px {
                                    width: 20px !important;
                                    height: 20px !important;
                                    margin: 0 !important;
                                    border: 2px solid currentColor !important;
                                }

                                .swal2-icon.swal2-success.swal-icon-20px [class^=swal2-success-line] {
                                    height: 3px !important;
                                }

                                .swal2-icon.swal2-success.swal-icon-20px .swal2-success-line-tip {
                                    width: 6px !important;
                                    left: 3px !important;
                                    top: 11px !important;
                                }

                                .swal2-icon.swal2-success.swal-icon-20px .swal2-success-line-long {
                                    width: 12px !important;
                                    right: 3px !important;
                                    top: 9px !important;
                                }

                                .swal2-icon.swal2-success.swal-icon-20px .swal2-success-ring {
                                    width: 20px !important;
                                    height: 20px !important;
                                }
                            </style>
                        @endif
                    @endpush
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>SalePrice</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Featured</th>
                                <th>Stock</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="pname">
                                        <div class="image">
                                            <img src="{{ asset('uploads/products/') }}/{{ $product->image }}"
                                                alt="{{ $product->name }}" class="image">
                                        </div>
                                        <div class="name">
                                            <a href="#" class="body-title-2">{{ $product->name }}</a>
                                            <div class="text-tiny mt-3">{{ $product->slug }}</div>
                                        </div>
                                    </td>
                                    <td>${{ $product->regular_price }}</td>
                                    <td>${{ $product->sale_price }}</td>
                                    <td>{{ $product->SKU }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->brand->name }}</td>
                                    <td>{{ $product->featured == 0 ? 'No' : 'Yes' }}</td>
                                    <td>{{ $product->stock_status }}</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td>
                                        <div class="list-icon-function">
                                            <a href="#" target="_blank">
                                                <div class="item eye">
                                                    <i class="icon-eye"></i>
                                                </div>
                                            </a>
                                            <a href="{{ route('admin.product.edit', ['id' => $product->id]) }}">
                                                <div class="item edit">
                                                    <i class="icon-edit-3"></i>
                                                </div>
                                            </a>
                                            <form action="{{ route('admin.product.delete', ['id' => $product->id]) }}"
                                                method="POST">
                                                <div class="item text-danger delete">
                                                    <i class="icon-trash-2"></i>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">


                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(function() {
            $('.delete').on('click', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');

                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this record!",
                    icon: "warning",
                    buttons: ["Cancel", "Yes, delete it!"],
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
