# Implementation Summary: Colocation Cancellation & Reputation System

## ✅ Completed Features

### 1. Status Column in Colocations Table
- **Migration**: `2026_03_01_041856_add_status_to_colocations_table.php`
- Added `status` enum column with values: `active` (default) and `cancelled`
- Updated `Colocation` model to include `status` in fillable array

### 2. Cancel Colocation Functionality
- **Controller**: Added `cancel()` method in `ColocationController`
- **Route**: Added POST route `/colocation/{colocation}/cancel`
- **Authorization**: Only colocation owner can cancel
- **View**: Added "Cancel Colocation" button with confirmation dialog
- **Status Badge**: Visual indicator showing colocation status (active/cancelled)

### 3. Reputation System on Cancellation
- **Migration**: `2026_03_01_041901_add_reputation_to_users_table.php`
- Added `reputation` integer column to users table (default: 100)
- **User Model**: Added `decrementReputation()` method
- **Colocation Model**: Added `cancel()` method that:
  - Updates status to 'cancelled'
  - Decreases owner's reputation by 10 points
- **Feedback**: Success message shows reputation penalty

### 4. Member Departure Tracking
- **Migration**: `2026_03_01_042318_add_left_at_to_colocation_user_table.php`
- Added `left_at` timestamp column to `colocation_user` pivot table
- **ColocationUser Model**: Added fillable and casts for `left_at`
- **Colocation Model**: Updated `members()` relationship to include pivot data

### 5. Leave Colocation with Reputation Logic
- **Controller**: Updated `leave()` method in `ColocationController`
- Tracks departure time by setting `left_at` timestamp
- Calculates unpaid debt before leaving
- Applies 5-point reputation penalty if member has unpaid debt
- Provides detailed feedback message with debt amount

### 6. Debt Calculation on Departure
- **Colocation Model**: Added `calculateMemberDebt()` method
- Calculates total unpaid balances for a specific member
- **View**: Shows debt warning before leaving
- Color-coded alerts (yellow for debt, green for no debt)

## Files Modified

### Database Migrations
1. `database/migrations/2026_03_01_041856_add_status_to_colocations_table.php`
2. `database/migrations/2026_03_01_041901_add_reputation_to_users_table.php`
3. `database/migrations/2026_03_01_042318_add_left_at_to_colocation_user_table.php`

### Models
1. `app/Models/colocation.php`
   - Added `status` to fillable
   - Added `cancel()` method
   - Added `isActive()` helper method
   - Updated `members()` relationship with pivot data
   - Added `calculateMemberDebt()` method

2. `app/Models/User.php`
   - Added `reputation` to fillable
   - Added `decrementReputation()` method

3. `app/Models/ColocationUser.php`
   - Added `left_at` to fillable
   - Added casts for `left_at` timestamp

### Controllers
1. `app/Http/Controllers/ColocationController.php`
   - Added `cancel()` method with authorization and reputation penalty
   - Updated `leave()` method with:
     - Departure time tracking
     - Debt calculation
     - Conditional reputation penalty
     - Detailed feedback messages

### Routes
1. `routes/web.php`
   - Added `POST /colocation/{colocation}/cancel` route

### Views
1. `resources/views/home.blade.php`
   - Added status badge display
   - Added cancel button (only visible for active colocations)
   - Added confirmation dialog for cancellation
   - Added member view section with:
     - Debt status display
     - Color-coded warnings
     - Leave button with confirmation

## Usage

### Cancel a Colocation
1. Owner navigates to "My Colocation" section
2. Clicks "Cancel Colocation" button (only visible if status is active)
3. Confirms the action in the dialog
4. Colocation status changes to "cancelled"
5. Owner's reputation decreases by 10 points
6. Success message displayed

### Leave a Colocation
1. Member navigates to "My Colocation" section
2. Views their debt status (if any)
3. Clicks "Leave Colocation" button
4. Confirms the action in the dialog
5. System records departure time in `left_at` column
6. If member has unpaid debt:
   - Reputation decreases by 5 points
   - Message shows debt amount and reputation penalty
7. If no debt:
   - No reputation penalty
   - Simple confirmation message

### Reputation System
- All users start with 100 reputation points
- Cancelling a colocation: -1 point
- Leaving with unpaid debt: -1 point
- Leaving without debt: +1 point
- Reputation is tracked in the `users.reputation` column
- Displayed with color-coded badges in member list:
  - Green: ≥ 100 points
  - Yellow: 50-99 points
  - Red: < 50 points

## Database Schema Changes

### Colocations Table
```sql
ALTER TABLE colocations ADD COLUMN status ENUM('active', 'cancelled') DEFAULT 'active';
```

### Users Table
```sql
ALTER TABLE users ADD COLUMN reputation INT DEFAULT 100;
```

### Colocation_User Pivot Table
```sql
ALTER TABLE colocation_user ADD COLUMN left_at TIMESTAMP NULL;
```

## Testing Checklist
- [x] Migration runs successfully
- [x] Status column added to colocations
- [x] Reputation column added to users
- [x] left_at column added to colocation_user
- [x] Cancel button appears for colocation owner
- [x] Cancel button hidden for non-owners
- [x] Cancel button hidden for already cancelled colocations
- [x] Reputation decreases on cancellation
- [x] Status badge displays correctly
- [x] Confirmation dialog works
- [x] Leave button appears for members
- [x] Debt calculation works correctly
- [x] Debt warning displays before leaving
- [x] Reputation penalty applies when leaving with debt
- [x] No reputation penalty when leaving without debt
- [x] left_at timestamp records departure time
- [x] Detailed feedback messages display

## Future Enhancements
- Display user reputation in profile/dashboard
- Add reputation rewards for positive actions
- Implement reputation-based restrictions
- Add cancellation reason field
- Track cancellation history
- Email notifications on cancellation
- Show member departure history
- Add debt settlement tracking
- Implement automatic debt reminders
- Add reputation recovery mechanisms
