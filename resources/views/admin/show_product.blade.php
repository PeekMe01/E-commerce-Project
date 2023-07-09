<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <base href="/public">
    @include('admin.css')
    <style>
        .center{
            margin: auto;
            width: 50%;
            text-align: center;
            margin-top: 30px;
            color: white;
            border: 2px white solid;
        }
        .center td, .center tr, .center th{
            border: 1px white solid;
        }
        .font_size{
            text-align: center;
            font-size: 40px;
            padding-top: 20px;
        }
        .img_size{
            min-width: 150px;
        }
        .th_color{
            background: skyblue;
        }
        .th_deg{
            padding: 30px;
        }
    </style>
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_sidebar.html -->
      @include('admin.sidebar')
      <!-- partial -->
      @include('admin.header')
        <!-- partial -->
        <div class="main-panel">
            <div class="content-wrapper">
                @if (session()->has('message'))
                    <div class="alert alert-dismissable warning_message {{ session()->get('type')=='bad'? 'alert-danger':'alert-success' }}">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                        {{ session()->get('message') }}
                    </div>
                @endif
                <h2 class="font_size">All Products</h2>

                <table class="center">
                    <tr class="th_color">
                        <th class="th_deg">Product Title</th>
                        <th class="th_deg">Description</th>
                        <th class="th_deg">Quantity</th>
                        <th class="th_deg">Catagory</th>
                        <th class="th_deg">Price</th>
                        <th class="th_deg">Discount Price</th>
                        <th class="th_deg">Product Image</th>
                        <th class="th_deg">Delete</th>
                        <th class="th_deg">Edit</th>
                    </tr>

                    @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->title }}</td>
                        <td>{{ $product->description }}</td>
                        <td>{{ $product->quantity }}</td>
                        <td>{{ $product->catagory }}</td>
                        <td>{{ $product->prize }}</td>
                        <td>{{ $product->discount_price }}</td>
                        <td><img class="img_size" src='/product/{{ $product->image }}' alt="product image"></td>
                        <td><a onclick="return confirm('Are you sure you want to delete {{ $product->title }}')" class="btn btn-danger" href="{{ url('delete_product', $product->id) }}">Delete</a></td>
                        <td><a class="btn btn-success" href="{{ url('update_product', $product->id) }}">Edit</a></td>
                    </tr>
                    @endforeach

                </table>

            </div>
        </div>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    @include('admin.script')
    <!-- End custom js for this page -->
  </body>
</html>