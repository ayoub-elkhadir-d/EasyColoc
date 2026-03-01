<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- NAVBAR -->
<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-8">
                <h1 class="text-xl font-bold">EasyColoc</h1>
                <div class="flex space-x-4">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-black transition">Home</a>
                    <a href="{{ route('profile.show') }}" class="text-black font-semibold">Profile</a>
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-black transition">Dashboard</a>
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-600 hover:text-black transition">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="max-w-4xl mx-auto px-6 py-10">
    @if($errors->any())
    <div class="mb-6 p-4 bg-red-100 border border-red-300 rounded-xl">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8">
        <h2 class="text-3xl font-bold mb-8">Edit Profile</h2>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name', $user->name) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none"
                       required>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" 
                       name="email" 
                       value="{{ old('email', $user->email) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none"
                       required>
            </div>

            <!-- Reputation (Read-only) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Reputation</label>
                <div class="flex items-center space-x-2">
                    <span class="text-2xl font-bold {{ $user->reputation >= 100 ? 'text-green-600' : ($user->reputation >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $user->reputation }}
                    </span>
                    <span class="text-xl">⭐</span>
                    <span class="text-sm text-gray-500">(Cannot be edited)</span>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-4">
                <button type="submit" class="px-6 py-2 bg-black text-white rounded-xl hover:bg-gray-800 transition">
                    Save Changes
                </button>
                <a href="{{ route('profile.show') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
