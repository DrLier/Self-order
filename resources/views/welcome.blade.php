<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Cafe Self-Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1>Welcome to Cafe Self-Order</h1>
            <p class="lead">Order your favorite menu items without waiting in line!</p>
            <a href="{{ url('/self-order') }}" class="btn btn-primary btn-lg">Start Ordering</a>
        </div>
    </div>
</div>
</body>
</html>