<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Order;
use App\OrderProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatus;
use App\Mail\Welcome;
use App\Mail\ContactMessage;
use App\OrderStatusEmail;

class HomeController extends Controller
{
    public function __construct() {
    }

    public function index() {
        $sliderImages = array_diff(scandir('./images/home_page/'), array('.', '..'));
        $newProducts = Product::orderBy('created_at', 'DESC')->where('active', '1')->limit(12)->get();
        $discountedProducts = Product::where('discount_price', '<>', 0)->where('active', '1')->orderBy('created_at', 'DESC')->limit(12)->get();
        return view('home', [
            'newProducts' => $newProducts,
            'discountedProducts' => $discountedProducts,
            'sliderImages' => array_values($sliderImages),
        ]);
    }

    public function aszf() {
        return view('aszf', [
            'contentOutsideApp' => true,
        ]);
    }

    public function shipping() {
        return view('shipping', [
            'contentOutsideApp' => true,
        ]);
    }

    public function contact() {
        return view('contact');
    }

    public function customOrder() {
        return view('custom_order');
    }

    public function sendCustomOrderMessage(Request $request) {
        $data = $request->all();

        Mail::to('zsuzsi@colorzebdesign.hu')->send(new ContactMessage($data['name'], $data['email'], $data['text']));

        return 'success';
    }

    public function sendContactMessage(Request $request) {
        $data = $request->all();

        Mail::to('zsuzsi@colorzebdesign.hu')->send(new ContactMessage($data['name'], $data['email'], $data['text']));

        return 'success';
    }

    public function aboutColorzeb() {
        return view('colorzeb');
    }

    public function userOrders(Request $request, $page = '') {
        // set url for paginator
        $pageSize = 10;
        $paginatorUrl = explode('/', $request->url());
        if ($page == '') {
            $page = 1;
        } else {
            unset($paginatorUrl[count($paginatorUrl) - 1]);
        }

        $paginatorUrl = implode('/', $paginatorUrl);

        $orders = Order::select('*')->where('user_id', Auth::user()->id)->paginate($pageSize, ['orders.id'], 'page', $page)->withPath($paginatorUrl);
        return view('user_orders', [
            'active' => 'Megrendeléseim',
            'orders' => $orders,
            'userName' => Auth::user()->name,
        ]);
    }

    public function userOrderDetails($id) {
        $order = Order::where('user_id', Auth::user()->id)->where('id', $id)->first();
        $products = OrderProduct::where('order_id', $id)->get();
        if (!$order) {
            return abort(404);
        };

        return view('user_order_details', [
            'active' => 'Megrendelés részletek',
            'userName' => Auth::user()->name,
            'order' => $order,
            'products' => $products,
        ]);
    }

    public function userData() {
        return view('user_data', [
            'active' => 'Adataim',
            'userName' => Auth::user()->name,
            'user' => Auth::user(),
        ]);   
    }

    public function saveUserData(Request $request) {
        $data = $request->all();
        $user = Auth::user();
        $user->payment_method = $data['paymentMethod'];
        $user->billing_name = $data['billingName'];
        $user->billing_tax_number = is_null($data['billingTaxNumber']) ? '' : $data['billingTaxNumber'];
        $user->billing_zip_code = $data['billingZip'];
        $user->billing_city = $data['billingCity'];
        $user->billing_address = $data['billingAddress'];
        $user->shipping_name = $data['shippingName'];
        $user->shipping_phone = $data['shippingPhone'];
        $user->shipping_zip_code = $data['shippingZip'];
        $user->shipping_city = $data['shippingCity'];
        $user->shipping_address = $data['shippingAddress'];
        $user->shipping_method = $data['shippingMethod'];
        $user->save();

        return 'success';
    }
}
