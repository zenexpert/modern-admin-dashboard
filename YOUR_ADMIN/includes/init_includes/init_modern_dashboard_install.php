<?php
// -----
// Admin-level initialization script for the Modern Admin Dashboard plugin by ZenExpert.
// Copyright (C) 2026, ZenExpert
//
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

// -----
// Only install when an admin is logged in.
//
if (isset($_SESSION['admin_id'])) {
    // check if color code field exists, if not, add it
    if (!$sniffer->field_exists(TABLE_ORDERS_STATUS, 'orders_status_color_code')) {
        $db->Execute("ALTER TABLE " . TABLE_ORDERS_STATUS . " ADD orders_status_color_code VARCHAR(7) NULL DEFAULT NULL AFTER orders_status_name");
    }
}