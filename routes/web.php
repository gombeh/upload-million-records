<?php

use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/upload', [SalesController::class, 'create']);
Route::post('/upload', [SalesController::class, 'store']);
Route::get('/batch/{id}', [SalesController::class, 'batch']);

Route::get('/products', function() {
    $res = file_get_contents("https://noavarpub.com/interview/php/json.php");
    $res = json_decode($res, true);
    $products = $res['result'];
    usort($products, function($a, $b){
        return (int)$a['product_price'] < (int)$b['product_price'] ? 1 : -1;
    });
    $html = "<table><thead><td>title</td><td>price</td></thead></tbody>";
    foreach($products as $product){
        $html .="<tr><td>{$product['product_title']}</td><td>{$product['product_price']}</td></tr>";
    }
    $html .= "</tbody></table>";
    echo $html;
});

