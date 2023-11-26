<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    @include('admin.css')

    <style>
        .title_deg{
            text-align: center;
            font-size: 25px;
            font-weight: bold;
            padding-bottom: 50px;
        }
        .center{
            margin: auto;
            text-align: center;
            width: 100%;
        }
        .img_size{
            min-width: 150px;
            max-width: 150px;
        }
        table tr td, table tr th{
            padding:10px;
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
                <h1 class="title_deg">All Orders</h1>

                <div style="text-align: center; padding-bottom: 30px">

                    <form action="{{ url('search') }}" method="get">
                        @csrf

                        <input style="color:black" type="text" name='search' placeholder="Search...">
                        <input type="submit" value="Search" class="btn btn-primary">

                    </form>

                </div>

                <table class="table-dashed table-striped table-bordered center text-light">
                    <tr class="table-success">
                        <th>Name</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Product Title</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Payment Status</th>
                        <th>Delivery Status</th>
                        <th>Image</th>
                        <th>Delivered</th>
                        <th>Print PDF</th>
                        <th>Send Email</th>
                    </tr>

                    @forelse ($orders as $order)
                    <tr>
                        <td>{{ $order->name }}</td>
                        <td>{{ $order->email }}</td>
                        <td>{{ $order->address }}</td>
                        <td>{{ $order->phone }}</td>
                        <td>{{ $order->product_title }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ $order->price }}</td>
                        <td>{{ $order->payment_status }}</td>
                        <td>{{ $order->delivery_status }}</td>
                        <td><img class="img_size" src='/product/{{ $order->image }}'></td>
                        <td>
                            @if ($order->delivery_status=='delivered')
                                <p style="color:green">Delivered</p>
                            @else
                                <a href="{{ url('delivered', $order->id) }}" class="btn btn-primary">Delivered</a>
                            @endif
                        </td>
                        <td><a class="btn btn-secondary" href="{{ url('print_pdf', $order->id) }}">Print PDF</a></td>
                        <td><a href="{{ url('send_email', $order->id) }}" class="btn btn-info">Send Email</a></td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan = "16">No Data Found</td>
                    </tr>

                    @endforelse
                </table>
            </div>
        </div>

          <!-- partial -->

        <!-- main-panel ends -->

      <!-- page-body-wrapper ends -->

    <!-- container-scroller -->
    <!-- plugins:js -->
    @include('admin.script')
    <!-- End custom js for this page -->
  </body>
</html>