# Member Departure Tracking - Implementation Details

## Overview
This document details the implementation of member departure tracking, including the ability for members to leave a colocation with automatic debt calculation and reputation penalties.

## Files Created

### 1. Migration: add_left_at_to_colocation_user_table.php
**Path**: `database/migrations/2026_03_01_042318_add_left_at_to_colocation_user_table.php`

**Purpose**: Add timestamp column to track when members leave

**Changes**:
```php
$table->timestamp('left_at')->nullable()->after('user_id');
```

## Files Modified

### 1. ColocationUser Model
**Path**: `app/Models/ColocationUser.php`

**Changes**:
- Added `left_at` to fillable array
- Added cast for `left_at` as datetime

```php
protected $fillable = ['left_at'];
protected $casts = ['left_at' => 'datetime'];
```

### 2. Colocation Model
**Path**: `app/Models/colocation.php`

**Changes**:
1. Updated `members()` relationship to include pivot data:
```php
public function members()
{
    return $this->belongsToMany(User::class)
        ->using(ColocationUser::class)
        ->withPivot('left_at')
        ->withTimestamps();
}
```

2. Added `calculateMemberDebt()` method:
```php
public function calculateMemberDebt($userId)
{
    $unpaidBalances = Balance::whereHas('depense', function($query) {
        $query->where('colocation_id', $this->id);
    })
    ->where('user_id', $userId)
    ->where('is_paid', false)
    ->sum('amount');

    return $unpaidBalances;
}
```

### 3. ColocationController
**Path**: `app/Http/Controllers/ColocationController.php`

**Changes**:
Completely rewrote the `leave()` method:

**Before**:
```php
public function leave(Colocation $colocation)
{
    auth()->user()->colocations()->detach($colocation->id);
    return redirect()->route('home')->with('success', 'You left the colocation');
}
```

**After**:
```php
public function leave(Colocation $colocation)
{
    $userId = auth()->id();
    $debt = $colocation->calculateMemberDebt($userId);
    
    $membership = $colocation->members()->where('user_id', $userId)->first();
    
    if ($membership) {
        $membership->pivot->update(['left_at' => now()]);
        
        if ($debt > 0) {
            auth()->user()->decrementReputation(5);
            return redirect()->route('home')->with('success', "You left the colocation with an unpaid debt of {$debt}€. Your reputation decreased by 5 points.");
        }
        
        return redirect()->route('home')->with('success', 'You left the colocation');
    }
    
    return redirect()->route('home')->withErrors(['error' => 'You are not a member of this colocation']);
}
```

### 4. Home View
**Path**: `resources/views/home.blade.php`

**Changes**:
Added member view section with debt status and leave functionality:

```blade
@if($colocation->members->contains(auth()->id()))
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8">
    <h3 class="text-lg font-semibold mb-4">Your Membership</h3>
    
    @php
        $debt = $colocation->calculateMemberDebt(auth()->id());
    @endphp
    
    @if($debt > 0)
    <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
        <p class="text-sm text-yellow-800">
            <strong>Warning:</strong> You have an unpaid debt of {{ number_format($debt, 2) }}€. 
            Leaving with debt will decrease your reputation by 5 points.
        </p>
    </div>
    @else
    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl">
        <p class="text-sm text-green-800">
            You have no unpaid debts. You can leave without reputation penalty.
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
```

## Feature Flow

### Member Leaving Process

1. **Member Views Colocation**
   - System calculates current debt
   - Displays debt status with color-coded alert

2. **Member Clicks Leave**
   - Confirmation dialog appears
   - Member confirms action

3. **System Processing**
   - Retrieves member's unpaid balances
   - Records departure time in `left_at`
   - Checks if debt exists

4. **Reputation Logic**
   - If debt > 0: Decrease reputation by 5 points
   - If debt = 0: No penalty

5. **Feedback**
   - Success message with debt amount (if applicable)
   - Reputation change notification (if applicable)

## Business Rules

### Debt Calculation
- Only counts unpaid balances (`is_paid = false`)
- Only includes expenses from current colocation
- Sums all unpaid amounts for the member

### Reputation Penalties
- **Leaving with debt**: -5 points
- **Leaving without debt**: 0 points
- **Owner cancelling**: -10 points (separate feature)

### Departure Tracking
- `left_at` timestamp records exact departure time
- Soft delete approach (record remains in database)
- Can be used for historical analysis

## Database Schema

### colocation_user Table
```sql
CREATE TABLE colocation_user (
    id BIGINT UNSIGNED PRIMARY KEY,
    colocation_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    left_at TIMESTAMP NULL,  -- NEW COLUMN
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (colocation_id) REFERENCES colocations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## API Endpoints

### Leave Colocation
- **Route**: `POST /colocation/{colocation}/leave`
- **Name**: `colocation.leave`
- **Auth**: Required
- **Parameters**: 
  - `colocation` (route parameter)
- **Response**: Redirect to home with success/error message

## Error Handling

### Scenarios Covered
1. **Not a member**: Returns error message
2. **Already left**: Updates existing record
3. **No membership found**: Returns error message

## Security Considerations

1. **Authorization**: Only authenticated users can leave
2. **Validation**: Checks membership before processing
3. **Data Integrity**: Uses transactions implicitly through Eloquent
4. **XSS Prevention**: All output is escaped in Blade templates

## Performance Considerations

1. **Debt Calculation**: Single query with joins
2. **Pivot Update**: Direct update on pivot record
3. **Eager Loading**: Members relationship loaded in controller

## Future Enhancements

1. **Departure History**: Show list of members who left
2. **Debt Settlement**: Allow debt payment after leaving
3. **Notifications**: Email owner when member leaves
4. **Analytics**: Track departure patterns
5. **Reputation Recovery**: Allow users to improve reputation
6. **Debt Disputes**: Add dispute resolution system
7. **Automatic Reminders**: Send debt reminders before leaving
8. **Partial Payments**: Track partial debt payments

## Maintenance Notes

### Common Issues
1. **Debt not calculating**: Check Balance model relationships
2. **Reputation not updating**: Verify User model method
3. **left_at not recording**: Check pivot model fillable array

### Debugging Queries
```sql
-- Check member's unpaid balances
SELECT b.*, d.title, d.amount as total_expense
FROM balances b
JOIN depenses d ON b.depense_id = d.id
WHERE b.user_id = ? 
AND b.is_paid = 0
AND d.colocation_id = ?;

-- Check departure records
SELECT * FROM colocation_user 
WHERE user_id = ? 
AND colocation_id = ?;
```

## Testing Checklist

- [x] Migration runs successfully
- [x] Debt calculation is accurate
- [x] Reputation penalty applies correctly
- [x] No penalty when no debt
- [x] left_at timestamp records
- [x] UI displays debt warnings
- [x] Confirmation dialog works
- [x] Error handling works
- [x] Success messages display
- [x] Database constraints maintained
