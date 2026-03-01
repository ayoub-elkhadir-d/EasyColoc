# Reputation System Implementation Summary

## ✅ Completed Features

### 1. Reputation Column in Users Table
- **Status**: Already exists from previous implementation
- **Migration**: `2026_03_01_041901_add_reputation_to_users_table.php`
- **Default Value**: 100 points
- **Type**: Integer

### 2. +1/-1 Reputation on Leave/Cancel Based on Debt
- **Leave with debt**: -1 reputation point
- **Leave without debt**: +1 reputation point
- **Cancel colocation**: -1 reputation point

### 3. Display Reputation in Member List
- **Location**: Members section in colocation view
- **Display**: Star emoji (⭐) with reputation score
- **Color Coding**:
  - 🟢 Green badge: ≥ 100 points (good standing)
  - 🟡 Yellow badge: 50-99 points (warning)
  - 🔴 Red badge: < 50 points (poor standing)

## Files Modified

### 1. User Model (`app/Models/User.php`)
**Added**:
```php
public function incrementReputation(int $points): void
{
    $this->increment('reputation', $points);
}
```

### 2. Colocation Model (`app/Models/colocation.php`)
**Updated**:
```php
public function cancel()
{
    $this->update(['status' => 'cancelled']);
    $this->owner->decrementReputation(1); // Changed from 10 to 1
}
```

### 3. ColocationController (`app/Http/Controllers/ColocationController.php`)
**Updated leave() method**:
```php
if ($debt > 0) {
    auth()->user()->decrementReputation(1); // -1 for debt
    return redirect()->route('home')->with('success', "You left the colocation with an unpaid debt of {$debt}€. Your reputation decreased by 1 point.");
}

auth()->user()->incrementReputation(1); // +1 for clean leave
return redirect()->route('home')->with('success', 'You left the colocation. Your reputation increased by 1 point.');
```

**Updated cancel() method**:
- Message changed to reflect -1 reputation penalty

### 4. Home View (`resources/views/home.blade.php`)
**Members List**:
```blade
<div>
    <span class="font-medium">{{ $member->name }}</span>
    <span class="ml-2 text-xs px-2 py-1 rounded-full {{ $member->reputation >= 100 ? 'bg-green-100 text-green-800' : ($member->reputation >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
        ⭐ {{ $member->reputation }}
    </span>
</div>
```

**Updated Messages**:
- Cancel confirmation: "This will decrease your reputation by 1 point"
- Leave with debt warning: "Leaving with debt will decrease your reputation by 1 point"
- Leave without debt: "Leaving will increase your reputation by 1 point"

## Reputation Logic

### Earning Points (+1)
- ✅ Leave colocation with all debts paid
- Clean departure shows responsibility

### Losing Points (-1)
- ❌ Leave colocation with unpaid debts
- ❌ Cancel colocation as owner
- Shows irresponsible behavior

### Starting Points
- 🎯 All new users start with 100 reputation points
- Provides buffer for mistakes
- Encourages responsible behavior

## Visual Indicators

### Badge Colors
```
reputation >= 100  → 🟢 Green  (Excellent)
reputation 50-99   → 🟡 Yellow (Warning)
reputation < 50    → 🔴 Red    (Poor)
```

### Display Format
```
John Doe ⭐ 105
Jane Smith ⭐ 85
Bob Wilson ⭐ 45
```

## User Experience Flow

### Scenario 1: Responsible Member Leaves
1. Member has no unpaid debts
2. Green message: "Leaving will increase your reputation by 1 point"
3. Clicks "Leave Colocation"
4. Reputation: 100 → 101
5. Success: "You left the colocation. Your reputation increased by 1 point."

### Scenario 2: Member Leaves with Debt
1. Member has unpaid debts (e.g., 25€)
2. Yellow warning: "You have an unpaid debt of 25.00€. Leaving with debt will decrease your reputation by 1 point."
3. Clicks "Leave Colocation"
4. Reputation: 100 → 99
5. Success: "You left the colocation with an unpaid debt of 25€. Your reputation decreased by 1 point."

### Scenario 3: Owner Cancels Colocation
1. Owner clicks "Cancel Colocation"
2. Confirmation: "Are you sure? This will decrease your reputation by 1 point."
3. Confirms action
4. Reputation: 100 → 99
5. Success: "Colocation cancelled. Your reputation decreased by 1 point."

## Benefits

### For Users
- ✅ Clear feedback on reputation changes
- ✅ Visual reputation display for all members
- ✅ Incentive to pay debts before leaving
- ✅ Fair +1/-1 system (not too harsh)

### For Owners
- ✅ Can see member reputation at a glance
- ✅ Make informed decisions about invitations
- ✅ Identify responsible vs irresponsible members

### For System
- ✅ Simple, balanced reputation system
- ✅ Encourages responsible behavior
- ✅ Discourages debt accumulation
- ✅ Easy to understand and implement

## Testing Checklist

- [x] Reputation column exists in users table
- [x] incrementReputation() method works
- [x] decrementReputation() method works
- [x] Leave with debt: -1 reputation
- [x] Leave without debt: +1 reputation
- [x] Cancel colocation: -1 reputation
- [x] Reputation displays in member list
- [x] Color coding works correctly
- [x] Messages reflect new +1/-1 system
- [x] Confirmation dialogs updated

## Future Enhancements

1. **Reputation History**: Track all reputation changes with timestamps
2. **Reputation Rewards**: Bonus points for consistent good behavior
3. **Reputation Restrictions**: Minimum reputation to join colocations
4. **Reputation Recovery**: Special actions to regain lost reputation
5. **Leaderboard**: Display top members by reputation
6. **Badges**: Achievement badges for reputation milestones
7. **Notifications**: Alert users when reputation changes
8. **Profile Display**: Show reputation on user profile page

## Database Queries

### Check User Reputation
```sql
SELECT id, name, reputation 
FROM users 
ORDER BY reputation DESC;
```

### Find Low Reputation Users
```sql
SELECT id, name, reputation 
FROM users 
WHERE reputation < 50;
```

### Average Reputation
```sql
SELECT AVG(reputation) as avg_reputation 
FROM users;
```

## Conclusion

The reputation system is now fully functional with:
- ✅ Simple +1/-1 point system
- ✅ Visual display with color-coded badges
- ✅ Clear user feedback
- ✅ Balanced incentives for responsible behavior

All features are working as expected! 🎉
