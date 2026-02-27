<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">

<!-- ================= NAVBAR ================= -->
<nav class="bg-white border-b shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between items-center h-16">

            <h1 class="text-xl font-bold">
                EasyColoc
            </h1>

            <div class="flex items-center gap-6">

                <div class="text-right hidden sm:block">
                    <p class="font-semibold">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ auth()->user()->email }}
                    </p>
                </div>

                <span class="px-3 py-1 text-xs rounded-full
                {{ auth()->user()->role=='admin'
                ? 'bg-red-100 text-red-600'
                : 'bg-gray-100 text-gray-600' }}">
                    {{ auth()->user()->role }}
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        class="px-4 py-2 bg-black text-white rounded-xl hover:bg-gray-800">
                        Logout
                    </button>
                </form>

            </div>
        </div>
    </div>
</nav>


<div class="max-w-7xl mx-auto px-6 py-10">
<div class="flex gap-8">

<!-- ================= SIDEBAR ================= -->
<aside class="w-64 bg-white border rounded-2xl p-4">
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

@if($colocation->owner_id===auth()->id())
<button onclick="showSection(event,'categories')"
class="nav-btn w-full text-left px-4 py-3 rounded-xl text-gray-700 hover:bg-gray-100">
Categories
</button>
@endif
@endif

@if(auth()->user()->role==='admin')
<button onclick="showSection(event,'users')"
class="nav-btn w-full text-left px-4 py-3 rounded-xl text-gray-700 hover:bg-gray-100">
Users
</button>
@endif

</nav>
</aside>


<!-- ================= CONTENT ================= -->
<div class="flex-1">

@if(session('success'))
<div class="mb-6 p-4 bg-green-100 rounded-xl">
{{ session('success') }}
</div>
@endif


<!-- ================= COLOCATION ================= -->
<div id="colocation-section" class="section">

@if($colocation)

<div class="bg-white rounded-2xl p-8 shadow">

<h2 class="text-3xl font-bold">
{{ $colocation->num }}
</h2>

<p class="text-gray-600 mt-2">
{{ $colocation->description }}
</p>

<p class="text-sm text-gray-500">
Owner : {{ $colocation->owner->name }}
</p>

<hr class="my-6">

<h3 class="font-semibold mb-4">Members</h3>

@foreach($colocation->members as $member)
<div class="flex justify-between bg-gray-50 p-3 rounded-xl mb-2">

<span>{{ $member->name }}</span>

@if($colocation->owner_id===auth()->id())
<form method="POST"
action="{{ route('colocation.removeMember',[$colocation,$member->id]) }}">
@csrf
@method('DELETE')
<button class="text-red-500 text-sm">Remove</button>
</form>
@endif

</div>
@endforeach

</div>

@else

<div class="bg-white rounded-2xl p-8 max-w-md">

<h2 class="text-2xl font-bold mb-6">
Create Colocation
</h2>

<form method="POST" action="{{ route('colocation.store') }}">
@csrf

<input name="num"
placeholder="Number"
class="w-full mb-3 px-4 py-2 border rounded-xl"
required>

<textarea name="description"
placeholder="Description"
class="w-full mb-3 px-4 py-2 border rounded-xl"
required></textarea>

<button
class="w-full bg-black text-white py-2 rounded-xl">
Create
</button>

</form>

</div>

@endif
</div>

@if($colocation && $colocation->owner_id === auth()->id())
<div id="categories-section" class="section hidden">

<div class="bg-white rounded-2xl p-8 shadow">

<h2 class="text-2xl font-bold mb-6">
Categories
</h2>

<!-- ADD CATEGORY -->
<div class="bg-gray-50 border rounded-xl p-6 mb-6">

<h3 class="font-semibold mb-4">
Add New Category
</h3>

<form method="POST"
action="{{ route('category.store',$colocation) }}"
class="flex gap-4">

@csrf

<input
type="text"
name="name"
placeholder="Category name"
class="flex-1 px-4 py-2 border rounded-xl"
required>

<button
class="bg-black text-white px-6 rounded-xl">
Add
</button>

</form>

</div>


<!-- CATEGORY LIST -->
<div class="space-y-3">

@forelse($colocation->categories as $category)

<div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl">

<span class="font-medium">
{{ $category->name }}
</span>

<form method="POST"
action="{{ route('category.destroy',[$colocation,$category]) }}">
@csrf
@method('DELETE')

<button class="text-red-500 text-sm">
Delete
</button>

</form>

</div>

@empty

<p class="text-gray-500 text-center py-6">
No categories yet
</p>

@endforelse

</div>

</div>
</div>
@endif
<!-- ================= EXPENSES ================= -->
@if($colocation)
<div id="expenses-section" class="section hidden">

<div class="bg-white rounded-2xl p-8 shadow">

<h2 class="text-2xl font-bold mb-6">Expenses</h2>

<form method="POST"
action="{{ route('depense.store',$colocation) }}"
class="grid md:grid-cols-2 gap-4 mb-6">
@csrf

<input name="title" placeholder="Title"
class="border rounded-xl px-4 py-2" required>

<input type="number" step="0.01"
name="amount"
placeholder="Amount"
class="border rounded-xl px-4 py-2" required>

<input type="date"
name="date"
class="border rounded-xl px-4 py-2" required>

<select name="category_id"
class="border rounded-xl px-4 py-2">

<option value="">No category</option>

@foreach($colocation->categories as $category)
<option value="{{ $category->id }}">
{{ $category->name }}
</option>
@endforeach

</select>

<button
class="md:col-span-2 bg-black text-white py-2 rounded-xl">
Add Expense
</button>

</form>


@foreach($colocation->depenses as $depense)
<div class="flex justify-between bg-gray-50 p-4 rounded-xl mb-3">

<div>
<p class="font-semibold">
{{ $depense->title }}
</p>

<p class="text-sm text-gray-600">
{{ $depense->amount }} €
|
{{ $depense->payer->name }}
</p>
</div>

<form method="POST"
action="{{ route('depense.destroy',[$colocation,$depense]) }}">
@csrf
@method('DELETE')
<button class="text-red-500">Delete</button>
</form>

</div>
@endforeach

</div>
</div>
@endif


<!-- ================= USERS ================= -->
@if(auth()->user()->role==='admin')
<div id="users-section" class="section hidden">

<div class="bg-white rounded-2xl p-8 shadow">

<h2 class="text-2xl font-bold mb-6">
Users Management
</h2>

<table class="w-full">

@foreach($users as $user)
<tr class="border-b">
<td class="py-3">{{ $user->name }}</td>
<td>{{ $user->email }}</td>
<td>{{ $user->role }}</td>
</tr>
@endforeach

</table>

</div>
</div>
@endif


</div>
</div>
</div>


<!-- ================= JS ================= -->
<script>
function showSection(event,section){

document.querySelectorAll('.section')
.forEach(el=>el.classList.add('hidden'));

document.getElementById(section+'-section')
.classList.remove('hidden');

document.querySelectorAll('.nav-btn')
.forEach(btn=>{
btn.classList.remove('bg-black','text-white');
btn.classList.add('text-gray-700');
});

event.target.classList.add('bg-black','text-white');
}
</script>

</body>
</html>