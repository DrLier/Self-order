<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        return view('menus', compact('menus'));
    }

    public function showSelfOrder()
    {
        $menus = Menu::paginate(10);
        $order = Order::where('status', 'pending')->first(); // Asumsi order dengan status 'pending' adalah order aktif
        $total_price = $order ? $order->orderMenus->sum(function($orderMenu) {
            return $orderMenu->menu->price * $orderMenu->quantity;
        }) : 0;

        return view('self_order', compact('menus', 'order', 'total_price'));
    }
}
