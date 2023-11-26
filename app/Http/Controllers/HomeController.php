<?php

namespace App\Http\Controllers;

use Stripe;
use Session;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Reply;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use RealRashid\SweetAlert\Facades\Alert;

class HomeController extends Controller
{
    public function redirect() {
        $usertype = Auth::user()->usertype;

        // 2
        if($usertype=='1'){
            $total_product = Product::all()->count();
            $total_order = Order::all()->count();
            $total_user = User::all()->count();

            $total_revenue = Order::selectRaw('sum(price) as total')->first();
            $total_delivered = Order::where('delivery_status','=','delivered')->get()->count();
            $total_processing = Order::where('delivery_status','=','processing')->get()->count();
            
            return view('admin.home')->with(['total_product'=>$total_product, 'total_order'=>$total_order, 'total_user'=>$total_user, 'total_revenue'=>$total_revenue->total, 'total_delivered'=>$total_delivered, 'total_processing'=>$total_processing]);
        }else{
            return view('home.userpage')->with(['products'=>Product::paginate(10), 'comments'=>Comment::orderby('id','desc')->get(), 'replies'=>Reply::all()]);
        }
    }

    public function index() {
        return view('home.userpage')->with(['products'=>Product::paginate(10), 'comments'=>Comment::orderby('id','desc')->get(), 'replies'=>Reply::all()]);
    }

    public function product_details($id) {

        $product = Product::find($id);

        return view('home.product_details')->with('product', $product);
    }

    public function add_cart(Request $request, $id) {
        if(Auth::id()){
            $user = Auth::user();
            $product = Product::find($id);

            $product_exist_id = Cart::where('product_id','=',$id)->where('user_id','=',$user->id)->get('id')->first();

            if($product_exist_id){
                $cart = Cart::find($product_exist_id)->first();
                $quantity = $cart->quantity;
                $cart->quantity = $quantity + $request->quantity;
                $cart->price = $product->discount_price?($product->discount_price * $cart->quantity):($product->prize * $cart->quantity);
                $cart->save();
            }else{
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
            }
            Alert::success('Product Added Successfully', ' We have added product to the cart');

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
        Alert::info('Product Removed Successfully', ' We have removed product from the cart');
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

    public function show_order() {
        if(Auth::id()){
            $user = Auth::user();
            $userid = $user->id;
            $order=Order::where('user_id','=',$userid)->get();
            return view('home.order')->with('order', $order);
        }else{
            return redirect('login');
        }
    }

    public function cancel_order($id) {
        $order = Order::find($id);

        $order->delivery_status = 'You canceled the order';

        $order->save();

        return redirect()->back();
    }

    public function add_comment(Request $request){
        $comment = Comment::create([
            'name'=>Auth::user()->name,
            'user_id'=>Auth::user()->id,
            'comment'=>$request->comment
        ]);

        return redirect()->back();

    }

    public function add_reply(Request $request) {
        $reply = Reply::create([
            'name'=>Auth::user()->name,
            'user_id'=>Auth::user()->id,
            'comment_id'=>$request->commentId,
            'reply'=>$request->reply,
        ]);

        return redirect()->back();
    }

    public function product_search(Request $request) {
        $search_text = $request->search;

        $products = Product::where('title', 'LIKE', "%$search_text%")->orWhere('catagory', 'LIKE', "$search_text")->paginate(10);

        return view('home.userpage')->with(['products'=>$products, 'comments'=>Comment::orderby('id','desc')->get(), 'replies'=>Reply::all()]);
    }

    public function search_product(Request $request) {
        $search_text = $request->search;

        $products = Product::where('title', 'LIKE', "%$search_text%")->orWhere('catagory', 'LIKE', "$search_text")->paginate(10);

        return view('home.all_product')->with(['products'=>$products, 'comments'=>Comment::orderby('id','desc')->get(), 'replies'=>Reply::all()]);
    }

    public function product(){
        return view('home.all_product')->with(['products'=>Product::paginate(10), 'comments'=>Comment::all(), 'replies'=>Reply::all()]);
    }
}
