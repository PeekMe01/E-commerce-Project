<?php

namespace App\Http\Controllers;

use Stripe;
use Session;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function redirect() {
        $usertype = Auth::user()->usertype;

        if($usertype=='1'){
            return view('admin.home');
        }else{
            return view('home.userpage')->with('products',Product::paginate(10));
        }
    }

    public function index() {
        return view('home.userpage')->with('products',Product::paginate(10));
    }

    public function product_details($id) {

        $product = Product::find($id);

        return view('home.product_details')->with('product', $product);
    }

    public function add_cart(Request $request, $id) {
        if(Auth::id()){
            $user = Auth::user();
            $product = Product::find($id);
            $cart = Cart::create([
                'name'=>$user->name,
                'email'=>$user->email,
                'phone'=>$user->phone,
                'address'=>$user->address,
                'user_id'=>$user->id,
                'product_title'=>$product->title,
                'price'=>$product->discount_price?($product->discount_price * $request->quantity):($product->prize * $request->quantity),
                'image'=>$product->image,
                'quantity'=>$request->quantity,
                'product_id'=>$product->id
            ]);

            return redirect()->back();
        } else {
            return redirect('login');
        }
    }

    public function show_cart() {
        return view('home.showcart')->with('cart',Cart::where('user_id','=',Auth::user()->id)->get());
    }

    public function remove_cart($id) {
        $cart=Cart::find($id);
        $cart->delete();

        return redirect()->back();
    }

    public function cash_order() {
        $user = Auth::user();

        $userid = $user->id;

        $data=Cart::where('user_id', '=', $userid)->get();

        foreach($data as $data){
            $order = Order::create([
                'name'=>$data->name,
                'email'=>$data->email,
                'phone'=>$data->phone,
                'address'=>$data->address,
                'user_id'=>$data->user_id,

                'product_title'=>$data->product_title,
                'price'=>$data->price,
                'quantity'=>$data->quantity,
                'image'=>$data->image,
                'product_id'=>$data->product_id,

                'payment_status' => 'cash on delivery',
                'delivery_status' => 'processing'
            ]);

            $cart_id = $data->id;
            $cart = Cart::find($cart_id);
            $cart->delete();
        }

        return redirect()->back()->with(['message' => 'We have Received your Order. We will connect with you soon...', 'type'=>'good']);
    }

    public function stripe($totalprice) {
        return view('home.stripe')->with('totalprice',$totalprice);
    }

    public function stripePost(Request $request, $totalprice)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    
        Stripe\Charge::create ([
                "amount" => $totalprice * 100,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Thanks for payment." 
        ]);

        $user = Auth::user();

        $userid = $user->id;

        $data=Cart::where('user_id', '=', $userid)->get();

        foreach($data as $data){
            $order = Order::create([
                'name'=>$data->name,
                'email'=>$data->email,
                'phone'=>$data->phone,
                'address'=>$data->address,
                'user_id'=>$data->user_id,

                'product_title'=>$data->product_title,
                'price'=>$data->price,
                'quantity'=>$data->quantity,
                'image'=>$data->image,
                'product_id'=>$data->product_id,

                'payment_status' => 'Paid',
                'delivery_status' => 'processing'
            ]);

            $cart_id = $data->id;
            $cart = Cart::find($cart_id);
            $cart->delete();
        }
      
        Session::flash('success', 'Payment successful!');
              
        return back();
    }
}
