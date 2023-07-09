<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Catagory;
use PDF;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function view_catagory() {

        $data = Catagory::all();

        return view('admin.catagory', compact('data'));
    }

    public function add_catagory(Request $request) {

        $request->validate([
            'catagory'=>'required'
        ]);
        
        $catagory = Catagory::create([
            'catagory_name' => $request->input('catagory')
        ]);

        return redirect()->back()->with('message', 'Catagory Added Successfully!');
    }

    public function delete_catagory($id) {

        if(auth()->user()->usertype==0){
            return redirect('/')->with('message', 'We will find you, and it won\'t end well for you.');
        }

        if(is_null(Catagory::find($id))) {return redirect()->back()->with(['message' =>'Catagory Doesn\'t Exist!', 'type' => 'bad']);}

        Catagory::find($id)?->delete();

        return redirect()->back()->with(['message' => 'Catagory Deleted Successfully!', 'type' => 'good']);
    }

    public function view_product() {
        
        if(auth()->user()->usertype==1) return view('admin.product')->with('data', Catagory::all());
        return redirect()->back();
    }

    public function add_product(Request $request) {
        if(auth()->user()->usertype==1){
            $request->validate([
                'title'=>'required',
                'description'=>'required',
                'catagory'=>'required',
                'quantity'=>'required',
                'price'=>'required',
                'image'=>'required|mimes:png,jpg,jpeg|max:5048'
            ]);

            $newImageName = time() . '.' . $request->image->extension();
            $request->image->move('product', $newImageName);

            $product = Product::create([
                'title' => $request->input('title'),
                'description'=>$request->input('description'),
                'image'=>$newImageName,
                'catagory'=>$request->input('catagory'),
                'quantity'=>$request->input('quantity'),
                'prize'=>$request->input('price'),
                'discount_price'=>$request->input('dis_price')
            ]);
            

            return redirect()->back()->with(['message'=>'Product Added Successfully!', 'type'=>'good']);
        }else{
            return redirect()->back()->with(['message'=>'You need to be an admin, wtf are you doing here anyway!', 'type'=>'bad']);
        }
    }

    public function show_product() {

        $products=Product::all();
        return view('admin.show_product')->with('products', $products);
    }

    public function delete_product($id) {
        $product = Product::find($id);

        if($product==null) return redirect()->back()->with(['message'=>'Product Not Found!', 'type'=>'bad']);
        $product->delete();

        return redirect()->back();
    }

    public function update_product($id) {
        $product = Product::find($id);

        if($product==null) return redirect()->back()->with(['message'=>'Product Not Found!', 'type'=>'bad']);
        
        return view('admin.update_product')->with(['data' => Product::find($id), 'catagory'=> Catagory::all()]);
    }

    public function update_product_confirm($id, Request $request) {
        $product = Product::find($id);

        $request->validate([
            'title'=>'required',
            'description'=>'required',
            'catagory'=>'required',
            'quantity'=>'required',
            'price'=>'required',
            'image'=>'mimes:png,jpg,jpeg|max:5048'
        ]);

        if($request->image!=null){
            $newImageName = time() . '.' . $request->image->extension();
            $request->image->move('product', $newImageName);
        }

        // $product->update($formFields);

        $product->title = $request->input('title');
        $product->description = $request->input('description');
        $product->image = $request->image==null?$product->image:$newImageName;
        $product->catagory = $request->input('catagory');
        $product->quantity = $request->input('quantity');
        $product->prize = $request->input('price');
        $product->discount_price = $request->input('dis_price');

        $product->save();

        return redirect('show_product')->with(['message'=>'Listing updated successfuly!', 'products'=>Product::all()]);
    }

    public function order() {
        return view('admin.order')->with(['orders'=>Order::all()]);
    }

    public function delivered($id) {
        $order = Order::find($id);

        $order->delivery_status = 'delivered';
        $order->payment_status = 'Paid';
        $order->save();

        return redirect()->back();
    }

    public function print_pdf($id) {
        
        $order = Order::find($id);
        
        $pdf = PDF::loadView('admin.pdf',compact('order'));

        return $pdf->download('order_details.pdf');
    }
}
