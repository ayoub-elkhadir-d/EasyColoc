<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
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
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-100 border border-green-300 rounded-xl">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8">
        <div class="flex justify-between items-start mb-8">
            <h2 class="text-3xl font-bold">My Profile</h2>
            <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-black text-white rounded-xl hover:bg-gray-800 transition">
                Edit Profile
            </a>
        </div>

        <div class="space-y-6">
            <!-- Name -->
            <div>
                <label class="text-sm font-semibold text-gray-600">Name</label>
                <p class="text-lg mt-1">{{ $user->name }}</p>
            </div>

            <!-- Email -->
            <div>
                <label class="text-sm font-semibold text-gray-600">Email</label>
                <p class="text-lg mt-1">{{ $user->email }}</p>
            </div>

            <!-- Role -->
            <div>
                <label class="text-sm font-semibold text-gray-600">Role</label>
                <p class="text-lg mt-1">
                    <span class="px-3 py-1 text-sm rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </p>
            </div>

            <!-- Reputation -->
            <div>
                <label class="text-sm font-semibold text-gray-600">Reputation</label>
                <div class="mt-2">
                    <div class="flex items-center space-x-3">
                        <span class="text-4xl font-bold {{ $user->reputation >= 100 ? 'text-green-600' : ($user->reputation >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $user->reputation }}
                        </span>
                        <span class="text-2xl">⭐</span>
                    </div>
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full {{ $user->reputation >= 100 ? 'bg-green-600' : ($user->reputation >= 50 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                 style="width: {{ min($user->reputation, 150) / 150 * 100 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            @if($user->reputation >= 100)
                                Excellent standing
                            @elseif($user->reputation >= 50)
                                Good standing
                            @else
                                Needs improvement
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Member Since -->
            <div>
                <label class="text-sm font-semibold text-gray-600">Member Since</label>
                <p class="text-lg mt-1">{{ $user->created_at->format('F d, Y') }}</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
