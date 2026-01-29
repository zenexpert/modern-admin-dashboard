# Modern Zen Cart Admin Dashboard & Status Colors

A comprehensive UI/UX overhaul for the Zen Cart admin panel. This modification introduces a modern 2-tier header, drag-and-drop dashboard widgets, and a global Order Status Color system that adds visual cues (badges/pills) throughout the admin.

## Key Features

* **Order Status Colors:** Assign Hex colors to statuses directly in `Admin > Localization > Orders Status`.
* **Visual Badges:** Replaces plain text status names with colored pills in the Orders list and Dashboard widgets.
* **Modern Header:** A cleaner, responsive header with a dark top bar, distinct navigation menu, and quick search.
* **Drag-and-Drop Dashboard:** Widgets can be rearranged via drag-and-drop, with layouts saved automatically per database.
* **Dashboard "Cockpit":** The "Recent Orders" widget includes quick-filter pills for specific statuses (e.g., Pending, Processing).
* **Optimized Performance:** Uses a centralized lookup function (`zen_getOrdersStatuses`) to fetch colors globally, avoiding performance bottlenecks.

## Customizing the Dashboard
* **Drag & Drop:** Click and hold the header of any widget to move it.
* **Zones:**
    * **Main (Left):** Wide area optimized for charts or detailed tables.
    * **Sidebar (Right):** Narrow area optimized for quick stats or lists.
    * **Bottom:** Full-width area (auto-adjusts width based on widget type).
* **Saving:** Layouts are saved automatically to the database immediately upon drop.

## Site-Specific Overrides
You can customize the "Recent Orders" widget behavior by defining variables in `admin/includes/extra_datafiles/site_specific_admin_overrides.php`:

```php
<?php
// Hide the status filter pills in the widget header
$show_status_pills = false; 

// Change the number of rows displayed in Recent Orders widget
$recentOrdersMaxRows = 20;

// Default Status IDs to show in header pills (e.g., 1=Pending, 2=Processing)
$target_status_ids = [1, 2];
?>
```

## Compatibility
* **Zen Cart Version:** v2.1.0 out of the box (down to 1.5.8 with required manual merge of orders.php and includes/functions/general.php)
* **PHP Version:** 7.4 - 8.x
* **Dependencies:** jQuery UI, chart.js

##  License
Portions Copyright 2003-2026 Zen Cart Development Team.
Released under the **GNU Public License V2.0**.

## Screenshot

![screenshot](https://github.com/user-attachments/assets/64f0bd7b-925a-4d3a-b28c-04bd16597af1)
