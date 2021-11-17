<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Category;
use App\Order;
use App\OrderStatusEmail;
use App\OrderProduct;
use App\CustomProductParameter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\OrderStatus;
use Intervention\Image\ImageManagerStatic as Image;

class AdminController extends Controller
{
    public function __construct() {
    }

    public function orders(Request $request, $page = '') {
        // set url for paginator
        $pageSize = 10;
        $paginatorUrl = explode('/', $request->url());
        if ($page == '') {
            $page = 1;
        } else {
            unset($paginatorUrl[count($paginatorUrl) - 1]);
        }

        $paginatorUrl = implode('/', $paginatorUrl);

        $orders = Order::select('*')->orderBy('id', 'desc')->paginate($pageSize, ['orders.id'], 'page', $page)->withPath($paginatorUrl);
        return view('admin/orders', [
            'active' => 'Megrendelések',
            'orders' => $orders,
        ]);
    }

    public function orderDetails($id) {
        $order = Order::where('id', $id)->first();
        $products = OrderProduct::where('order_id', $id)->get();
        $emails = OrderStatusEmail::where('order_id', $id)->orderBy('created_at', 'DESC')->get();

        return view('admin/order_details', [
            'active' => 'Megrendelés részletek',
            'order' => $order,
            'products' => $products,
            'emails' => $emails,
        ]);
    }

    public function modifyOrder(Request $request) {
        $data = $request->all();
        Order::where('id', $data['id'])->update([
            'status' => $data['status'],
            'admin_comment' => $data['admin_comment'],
            'payed' => $data['payed'],
        ]);
        
        return 'success';
    }

    public function sendOrderStatusEmail(Request $request) {
        $data = $request->all();

        $order = Order::where('id', $data['orderId'])->first();
        $orderProducts = OrderProduct::where('order_id', $data['orderId'])->get();

        $emailRecord = new OrderStatusEmail();
        $emailRecord->order_id = $order->id;
        $emailRecord->status = $data['status'];
        $emailRecord->custom_text = is_null($data['customText']) ? '' : $data['customText'];
        $emailRecord->save();

        Mail::to($data['email'])->send(new OrderStatus($order, $orderProducts, $data['status'], $data['customText']));

        return 'success';
    }

    public function categories() {
        $categories = Category::all();

        return view('admin/categories', [
            'active' => 'Kategóriák',
            'categories' => $categories,
        ]);
    }

    public function removeCategory(Request $request) {
        $category = Category::where('id', $request->input('categoryId'))->first();
        if ($category) {
            Product::where('category_id', $request->input('categoryId'))->update(['category_id' => NULL, 'category_name' => '']);
            $category->delete();
            return 'ok';
        }

        return 'error';
    }

    public function createCategory(Request $request) {
        $category = new Category();
        $category->name = $request->input('newCategoryName');
        $category->save();

        return $category->id;
    }

    public function modifyCategory(Request $request) {
        $category = Category::where('id', $request->input('categoryId'))->update(['name' => $request->input('categoryNewName')]);
        Product::where('category_id', $request->input('categoryId'))->update(['category_name' => $request->input('categoryNewName')]);
    }

    public function products(Request $request, $page = '') {
        // set url for paginator
        $pageSize = 15;
        $paginatorUrl = explode('/', $request->url());
        if ($page == '') {
            $page = 1;
        } else {
            unset($paginatorUrl[count($paginatorUrl) - 1]);
        }

        $paginatorUrl = implode('/', $paginatorUrl);
        $products = Product::select('*')->paginate($pageSize, ['products.id'], 'page', $page)->withPath($paginatorUrl);
        return view('admin/products', [
            'active' => 'Termékek',
            'products' => $products,
        ]);
    }

    public function createProduct() {
        $categories = Category::select(['id', 'name'])->get();
        return view('admin/modify_product', [
            'active' => 'Új termék',
            'categories' => json_encode($categories),
        ]);
    }

    public function editProduct($productId) {
        $product = Product::where('id', $productId)->first();

        if (!$product) {
            return abort(404);
        };

        // set images
        $imageColorPairs = json_decode($product->images)->images;
        $imageNames = [];
        foreach ($imageColorPairs as $index => $image) {
            $image->id = $index;

            if ($image->extraImage == NULL) {
                $image->extraImage = new \StdClass();
                $image->extraImage->name = 'nincs';
                $image->extraImage->file = '';
            }


            if (!in_array($image->name, $imageNames, true)) {
                array_push($imageNames, $image->name);
            }
        }

        $categories = Category::select(['id', 'name'])->get();
        $customParameters = CustomProductParameter::getByProductId($product->id);
        return view('admin/modify_product', [
            'active' => 'Termék szerkesztése',
            'categories' => json_encode($categories),
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'categoryId' => $product->category_id == NULL ? -1 : $product->category_id,
            'price' => $product->price,
            'discount' => $product->discount,
            'parameterSettings' => $product->parameter_settings,
            'customParameters' => json_encode($customParameters),
            'imageColorPairs' => json_encode($imageColorPairs),
            'imageNames' => json_encode($imageNames),
            'isActive' => $product->active,
        ]);
    }

    public function copyProduct($productId) {
        $product = Product::where('id', $productId)->first();

        if (!$product) {
            return abort(404);
        };

        // set images
        $imageColorPairs = json_decode($product->images)->images;
        $imageNames = [];
        foreach ($imageColorPairs as $index => $image) {
            $image->id = $index;

            if ($image->extraImage == NULL) {
                $image->extraImage = new \StdClass();
                $image->extraImage->name = 'nincs';
                $image->extraImage->file = '';
            }


            if (!in_array($image->name, $imageNames, true)) {
                array_push($imageNames, $image->name);
            }
        }

        // parameter settings
        $parameterSettings = json_decode($product->parameter_settings);
        
        foreach ($parameterSettings as $parameterSetting) {
            $parameterSetting->image = '';
        }

        $categories = Category::select(['id', 'name'])->get();
        $customParameters = CustomProductParameter::getByProductId($product->id);
        return view('admin/modify_product', [
            'active' => 'Termék másolása',
            'categories' => json_encode($categories),
            'id' => '-1',
            'name' => $product->name . ' (másolat)',
            'description' => $product->description,
            'categoryId' => $product->category_id == NULL ? -1 : $product->category_id,
            'price' => $product->price,
            'discount' => $product->discount,
            'parameterSettings' => json_encode($parameterSettings),
            'customParameters' => json_encode($customParameters),
            'imageColorPairs' => json_encode([]),
            'imageNames' => json_encode([]),
            'isActive' => $product->active,
        ]);
    }

    public function modifyProduct(Request $request) {
        $data = $request->all();

        // retrieve or create product
        $product = Product::where('id', $data['id'])->first();
        $newProduct = false;
        if (!$product) {
            $product = new Product();
            $newProduct = true;
        }


        $product->name = $data['name'];
        $product->description = str_replace("\r\n", "<br>", $data['description']);
        $product->category_id = $data['categoryId'];
        $product->category_name = is_null($data['categoryName']) ? '' : $data['categoryName'];
        $product->price = intval($data['price']);
        $product->discount = intval($data['discount']);
        $product->discount_price = intval($data['discountPrice']);
        $product->shipping_time = 3;
        $product->active = $data['active'];
        $product->parameter_settings = $data['parameterSettings'];
        $product->has_custom_text_parameter = false;

        // main image
        $product->main_image = new \StdClass();
        $product->main_image->name = $data['mainImage']['name'];
        $product->main_image->extraImage = is_null($data['mainImage']['extraImage']['file']) ? '' : $data['mainImage']['extraImage']['file'];
        $product->main_image->color = $data['mainImage']['color'];
        $product->main_image = json_encode($product->main_image);

        // delete images which were not used after upload
        $processedImages = Storage::disk('processed_images')->files();
        for ($i = 0; $i < count($data['imagesToDelete']); $i++) {
            $imageId = explode('_', pathinfo($data['imagesToDelete'][$i], PATHINFO_FILENAME))[0];

            for ($j = 0; $j < count($processedImages); $j++) {
                $processedImageId = explode('_', pathinfo($processedImages[$j], PATHINFO_FILENAME))[0];
                if ($processedImageId == $imageId) {
                    Storage::disk('processed_images')->delete($processedImages[$j]);
                }
            }

            Storage::disk('public')->delete('product_images/' . $data['imagesToDelete'][$i]);
        }
        
        // decode images
        $product->images = new \StdClass;
        $product->images->images = json_decode($data['images']);

        // delete processed images
        $processedImages = Storage::disk('processed_images')->files();
        for ($i = 0; $i < count($product->images->images); $i++) {
            $imageId = explode('_', pathinfo($product->images->images[$i]->name, PATHINFO_FILENAME))[0];
            for ($j = 0; $j < count($processedImages); $j++) {
                $processedImageId = explode('_', pathinfo($processedImages[$j], PATHINFO_FILENAME))[0];
                if ($processedImageId == $imageId) {
                    Storage::disk('processed_images')->delete($processedImages[$j]);
                }
            }
        }

        // images
        for ($i = 0; $i < count($product->images->images); $i++) {
            unset($product->images->images[$i]->id);
            
            // create processed image
            $processedFileName = pathinfo(explode('_', $product->images->images[$i]->name)[0], PATHINFO_FILENAME) . '_' . $product->images->images[$i]->color . '_' . $product->images->images[$i]->extraImage->file . '.' . pathinfo($product->images->images[$i]->name, PATHINFO_EXTENSION);

            $coloredImage = Image::make(Storage::disk('public')->path('product_images/' . $product->images->images[$i]->name));
            if ($product->images->images[$i]->color !== '000000') {
                $coloredImage->colorize(intval(hexdec(substr($product->images->images[$i]->color, 0, 2)) / 255 * 100), intval(hexdec(substr($product->images->images[$i]->color, 2, 2)) / 255 * 100), intval(hexdec(substr($product->images->images[$i]->color, 4, 2)) / 255 * 100));
            }
            
            $coloredImage->insert(Storage::disk('public')->path('extra_images/watermark.png'));
            
            if ($product->images->images[$i]->extraImage->file) {
                $coloredImage->insert(Storage::disk('public')->path('extra_images/' . $product->images->images[$i]->extraImage->file . '.png'));
            }

            $coloredImage->save(Storage::disk('processed_images')->path($processedFileName));
        }

        $product->images = json_encode($product->images);

        // check if product has custom text parameter
        $data['customParameters'] = json_decode($data['customParameters']);
        for ($i = 0; $i < count($data['customParameters']); $i++) {
            if ($data['customParameters'][$i]->type == 'text') {
                $product->has_custom_text_parameter = true;
                break;
            }
        }

        // save product
        $product->save();

        // upload custom parameters
        if (!$newProduct) { 
            CustomProductParameter::where('product_id', $product->id)->delete();
        }

        for ($i = 0; $i < count($data['customParameters']); $i++) {
            // this is saved in the database because it takes less code
            // to process custom parameters if all 3 are saved
            if ($data['customParameters'][$i]->type == '') {
                $customParameter = new CustomProductParameter();
                $customParameter->product_id = $product->id;
                $customParameter->type = $data['customParameters'][$i]->type;
                $customParameter->name = '';
                $customParameter->index = $data['customParameters'][$i]->index;
                $customParameter->value = '';
                $customParameter->save();
            }

            if ($data['customParameters'][$i]->type == 'text') {
                $customParameter = new CustomProductParameter();
                $customParameter->product_id = $product->id;
                $customParameter->name = $data['customParameters'][$i]->name;
                $customParameter->type = $data['customParameters'][$i]->type;
                $customParameter->index = $data['customParameters'][$i]->index;
                $customParameter->value = '';
                $customParameter->save();
            }

            if ($data['customParameters'][$i]->type == 'select') {
                $values = explode(',', $data['customParameters'][$i]->values);
                for ($j = 0; $j < count($values); $j ++) {
                    $customParameter = new CustomProductParameter();
                    $customParameter->product_id = $product->id;
                    $customParameter->name = $data['customParameters'][$i]->name;
                    $customParameter->type = $data['customParameters'][$i]->type;
                    $customParameter->index = $data['customParameters'][$i]->index;
                    $customParameter->value = $values[$j];
                    $customParameter->save();
                }
            }
        }

        if ($newProduct) { 
            return 'Sikeres termékfeltöltés. A termék megtekintéséhez kattints <a href="/product/' . $product->id . '">ide</a>.';
        } else {
            return 'Sikeres termékmódosítás. A termék megtekintéséhez kattints <a href="/product/' . $product->id . '">ide</a>.';
        }
    }
}
