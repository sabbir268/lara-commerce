<?php

namespace App\Http\Controllers\API;

use Auth;
use JWTAuth;
use App\User;
use Response;
use Validator;
use JWTFactory;
use App\Product;
use App\Wishlist;
use App\Cart;
use App\BusinessSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserApiController extends Controller
{
    public function register(Request $request)
    {
        // return $request->email;
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'name' => 'required',
            'password'=> 'required',
            'user_type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        
        $register = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'user_type' => $request->user_type,
            'password' => bcrypt($request->password),
        ]);
        
        if(BusinessSetting::where('type', 'email_verification')->first()->value != 1){
            $register->email_verified_at = date('Y-m-d H:m:s');
            $register->save();
        }

        $token = JWTAuth::fromUser($register);
        $user = User::findOrFail($register->id);
        $user->token = $token;
        return $this->sendResponse($user, 'Registration Completed.', 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password'=> 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $credentials = $request->only('email', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return $this->sendResponse($token, 'Login and user retrived successfully.');
    }
    
    public function user(Request $request)
    {
        $user = auth()->user();
        return $this->sendResponse($user, 'User retrived successfully.');
    }

    //User Profile Update
    public function user_update(Request $request){
        if(auth()->user()){
            $user = User::findOrFail(auth()->user()->id);
            $user->name = $request->name;
            if($request->new_password != null && ($request->new_password == $request->confirm_password)){
                $user->password = Hash::make($request->new_password);
            }
            if($request->hasFile('photo')){
                $user->avatar_original = $request->photo->store('uploads/users');
            }
            $user->save();
            return $this->sendResponse($user, 'Profile Updated successfully.');
        }else{
            return $this->sendError('Unauthorized User', 401);
        }
    }

    //User Shipping Info Update
    public function shipping_info(Request $request){
        if(auth()->user()){
            $user = User::findOrFail(auth()->user()->id);
            $user->address = $request->address;
            $user->country = $request->country;
            $user->city = $request->city;
            $user->postal_code = $request->postal_code;
            $user->phone = $request->phone;
            $user->save();
            return $this->sendResponse($user, 'Shipping Info Updated successfully.');
        }else{
            return $this->sendError('Unauthorized User', 401);
        }
    }

    public function logout()
    {
        auth()->logout();
        return $this->sendResponse(null, 'Logged Out successfully.');
    }

    public function wishlist(){
        if(auth()->user()){
            $wishlists = Wishlist::where('user_id', auth()->user()->id)->get();
            foreach($wishlists as $key => $wishlist){
                $wishlists[$key]['product'] = Product::where('id', $wishlist->product_id)->where('published', 1)->first();
            }
            return $this->sendResponse($wishlists, 'Wishlist retrived successfully.');
        }else{
            return $this->sendError('Unauthorized User', 401);
        }
    }
    
    public function add_wishlist(Request $request){
        if(auth()->user()){
            $validator = Validator::make($request->all(), [
                'product_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $wishlist = Wishlist::where('user_id', auth()->user()->id)->where('product_id', $request->product_id)->first();
            if($wishlist == null){
                $wishlist = new Wishlist;
                $wishlist->user_id = auth()->user()->id;
                $wishlist->product_id = $request->product_id;
                $wishlist->save();
                return $this->sendResponse($wishlist, 'Product Added to wishlist successfully.', 201);
            }else{
                return $this->sendResponse($wishlist, 'Product Already exixt in wishlist.');
            }
        }else{
            return $this->sendError('Unauthorized User', 401);
        }
    }
    
    public function remove_wishlist(Request $request){
        if(auth()->user()){
            $validator = Validator::make($request->all(), [
                'wishlist_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $wishlist = Wishlist::findOrFail($request->wishlist_id);
            if($wishlist!=null){
                if(Wishlist::destroy($request->wishlist_id)){
                    return $this->sendResponse(null, 'Wishlist removed successfully.');
                }
            }

        }else{
            return $this->sendError('Unauthorized User', 401);
        }
    }

    public function cart(){
        if(auth()->user()){
            $carts = Cart::where('user_id', auth()->user()->id)->get();
            foreach($carts as $key => $cart){
                $carts[$key]['product'] = Product::where('id', $cart->product_id)->where('published', 1)->first();
            }
            return $this->sendResponse($carts, 'Cart retrived successfully.');
        }else{
            return $this->sendError('Unauthorized User', 401);
        }
    }
    
    public function add_cart(Request $request){
        if(auth()->user()){
            $validator = Validator::make($request->all(), [
                'product_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $cart = Cart::where('user_id', auth()->user()->id)->where('product_id', $request->product_id)->first();
            if($cart == null){
                $cart = new Cart;
                $cart->user_id = auth()->user()->id;
                $cart->product_id = $request->product_id;
                $cart->save();
                return $this->sendResponse($cart, 'Product Added to cart successfully.', 201);
            }else{
                return $this->sendResponse($cart, 'Product Already exixt in cart.');
            }
        }else{
            return $this->sendError('Unauthorized User', 401);
        }
    }
    
    public function remove_cart(Request $request){
        if(auth()->user()){
            $validator = Validator::make($request->all(), [
                'cart_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $cart = Cart::findOrFail($request->cart_id);
            if($cart!=null){
                if(Cart::destroy($request->cart_id)){
                    return $this->sendResponse(null, 'Cart removed successfully.');
                }
            }

        }else{
            return $this->sendError('Unauthorized User', 401);
        }
    }
}
