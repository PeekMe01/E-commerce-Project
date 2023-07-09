<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    @include('admin.css')

    <style style="text/css">
        .div_center{
            text-align: center;
            padding-top: 40px;
        }

        .h2_font{
            font-size: 40px;
            padding-bottom: 40px;
        }
        
        .input_color{
            color: black;
        }

        .form_style{
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: center;
        }
        
        .center{
            margin: auto;
            width: 50%;
            text-align: center;
            margin-top: 30px;
            color: white;
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
                    <h2 class="h2_font">Add Catagory</h2>

                    <form action="{{ url('/add_catagory') }}" method="POST" class="form_style">
                        @csrf
                        <input class="input_color" type="text" name='catagory' placeholder="Write catagory name">
                        <input type="submit" class="btn btn-primary" name='submit' value="Add Catagory">
                        @if ($errors->any())
                            @foreach ($errors->all() as $error) 
                                    <h2 style="color:red">{{ $error }}</h2>
                            @endforeach
                        @endif
                    </form>
                </div>

                <table class="center table table-striped table-bordered">
                    <tr>
                        <td>Catagory Name</td>
                        <td>Action</td>

                        @foreach ($data as $item)
                        <tr>
                            <td>{{ $item->catagory_name }}</td>
                            <td><a onclick="return confirm('Are you sure you want to delete {{ $item->catagory_name }}')" class="btn btn-danger" href="{{ url('delete_catagory', $item->id) }}">Delete</a></td>
                        </tr>
                        @endforeach
                    </tr>
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