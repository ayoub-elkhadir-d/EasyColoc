<!DOCTYPE html>
<html>
<head>
    <title>Colocations</title>
</head>
<body>
    <h1>Colocations</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form method="POST" action="{{ route('colocation.store') }}">
        @csrf
        <div>
            <label>Num:</label>
            <input type="text" name="num" required>
        </div>
        <div>
            <label>Description:</label>
            <textarea name="description" required></textarea>
        </div>
        <button type="submit">Add New Colocation</button>
    </form>

    <h2>List</h2>
    <ul>
        @foreach($colocations as $colocation)
            <li>{{ $colocation->num }} - {{ $colocation->description }}</li>
        @endforeach
    </ul>
</body>
</html>
