<!DOCTYPE html>
<html>
<head>
    <title>Self Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-lg-8 mt-2">
            <div class="row">
                <div class="col-lg-4">
                    <div class="col-auto">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="basic-addon1">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                @if(count($menus) > 0)
                    @foreach ($menus as $menu)
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mt-3">
                            <div class="card h-100" onclick="addToCart({{ $menu->id }})">
                                <img src="{{ asset('storage/menu/' . $menu->image) }}" class="card-img-top" alt="{{ $menu->name }}" style="height: 120px;">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $menu->name }}</h5>
                                    <p class="card-text">{{ $menu->price }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 mt-4">
                        <div class="alert alert-danger" role="alert">
                            Menu masih kosong, input menu terlebih dahulu.
                        </div>
                    </div>
                @endif
            </div>
            <div class="row mt-4">
                {{ $menus->links('pagination::bootstrap-5') }}
            </div>
        </div>
        <div class="col-lg-4 mt-2">
            <div class="card h-100">
                <div class="card-header text-center">
                    @if ($order)
                        Order Code: {{ $order->invoice_number }}
                    @else
                        Self Order
                    @endif
                </div>
                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success text-center">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div id="cart-items">
                        @if ($order)
                            @foreach ($order->orderMenus as $item)
                                <div class="card mt-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <img src="{{ asset('storage/menus/' . $item->menu->image) }}" style="width: 80px;height: 80px" alt="{{ $item->menu->name }}" />
                                        <div>
                                            <span>{{ $item->menu->name }}</span><br>
                                            <span class="text-muted">{{ $item->unit_price }}</span>
                                        </div>
                                        <div class="d-flex align-items-center me-2">
                                            <button class="btn btn-sm btn-warning me-2" onclick="updateCart({{ $item->menu->id }}, false)">-</button>
                                            <span>{{ $item->quantity }}</span>
                                            <button class="btn btn-sm btn-primary ms-2" onclick="updateCart({{ $item->menu->id }})">+</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center">
                                <p>Keranjang masih kosong</p>
                            </div>
                        @endif
                    </div>

                    @if ($total_price != 0)
                        <h4 class="text-center mt-3" id="total_price">Total: Rp {{ number_format($total_price, 0, ',', '.') }}</h4>
                    @endif
                </div>
                @if($order)
                    <div class="card-footer text-center">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Pembayaran
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form class="row g-3" action="/orders/complete" method="POST">
                            @csrf
                            <div class="col-md-12">
                                <label for="paid_amount" class="form-label">Uang yang dibayarkan</label>
                                <input type="number" class="form-control" id="paid_amount" name="paid_amount" required>
                                @error('paid_amount')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    let orderId = {{ $order ? $order->id : 'null' }};

    function addToCart(menuId) {
        if (!orderId) {
            createOrder().then(() => {
                updateCart(menuId);
            });
        } else {
            updateCart(menuId);
        }
    }

    function updateCart(menuId, increment = true) {
        fetch('/orders/update/' + menuId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order_id: orderId, increment: increment })
        }).then(response => response.json())
          .then(data => {
              console.log(data);
              if (data.total_price !== undefined) {
                  document.getElementById('total_price').innerText = 'Total: Rp ' + new Intl.NumberFormat('id-ID').format(data.total_price);
              }
              updateCartUI(data.order);
          });
    }

    function createOrder() {
        return fetch('/orders/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => response.json())
          .then(data => {
              orderId = data.order_id;
              console.log(data);
              updateCartUI({ order_menus: [] });
          });
    }

    function updateCartUI(order) {
        const cartContainer = document.getElementById('cart-items');
        cartContainer.innerHTML = '';

        order.order_menus.forEach(item => {
            const cartItem = document.createElement('div');
            cartItem.classList.add('card', 'mt-2');
            cartItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <img src="{{ asset('storage/menus/') }}/${item.menu.image}" style="width: 80px;height: 80px" alt="${item.menu.name}" />
                    <div>
                        <span>${item.menu.name}</span><br>
                        <span class="text-muted">${item.unit_price}</span>
                    </div>
                    <div class="d-flex align-items-center me-2">
                        <button class="btn btn-sm btn-warning me-2" onclick="updateCart(${item.menu.id}, false)">-</button>
                        <span>${item.quantity}</span>
                        <button class="btn btn-sm btn-primary ms-2" onclick="updateCart(${item.menu.id})">+</button>
                    </div>
                </div>
            `;
            cartContainer.appendChild(cartItem);
        });
    }
</script>
</body>
</html>