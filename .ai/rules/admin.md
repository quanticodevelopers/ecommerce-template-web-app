---
paths:
  - 'app/Models/User.php,app/Http/Controllers/Admin/CustomerController.php,tests/Feature/Admin/CustomerControllerTest.php'
---

# Admin

## Administrators also participate as customers
The customer context intentionally includes users with CUSTOMER or ADMIN roles because both share the same authenticatable User model and administrators may also act as customers. This relationship is one-way: CUSTOMER users do not gain admin access. Keep SUPER_ADMIN behavior unchanged unless the role policy is explicitly revised.
