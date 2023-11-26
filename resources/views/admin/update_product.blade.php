<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    @include('admin.css')
    <style type="text/css">
        .div_center{
            text-align: center;
            padding-top: 40px; 
        }

        .font_size{
            font-size: 40px;
            padding-bottom: 40px;
        }

        .input_color{
            color: black;
            padding-bottom: 20px;
            width: 250px;
        }

        label{
            display: inline-block;
            width: 250px;
            text-align: left;
        }
        .div_design{
            padding-bottom: 15px;
        }
        .product_img{
            width: 250px;
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
                <div class="div_center">
                    <h1 class="font_size">Update Product</h1>

                    <form action="{{ url('/update_product_confirm', $data->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="div_design">
                            <label for="title"> Product Title:</label>
                            <input required class="input_color" type="text" name="title" placeholder="Write a title" value="{{ $data->title }}">
                        </div>
                        <div class="div_design">
                            <label for="description"> Product Description:</label>
                            <input required class="input_color" type="text" name="description" placeholder="Write a description" value="{{ $data->description }}">
                        </div>
                        <div class="div_design">
                            <label for="price"> Product Price:</label>
                            <input required class="input_color" type="number" name="price" placeholder="Write a price" value="{{ $data->prize }}">
                        </div>
                        <div class="div_design">
                            <label for="discount"> Discount Price:</label>
                            <input class="input_color" type="text" name="dis_price" placeholder="Write a discount if applied" value="{{ $data->discount_price }}">
                        </div>
                        <div class="div_design">
                            <label for="quantity"> Product Quantity:</label>
                            <input required class="input_color" type="number" name="quantity" min="0" placeholder="Write a quantity" value="{{ $data->quantity }}">
                        </div>
                        <div class="div_design">
                            <label for="catagory"> Product Catagory:</label>
                            <select required name="catagory" id="" class="input_color">
                                <option value="{{ $data->catagory }}" selected>{{ $data->catagory }}</option>
                                @foreach ($catagory as $item)
                                    <option value="{{ $item->catagory_name }}">{{ $item->catagory_name }}</option>   
                                @endforeach
                            </select>
                        </div>
                        <div class="div_design">
                            <label for="image">Current Product Image:</label>
                            <img height="100" width="100" style="margin: auto" src="/product/{{ $data->image }}" alt="">
                        </div>
                        <div class="div_design">
                            <label for="image">Change Product Image:</label>
                            <input class="product_img" type="file" name="image">
                        </div>

                        <div class="div_design">
                            <input type="submit" value="Update Product" class="btn btn-primary">
                        </div>
                        @if ($errors->any())
                            @foreach ($errors->all() as $error) 
                                    <h2 style="color:red">{{ $error }}</h2>
                            @endforeach
                        @endif
                    </form>

                </div>
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