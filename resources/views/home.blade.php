<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">

<div class="max-w-7xl mx-auto px-6 py-10">
    <div class="flex gap-8">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-white border border-gray-200 rounded-2xl p-4">
            <nav class="space-y-2">

                <button onclick="showSection(event,'colocation')" 
                    class="nav-btn w-full text-left px-4 py-3 rounded-xl bg-black text-white">
                    My Colocation
                </button>

                @if(auth()->user()->role === 'admin')
                <button onclick="showSection(event,'users')" 
                    class="nav-btn w-full text-left px-4 py-3 rounded-xl text-gray-700 hover:bg-gray-100">
                    Users Management
                </button>
                @endif

            </nav>
        </aside>

        <!-- CONTENT -->
        <div class="flex-1">

            @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-300 rounded-xl">
                {{ session('success') }}
            </div>
            @endif

            <!-- COLOCATION SECTION -->
            <div id="colocation-section" class="section">

                @if($colocation)

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8">

                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-black">
                            {{ $colocation->num }}
                        </h2>

                        <p class="text-gray-600 mt-2">
                            {{ $colocation->description }}
                        </p>

                        <p class="text-sm text-gray-500 mt-1">
                            Owner: {{ $colocation->owner->name }}
                        </p>
                    </div>

                    @if($colocation->owner_id === auth()->id())

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                        <!-- UPDATE -->
                        <div class="bg-white border border-gray-200 rounded-2xl p-6">
                            <h3 class="text-lg font-semibold mb-4">
                                Update Colocation
                            </h3>

                            <form method="POST"
                                  action="{{ route('colocation.update',$colocation) }}"
                                  class="space-y-4">
                                @csrf
                                @method('PUT')

                                <input type="text"
                                       name="num"
                                       value="{{ $colocation->num }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none"
                                       required>

                                <textarea name="description"
                                          rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none"
                                          required>{{ $colocation->description }}</textarea>

                                <button type="submit"
                                    class="w-full px-4 py-2 bg-black text-white rounded-xl hover:bg-gray-800 transition">
                                    Update
                                </button>
                            </form>
                        </div>

                        <!-- INVITE -->
                        <div class="bg-white border border-gray-200 rounded-2xl p-6">
                            <h3 class="text-lg font-semibold mb-4">
                                Send Invitation
                            </h3>

                            <form method="POST"
                                  action="{{ route('invitation.send',$colocation) }}"
                                  class="space-y-4">
                                @csrf

                                <input type="email"
                                       name="email"
                                       placeholder="Email address"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none"
                                       required>

                                <button type="submit"
                                    class="w-full px-4 py-2 bg-black text-white rounded-xl hover:bg-gray-800 transition">
                                    Send Invitation
                                </button>
                            </form>
                        </div>

                    </div>

                    <!-- MEMBERS -->
                    <div class="mt-10">
                        <h3 class="text-lg font-semibold mb-4">
                            Members
                        </h3>

                        <div class="space-y-3">
                            @foreach($colocation->members as $member)
                            <div class="flex justify-between items-center p-4 bg-gray-50 border border-gray-200 rounded-xl">
                                <span>{{ $member->name }}</span>

                                <form method="POST"
                                      action="{{ route('colocation.removeMember',[$colocation,$member->id]) }}">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="text-sm text-gray-600 hover:text-black transition">
                                        Remove
                                    </button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    @endif
                </div>

                @else

                <!-- CREATE COLOCATION -->
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 max-w-md">
                    <h2 class="text-2xl font-bold mb-6">
                        Create New Colocation
                    </h2>

                    <form method="POST"
                          action="{{ route('colocation.store') }}"
                          class="space-y-4">
                        @csrf

                        <input type="text"
                               name="num"
                               placeholder="Number"
                               class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none"
                               required>

                        <textarea name="description"
                                  rows="4"
                                  placeholder="Description"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none"
                                  required></textarea>

                        <button type="submit"
                            class="w-full px-4 py-2 bg-black text-white rounded-xl hover:bg-gray-800 transition">
                            Create
                        </button>
                    </form>
                </div>

                @endif
            </div>

            <!-- USERS SECTION -->
            @if(auth()->user()->role === 'admin')
            <div id="users-section" class="section hidden">

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8">
                    <h2 class="text-2xl font-bold mb-6">
                        Users Management
                    </h2>

                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-gray-300 bg-gray-50">
                            <th class="py-3 text-left">ID</th>
                            <th class="py-3 text-left">Name</th>
                            <th class="py-3 text-left">Email</th>
                            <th class="py-3 text-left">Role</th>
                            <th class="py-3 text-left">Status</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($users as $user)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3">{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td class="text-gray-600">{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>
                                {{ $user->is_banned ? 'Banned' : 'Active' }}
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<script>
function showSection(event, section) {
    document.querySelectorAll('.section').forEach(el => el.classList.add('hidden'));
    document.getElementById(section + '-section').classList.remove('hidden');

    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.classList.remove('bg-black','text-white');
        btn.classList.add('text-gray-700');
    });

    event.target.classList.add('bg-black','text-white');
}
</script>

</body>
</html>