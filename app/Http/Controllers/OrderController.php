<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderMenu;
use App\Models\Menu;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        $order = new Order();
        $order->status = 'pending';
        $order->save();

        return response()->json(['order_id' => $order->id, 'message' => 'Order created successfully']);
    }

    public function updateCart(Request $request, $menuId)
    {
        $order = Order::find($request->order_id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $menu = Menu::find($menuId);
        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        $orderMenu = OrderMenu::where('order_id', $order->id)->where('menu_id', $menuId)->first();
        if ($orderMenu) {
            if ($request->increment) {
                $orderMenu->quantity += 1;
            } else {
                $orderMenu->quantity -= 1;
                if ($orderMenu->quantity <= 0) {
                    $orderMenu->delete();
                    return response()->json(['message' => 'Menu removed from cart', 'order' => $order->load('orderMenus.menu')]);
                }
            }
            $orderMenu->save();
        } else {
            $orderMenu = new OrderMenu();
            $orderMenu->order_id = $order->id;
            $orderMenu->menu_id = $menuId;
            $orderMenu->quantity = 1;
            $orderMenu->save();
        }

        $total_price = $order->orderMenus->sum(function($orderMenu) {
            return $orderMenu->menu->price * $orderMenu->quantity;
        });

        return response()->json(['message' => 'Cart updated successfully', 'total_price' => $total_price, 'order' => $order->load('orderMenus.menu')]);
    }

    public function completeOrder(Request $request)
    {
        $order = Order::find($request->order_id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->status = 'completed';
        $order->total_price = $request->paid_amount;
        $order->save();

        return response()->json(['message' => 'Order completed successfully']);
    }
}
