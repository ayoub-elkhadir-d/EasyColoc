# Member Departure Tracking - Testing Guide

## Features Implemented

### 1. Left_at Column in Colocation_User Pivot Table
- **Purpose**: Track when members leave a colocation
- **Type**: Timestamp (nullable)
- **Location**: `colocation_user` pivot table

### 2. Leave Colocation with Reputation Logic
- **Debt Calculation**: Automatically calculates unpaid balances
- **Reputation Penalty**: 
  - 5 points deducted if leaving with debt
  - No penalty if no debt
- **Departure Tracking**: Records timestamp in `left_at` column

### 3. Debt Calculation on Departure
- **Method**: `calculateMemberDebt($userId)` in Colocation model
- **Calculation**: Sums all unpaid balances for the member
- **Display**: Shows warning before leaving if debt exists

## Testing Steps

### Test 1: Leave Without Debt
1. Login as a member (not owner)
2. Navigate to "My Colocation"
3. Verify green message: "You have no unpaid debts"
4. Click "Leave Colocation"
5. Confirm the action
6. **Expected Result**: 
   - Success message: "You left the colocation"
   - No reputation penalty
   - `left_at` timestamp recorded in database

### Test 2: Leave With Debt
1. Login as a member
2. Have another member create an expense
3. Don't mark your share as paid
4. Navigate to "My Colocation"
5. Verify yellow warning with debt amount
6. Click "Leave Colocation"
7. Confirm the action
8. **Expected Result**:
   - Success message: "You left the colocation with an unpaid debt of X€. Your reputation decreased by 5 points."
   - Reputation decreased by 5 points
   - `left_at` timestamp recorded in database

### Test 3: Verify Database Changes
```sql
-- Check left_at column exists
DESCRIBE colocation_user;

-- Check reputation column exists
DESCRIBE users;

-- View member departure records
SELECT * FROM colocation_user WHERE left_at IS NOT NULL;

-- Check user reputation
SELECT id, name, reputation FROM users;
```

### Test 4: Debt Calculation Accuracy
1. Create multiple expenses in a colocation
2. Mark some as paid, leave others unpaid
3. Check the debt amount displayed
4. **Expected Result**: Debt = Sum of all unpaid balance amounts for the user

## Database Queries for Verification

### Check Member Departure History
```sql
SELECT 
    cu.id,
    u.name as member_name,
    c.num as colocation_num,
    cu.left_at,
    u.reputation
FROM colocation_user cu
JOIN users u ON cu.user_id = u.id
JOIN colocations c ON cu.colocation_id = c.id
WHERE cu.left_at IS NOT NULL
ORDER BY cu.left_at DESC;
```

### Check Unpaid Debts
```sql
SELECT 
    u.name,
    SUM(b.amount) as total_debt
FROM balances b
JOIN users u ON b.user_id = u.id
WHERE b.is_paid = 0
GROUP BY u.id, u.name;
```

### Check Reputation Changes
```sql
SELECT 
    id,
    name,
    reputation,
    created_at
FROM users
ORDER BY reputation ASC;
```

## Edge Cases to Test

1. **Member tries to leave when not a member**: Should show error
2. **Owner tries to leave**: Should use cancel functionality instead
3. **Member leaves multiple times**: Should update existing record
4. **Debt calculation with no expenses**: Should show 0 debt
5. **Debt calculation with all paid expenses**: Should show 0 debt

## UI Elements to Verify

### For Members (Non-Owners)
- [ ] "Your Membership" section visible
- [ ] Debt status card displays (green or yellow)
- [ ] Debt amount shows correctly
- [ ] Warning message for debt is clear
- [ ] Leave button is visible
- [ ] Confirmation dialog appears

### For Owners
- [ ] Cancel button visible (not leave button)
- [ ] Status badge shows correctly
- [ ] Members list displays

## Success Criteria

✅ Migration runs without errors
✅ `left_at` column added to `colocation_user` table
✅ Debt calculation returns correct amount
✅ Reputation decreases by 5 when leaving with debt
✅ No reputation penalty when leaving without debt
✅ `left_at` timestamp records correctly
✅ UI shows appropriate warnings
✅ Success messages display correctly
✅ Confirmation dialogs work

## Rollback Instructions

If needed, rollback the migration:
```bash
php artisan migrate:rollback --step=1
```

This will remove the `left_at` column from `colocation_user` table.
