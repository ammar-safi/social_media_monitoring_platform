# Policy Tests

This directory contains comprehensive tests for all application policies.

## Test Structure

- `BasePolicyTest.php` - Base test class with common setup and helper methods
- Individual policy test files for each policy class
- `AllPoliciesTest.php` - Meta test to ensure all policy tests work together

## Running the Tests

### Run all policy tests:
```bash
php artisan test tests/Feature/Policies/
```

### Run specific policy test:
```bash
php artisan test tests/Feature/Policies/UserPolicyTest.php
```

### Run with coverage:
```bash
php artisan test tests/Feature/Policies/ --coverage
```

## Test Users

Each test creates the following users:
- **Super Admin**: Has full access to everything (via Filament Spatie package)
- **Governmental Official**: Has specific permissions defined in MainRoles seeder
- **Policy Maker**: Has limited permissions defined in MainRoles seeder  
- **Regular User**: Has no specific role or permissions

## Known Issues Found

1. **HashtagPolicy Bug**: Uses inconsistent permission names
   - Uses `'update Hashtag'` instead of `'update hashtag'`
   - Uses `'delete Hashtag'` instead of `'delete hashtag'`

2. **Missing Permissions**: Some permissions in seeders don't match policy usage
   - "post" permissions created but not in main permissions array

3. **Inconsistent Naming**: Role names don't match user type enums exactly

## Test Coverage

✅ UserPolicy - Tests all methods, confirms Super Admin only access
✅ PostPolicy - Tests permission-based access for viewing
✅ AnalystPolicy - Tests permission-based access for viewing  
✅ RolePolicy - Tests Super Admin only access
✅ PermissionPolicy - Tests Super Admin only access
✅ RatingPolicy - Tests full CRUD with ownership rules
✅ HashtagPolicy - Tests CRUD with ownership (reveals permission name bugs)
✅ InvitePolicy - Tests CRUD with ownership and viewAny restrictions
✅ GovOrgPolicy - Tests view-only permissions
✅ ApproveUserPolicy - Tests complete restriction except reorder

## Recommendations

1. Fix permission name inconsistencies in HashtagPolicy
2. Add missing "post" to permissions array in PermissionSeeder
3. Consider adding a regular User role with basic permissions
4. Standardize role names to match user type enums