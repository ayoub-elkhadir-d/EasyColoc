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

<!-- NAVBAR -->
<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-8">
                <h1 class="text-xl font-bold">EasyColoc</h1>
                <div class="flex space-x-4">
                    <a href="{{ route('home') }}" class="text-black font-semibold">Home</a>
                    <a href="{{ route('profile.show') }}" class="text-gray-600 hover:text-black transition">Profile</a>
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-black transition">Dashboard</a>
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                <span class="text-xs px-2 py-1 rounded-full {{ auth()->user()->reputation >= 100 ? 'bg-green-100 text-green-800' : (auth()->user()->reputation >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    ⭐ {{ auth()->user()->reputation }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-600 hover:text-black transition">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-6 py-10">
    <div class="flex gap-8">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-white border border-gray-200 rounded-2xl p-4">
            <nav class="space-y-2">

                <button onclick="showSection(event,'colocation')" 
                    class="nav-btn w-full text-left px-4 py-3 rounded-xl bg-black text-white">
                    My Colocation
                </button>

                @if($colocation)
                <button onclick="showSection(event,'expenses')" 
                    class="nav-btn w-full text-left px-4 py-3 rounded-xl text-gray-700 hover:bg-gray-100">
                    Expenses
                </button>

                <button onclick="showSection(event,'settlements')" 
                    class="nav-btn w-full text-left px-4 py-3 rounded-xl text-gray-700 hover:bg-gray-100">
                    Settlements
                </button>

                @if($colocation->owner_id === auth()->id())
                <button onclick="showSection(event,'categories')" 
                    class="nav-btn w-full text-left px-4 py-3 rounded-xl text-gray-700 hover:bg-gray-100">
                    Categories
                </button>
                @endif
                @endif

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

                        <div class="mt-2">
                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $colocation->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($colocation->status) }}
                            </span>
                        </div>
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
                                <div>
                                    <span class="font-medium">{{ $member->name }}</span>
                                    <span class="ml-2 text-xs px-2 py-1 rounded-full {{ $member->reputation >= 100 ? 'bg-green-100 text-green-800' : ($member->reputation >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        ⭐ {{ $member->reputation }}
                                    </span>
                                </div>

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

                    <!-- CANCEL COLOCATION -->
                    @if($colocation->status === 'active')
                    <div class="mt-10">
                        <form method="POST" action="{{ route('colocation.cancel', $colocation) }}" onsubmit="return confirm('Are you sure? This will decrease your reputation by 1 point.')">
                            @csrf
                            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition">
                                Cancel Colocation
                            </button>
                        </form>
                    </div>
                    @endif

                    @endif
                </div>

                

                <!-- MEMBER VIEW (Non-owner members) -->
                @if($colocation && $colocation->owner_id !== auth()->id() && $colocation->members->contains(auth()->id()))
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 mt-6">
                    <h3 class="text-lg font-semibold mb-4">Your Membership</h3>
                    
                    @php
                        $debt = $colocation->calculateMemberDebt(auth()->id());
                    @endphp
                    
                    @if($debt > 0)
                    <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                        <p class="text-sm text-yellow-800">
                            <strong>Warning:</strong> You have an unpaid debt of {{ number_format($debt, 2) }}€. 
                            Leaving with debt will decrease your reputation by 1 point.
                        </p>
                    </div>
                    @else
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl">
                        <p class="text-sm text-green-800">
                            You have no unpaid debts. Leaving will increase your reputation by 1 point.
                        </p>
                    </div>
                    @endif
                    
                    <form method="POST" action="{{ route('colocation.leave', $colocation) }}" onsubmit="return confirm('Are you sure you want to leave this colocation?')">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition">
                            Leave Colocation
                        </button>
                    </form>
                </div>
                @endif

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

            <!-- EXPENSES SECTION -->
            @if($colocation)
            <div id="expenses-section" class="section hidden">
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8">
                    <h2 class="text-2xl font-bold mb-6">Expenses</h2>

                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">Add New Expense</h3>
                        <form method="POST" action="{{ route('depense.store', $colocation) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @csrf
                            <input type="text" name="title" placeholder="Title" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none" required>
                            <input type="number" step="0.01" name="amount" placeholder="Amount" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none" required>
                            <input type="date" name="date" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none" required>
                            <select name="category_id" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
                                <option value="">No Category</option>
                                @foreach($colocation->categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="md:col-span-2 px-4 py-2 bg-black text-white rounded-xl hover:bg-gray-800 transition">Add Expense</button>
                        </form>
                    </div>

                    <div class="space-y-3">
                        @forelse($colocation->depenses as $depense)
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-semibold">{{ $depense->title }}</h4>
                                    <p class="text-sm text-gray-600">{{ $depense->amount }} € - {{ $depense->date }} - Paid by {{ $depense->payer->name }}</p>
                                    @if($depense->category)
                                    <span class="text-xs bg-gray-200 px-2 py-1 rounded">{{ $depense->category->name }}</span>
                                    @endif
                                </div>
                                @if($depense->payer_id === auth()->id())
                                <form method="POST" action="{{ route('depense.destroy', [$colocation, $depense]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                                </form>
                                @endif
                            </div>
                            
                            <div class="mt-3 pt-3 border-t border-gray-300">
                                <p class="text-xs font-semibold text-gray-700 mb-2">Split among members:</p>
                                <div class="space-y-2">
                                    @foreach($depense->balances as $balance)
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="{{ $balance->is_paid ? 'text-green-600' : 'text-gray-700' }}">
                                            {{ $balance->user->name }}: {{ number_format($balance->amount, 2) }} €
                                            @if($balance->is_paid)
                                            <span class="text-xs">(Paid)</span>
                                            @endif
                                        </span>
                                        @if(!$balance->is_paid && $balance->user_id === auth()->id())
                                        <form method="POST" action="{{ route('balance.markPaid', [$colocation, $depense, $balance]) }}">
                                            @csrf
                                            <button type="submit" class="text-xs px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">Mark Paid</button>
                                        </form>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-8">No expenses yet</p>
                        @endforelse
                    </div>
                </div>
            </div>

            @if($colocation->owner_id === auth()->id())
            <div id="categories-section" class="section hidden">
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8">
                    <h2 class="text-2xl font-bold mb-6">Categories</h2>

                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">Add New Category</h3>
                        <form method="POST" action="{{ route('category.store', $colocation) }}" class="flex gap-4">
                            @csrf
                            <input type="text" name="name" placeholder="Category name" class="flex-1 px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none" required>
                            <button type="submit" class="px-6 py-2 bg-black text-white rounded-xl hover:bg-gray-800 transition">Add</button>
                        </form>
                    </div>

                    <div class="space-y-3">
                        @forelse($colocation->categories as $category)
                        <div class="flex justify-between items-center p-4 bg-gray-50 border border-gray-200 rounded-xl">
                            <span class="font-medium">{{ $category->name }}</span>
                            <form method="POST" action="{{ route('category.destroy', [$colocation, $category]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-8">No categories yet</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif
            @endif

            <!-- SETTLEMENTS SECTION -->
            @if($colocation)
            <div id="settlements-section" class="section hidden">
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8">
                    <h2 class="text-2xl font-bold mb-6">Settlements - Qui doit à qui</h2>

                    @php
                        $settlements = $colocation->getSettlements();
                        $creditors = collect($settlements)->filter(fn($s) => $s['balance'] > 0)->sortByDesc('balance');
                        $debtors = collect($settlements)->filter(fn($s) => $s['balance'] < 0)->sortBy('balance');
                    @endphp

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <p class="text-sm text-green-600 font-semibold">To Receive</p>
                            <p class="text-2xl font-bold text-green-700">{{ number_format($creditors->sum('balance'), 2) }} €</p>
                        </div>
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                            <p class="text-sm text-red-600 font-semibold">To Pay</p>
                            <p class="text-2xl font-bold text-red-700">{{ number_format(abs($debtors->sum('balance')), 2) }} €</p>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <p class="text-sm text-blue-600 font-semibold">Total Expenses</p>
                            <p class="text-2xl font-bold text-blue-700">{{ number_format($colocation->depenses->sum('amount'), 2) }} €</p>
                        </div>
                    </div>

                    <!-- Balances List -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Member Balances</h3>
                        
                        @foreach($settlements as $settlement)
                        <div class="p-4 border border-gray-200 rounded-xl {{ $settlement['balance'] > 0 ? 'bg-green-50' : ($settlement['balance'] < 0 ? 'bg-red-50' : 'bg-gray-50') }}">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="font-semibold">{{ $settlement['user']->name }}</span>
                                    @if($settlement['user']->id === auth()->id())
                                    <span class="ml-2 text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">You</span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if($settlement['balance'] > 0)
                                    <p class="text-lg font-bold text-green-700">+{{ number_format($settlement['balance'], 2) }} €</p>
                                    <p class="text-xs text-green-600">Should receive</p>
                                    @elseif($settlement['balance'] < 0)
                                    <p class="text-lg font-bold text-red-700">{{ number_format($settlement['balance'], 2) }} €</p>
                                    <p class="text-xs text-red-600">Should pay</p>
                                    @else
                                    <p class="text-lg font-bold text-gray-700">0.00 €</p>
                                    <p class="text-xs text-gray-600">Settled</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Settlement Instructions -->
                    @if($debtors->isNotEmpty() && $creditors->isNotEmpty())
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Suggested Settlements</h3>
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 space-y-3">
                            @php
                                $debtorsList = $debtors->values()->all();
                                $creditorsList = $creditors->values()->all();
                                $suggestions = [];
                                
                                foreach ($debtorsList as $debtor) {
                                    $remaining = abs($debtor['balance']);
                                    foreach ($creditorsList as &$creditor) {
                                        if ($remaining <= 0 || $creditor['balance'] <= 0) continue;
                                        $amount = min($remaining, $creditor['balance']);
                                        $suggestions[] = [
                                            'from' => $debtor['user']->name,
                                            'to' => $creditor['user']->name,
                                            'amount' => $amount
                                        ];
                                        $remaining -= $amount;
                                        $creditor['balance'] -= $amount;
                                    }
                                }
                            @endphp
                            
                            @foreach($suggestions as $suggestion)
                            <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                <span class="font-medium">{{ $suggestion['from'] }}</span>
                                <span class="text-gray-500">→</span>
                                <span class="font-medium">{{ $suggestion['to'] }}</span>
                                <span class="font-bold text-blue-700">{{ number_format($suggestion['amount'], 2) }} €</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

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
        btn.classList.add('text-gray-700','hover:bg-gray-100');
    });

    event.target.classList.remove('text-gray-700','hover:bg-gray-100');
    event.target.classList.add('bg-black','text-white');
}
</script>

</body>
</html>