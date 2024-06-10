<!DOCTYPE html>
<html>
<head>
    <title>Admin - Add Menu</title>
</head>
<body>
    <h1>Add New Menu</h1>
    <form action="/admin/menus" method="POST">
        @csrf
        <label for="name">Name:</label>
        <input type="text" name="name" required>
        <br>
        <label for="description">Description:</label>
        <textarea name="description"></textarea>
        <br>
        <label for="price">Price:</label>
        <input type="number" name="price" step="0.01" required>
        <br>
        <button type="submit">Add Menu</button>
    </form>
</body>
</html>
