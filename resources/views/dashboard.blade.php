<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #0f172a;
            color: #fff;
        }

        nav {
            background: #1e293b;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav a {
            color: #3b82f6;
            text-decoration: none;
            margin-left: 15px;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .container {
            padding: 30px;
        }

        h1 {
            margin-bottom: 20px;
        }

        button {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.2s;
            color: white;
        }

        .ban-btn { background: #ef4444; }
        .ban-btn:hover { background: #b91c1c; }

        .unban-btn { background: #22c55e; }
        .unban-btn:hover { background: #16a34a; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background: #334155;
        }

        tr:nth-child(even) {
            background: #1e293b;
        }

        tr:nth-child(odd) {
            background: #0f172a;
        }
    </style>
</head>
<body>
<nav>
    <div>
        <span>Welcome, {{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
    </div>
    <div>
        @if(auth()->user()->role === 'admin')
            <a href="#" id="showUsersBtn">Users</a>
        @endif
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" style="background:#3b82f6; border-radius:5px;">Logout</button>
        </form>
           <form method="GET" action="{{ route('home') }}" style="display:inline;">
            @csrf
            <button type="submit" style="background:#3b82f6; border-radius:5px;">add colocation</button>
        </form>
    </div>
</nav>

<div class="container">

    <h1>Dashboard</h1>
    <p>Here you can manage your application.</p>

    @if(auth()->user()->role === 'admin')
        <div id="usersList" style="display:none;">
            <h2>Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        <td>{{ $user->is_banned ? 'Banned' : 'Active' }}</td>
                        <td>
                            <form method="POST" action="{{ route('users.toggleBan', $user->id) }}">
                                @csrf
                                <button type="submit" class="{{ $user->is_banned ? 'unban-btn' : 'ban-btn' }}">
                                    {{ $user->is_banned ? 'Unban' : 'Ban' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>

<script>
    const showBtn = document.getElementById('showUsersBtn');
    const usersDiv = document.getElementById('usersList');

    if(showBtn) {
        showBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (usersDiv.style.display === 'none') {
                usersDiv.style.display = 'block';
            } else {
                usersDiv.style.display = 'none';
            }
        });
    }
</script>