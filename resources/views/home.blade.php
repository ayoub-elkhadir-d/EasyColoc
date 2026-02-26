<!DOCTYPE html>
<html>
<head>
    <title>Colocation</title>
</head>
<body>
    <h1>My Colocation</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <div style="color: red;">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if($colocation)
        <h2>{{ $colocation->num }}</h2>
        <p>{{ $colocation->description }}</p>
        <p>Owner: {{ $colocation->owner->name }}</p>

        @if($colocation->owner_id === auth()->id())
            <h3>Update Colocation</h3>
            <form method="POST" action="{{ route('colocation.update', $colocation) }}">
                @csrf
                @method('PUT')
                <input type="text" name="num" value="{{ $colocation->num }}" required>
                <textarea name="description" required>{{ $colocation->description }}</textarea>
                <button type="submit">Update</button>
            </form>

            <form method="POST" action="{{ route('colocation.destroy', $colocation) }}">
                @csrf
                @method('DELETE')
                <button type="submit">Delete Colocation</button>
            </form>

            <h3>Send Invitation</h3>
            <form method="POST" action="{{ route('invitation.send', $colocation) }}">
                @csrf
                <input type="email" name="email" placeholder="Email" required>
                <button type="submit">Send Invitation</button>
            </form>

            <h3>Members</h3>
            <ul>
                @foreach($colocation->members as $member)
                    <li>
                        {{ $member->name }}
                        <form method="POST" action="{{ route('colocation.removeMember', [$colocation, $member->id]) }}" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Remove</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @else
            <form method="POST" action="{{ route('colocation.leave', $colocation) }}">
                @csrf
                <button type="submit">Leave Colocation</button>
            </form>
        @endif
    @else
        <h3>Create New Colocation</h3>
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
    @endif

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>
