<?php

namespace App\Http\Controllers;

use PDF;
use Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\Catagory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SendEmailNotification;

class AdminController extends Controller
{
    public function view_catagory() {

        if(Auth::id() && (Auth::User()->usertype == 1)){
            $data = Catagory::all();

            return view('admin.catagory', compact('data'));
        }else{
            return redirect('login');
        }  
    }

    public function add_catagory(Request $request) {

        if(Auth::id() && (Auth::User()->usertype == 1)){

            $request->validate([
                'catagory'=>'required'
            ]);
            
            $catagory = Catagory::create([
                'catagory_name' => $request->input('catagory')
            ]);

            return redirect()->back()->with('message', 'Catagory Added Successfully!');
        }else{
            return redirect('login');
        }
    }

    // 2
    public function delete_catagory($id) {

        if(auth()->user()->usertype==0){
            return redirect('/')->with('message', 'We will find you, and it won\'t end well for you.');
        }

        if(is_null(Catagory::find($id))) {return redirect()->back()->with(['message' =>'Catagory Doesn\'t Exist!', 'type' => 'bad']);}

        Catagory::find($id)?->delete();

        return redirect()->back()->with(['message' => 'Catagory Deleted Successfully!', 'type' => 'good']);
    }

    public function view_product() {

        if(Auth::id() && (Auth::User()->usertype == 1)){  
        
            if(auth()->user()->usertype==1) return view('admin.product')->with('data', Catagory::all());
            return redirect()->back();

        }else{
            return redirect('login');
        }
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
        if(Auth::id() && (Auth::User()->usertype == 1)){
            $products=Product::all();
            return view('admin.show_product')->with('products', $products);
        }else{
            return redirect('login');
        }
    }

    public function delete_product($id) {
        if(Auth::id() && (Auth::User()->usertype == 1)){
            $product = Product::find($id);

            if($product==null) return redirect()->back()->with(['message'=>'Product Not Found!', 'type'=>'bad']);
            $product->delete();

            return redirect()->back();
        }else{
            return redirect('login');
        }
    }

    public function update_product($id) {
        if(Auth::id() && (Auth::User()->usertype == 1)){
            $product = Product::find($id);

            if($product==null) return redirect()->back()->with(['message'=>'Product Not Found!', 'type'=>'bad']);
            
            return view('admin.update_product')->with(['data' => Product::find($id), 'catagory'=> Catagory::all()]);
        }else{
            return redirect('login');
        }
    }

    public function update_product_confirm($id, Request $request) {
        if(Auth::id() && (Auth::User()->usertype == 1)){
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

        }else{
            return redirect('login');
        }
    }

    public function order() {
        if(Auth::id() && (Auth::User()->usertype == 1)){
            return view('admin.order')->with(['orders'=>Order::all()]);
        }else{
            return redirect('login');
        }
    }

    public function delivered($id) {

        if(Auth::id() && (Auth::User()->usertype == 1)){
            $order = Order::find($id);

            $order->delivery_status = 'delivered';
            $order->payment_status = 'Paid';
            $order->save();

            return redirect()->back();
        }else{
            return redirect('login');
        }
    }

    public function print_pdf($id) {
        if(Auth::id() && (Auth::User()->usertype == 1)){
            $order = Order::find($id);

            $pdf = PDF::loadView('admin.pdf',compact('order'));

            return $pdf->download('order_details.pdf');
        }else{
            return redirect('login');
        }  
    }

    public function send_email($id) {
        if(Auth::id() && (Auth::User()->usertype == 1)){
            $order = Order::find($id);
            
            return view('admin.email_info')->with('order', $order);
        }else{
            return redirect('login');
        }
    }

    public function send_user_email(Request $request, $id){

        if(Auth::id() && (Auth::User()->usertype == 1)){
            $order = Order::find($id);

            $details = [
                'greeting'=>$request->greeting,
                'firstline'=>$request->firstline,
                'body'=>$request->body,
                'button'=>$request->button,
                'url'=>$request->url,
                'lastline'=>$request->lastline
            ];

            Notification::send($order, new SendEmailNotification($details));

            return redirect()->back();
        }else{
            return redirect('login');
        }

        
    }

    public function searchdata(Request $request) {
        if(Auth::id() && (Auth::User()->usertype == 1)){
            $searchText = $request->search;

            $orders = Order::where('name','LIKE',"%$searchText%")->orWhere('phone','LIKE',"%$searchText%")->orWhere('product_title','LIKE',"%$searchText%")->get();

            return view('admin.order')->with('orders', $orders);
        }else{
            return redirect('login');
        }
    }
}
