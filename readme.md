This inventory management plugin aims to empower wholesalers, shop owners, distributors, and stock managers by providing a comprehensive toolset for managing commodities efficiently.

## Where to manage inventory and place orders

After activation, use the WordPress admin menu:

- **Inventory Manager → Manage Inventory**
  - Opens the frontend workspace page (`/seller-account/`) used for inventory operations.
- **Inventory Manager → Place Orders**
  - Opens the frontend orders page (`/orders/`) used for order workflows.
- **Inventory Settings**
  - Opens Redux-powered settings (`/wp-admin/admin.php?page=apgi_options`).

If these frontend pages are missing, deactivate and reactivate the plugin once to regenerate required pages.

## Redux Framework requirement

This plugin uses Redux Framework for all settings and admin configuration screens.

### Option 1 (recommended): install as a normal WordPress plugin
1. Go to **WP Admin → Plugins → Add New**.
2. Search for **Redux Framework**.
3. Install and activate it.
4. Open: `WP Admin → Inventory Settings`.

### Option 2: bundle Redux inside this plugin
If you prefer shipping Redux with this plugin repository, place Redux files in one of these paths:
- `vendor/redux-framework/redux-framework/redux-framework.php`
- `vendor/redux-framework/redux-core/framework.php`
- `lib/redux-framework/redux-framework.php`
- `lib/redux-framework/redux-core/framework.php`

The plugin auto-detects and loads Redux from these locations.
