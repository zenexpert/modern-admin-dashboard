<?php
/**
 * @copyright Copyright 2003-2025 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version Modern Dynamic Dashboard 2026
 * @author ZenExpert - https://zenexpert.com
 */

if (!zen_is_superuser() && !check_page(FILENAME_ORDERS, '')) return;

// to disable this module for everyone, uncomment the following "return" statement so the rest of this file is ignored
// return;

// Configure settings
// To override the $includeAttributesInPopoverRows or $recentOrdersMaxRows
// values, see
// https://docs.zen-cart.com/user/admin/site_specific_overrides/
$includeAttributesInPopoverRows = $includeAttributesInPopoverRows ?? true;
$maxRows = $recentOrdersMaxRows ?? 10; // default to 10 for a cleaner dashboard

// define orders statuses to show in top bar
$show_status_pills = $show_status_pills ?? true;
if(!isset($target_status_ids)) {
    $target_status_ids = [1, 2]; // pending and processing
}
$currencies ??= new currencies();

// prepare data
$status_color_code = '';
if($sniffer->field_exists(TABLE_ORDERS_STATUS, 'orders_status_color_code')) {
    $status_color_code = ', s.orders_status_color_code';
}
$sql = "SELECT o.orders_id, o.customers_name, o.customers_id, o.date_purchased,
               o.currency, o.currency_value, o.orders_status,
               ot.text as order_total, ot.value as order_value,
               s.orders_status_name $status_color_code
        FROM " . TABLE_ORDERS . " o
        LEFT JOIN " . TABLE_ORDERS_TOTAL . " ot ON (o.orders_id = ot.orders_id AND ot.class = 'ot_total')
        LEFT JOIN " . TABLE_ORDERS_STATUS . " s ON (o.orders_status = s.orders_status_id AND s.language_id = " . (int)$_SESSION['languages_id'] . ")
        ORDER BY o.orders_id DESC";
$orders = $db->Execute($sql, (int)$maxRows, true, 1800);

$orders = $db->Execute($sql, (int)$maxRows, true, 1800);

// get status metadata (name, color)
if($sniffer->field_exists(TABLE_ORDERS_STATUS, 'orders_status_color_code')) {
    $status_meta = [];
    if (!empty($target_status_ids)) {
        $ids_str = implode(',', $target_status_ids);
        $sql_meta = "SELECT orders_status_id, orders_status_name, orders_status_color_code
                 FROM " . TABLE_ORDERS_STATUS . "
                 WHERE language_id = " . (int)$_SESSION['languages_id'] . "
                 AND orders_status_id IN (" . $ids_str . ")";
        $result_meta = $db->Execute($sql_meta);

        while (!$result_meta->EOF) {
            $id = $result_meta->fields['orders_status_id'];
            $status_meta[$id] = [
                'name' => $result_meta->fields['orders_status_name'],
                'color' => $result_meta->fields['orders_status_color_code']
            ];
            $result_meta->MoveNext();
        }
    }
}

if($show_status_pills) {
    $status_counts = [];
    // pre-fill with 0 to ensure badges show even if count is 0
    foreach ($target_status_ids as $tid) $status_counts[$tid] = 0;
    if (!empty($target_status_ids)) {
        $ids_str = implode(',', $target_status_ids);
        $sql_stats = "SELECT orders_status, count(*) as total
                      FROM " . TABLE_ORDERS . "
                      WHERE orders_status IN (" . $ids_str . ")
                      GROUP BY orders_status";
        $result_stats = $db->Execute($sql_stats);
        while (!$result_stats->EOF) {
            $status_counts[$result_stats->fields['orders_status']] = $result_stats->fields['total'];
            $result_stats->MoveNext();
        }
    }
}
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-2 hidden-xs">
                <i class="fa fa-list-alt"></i> <?php echo BOX_ENTRY_NEW_ORDERS; ?>
            </div>
            <div class="col-xs-12 col-md-8 mb-2 text-center status-pills">
                <?php
                if($show_status_pills) {
                foreach ($target_status_ids as $sID) {
                    $count = $status_counts[$sID];
                    $name  = isset($status_meta[$sID]) ? $status_meta[$sID]['name'] : zen_get_orders_status_name($sID);
                    $customColor = isset($status_meta[$sID]) ? $status_meta[$sID]['color'] : null;

                    // determine style
                    $inlineStyle = '';
                    $badgeClass  = 'label-default';

                    if (!empty($customColor)) {
                        // custom color badge
                        $badgeClass  = 'label';
                        $inlineStyle = 'background-color: ' . $customColor . '; border-color: ' . $customColor . '; color: #fff;';
                    } else {
                        // fallback Bootstrap colors
                        switch ($sID) {
                            case 1: $badgeClass = 'label-warning'; break; // Pending
                            case 2: $badgeClass = 'label-info'; break;    // Processing
                            case 3: $badgeClass = 'label-success'; break; // Delivered
                            default: $badgeClass = 'label-default';
                        }
                    }

                    // fade out if count is 0
                    $opacity = ($count == 0) ? 'opacity: 0.5;' : '';
                    ?>
                    <a href="<?php echo zen_href_link(FILENAME_ORDERS, 'statusFilterSelect=' . $sID); ?>">
                        <span class="label <?php echo $badgeClass; ?>" style="<?php echo $inlineStyle . $opacity; ?>">
                            <?php echo $name; ?>: <strong><?php echo $count; ?></strong>
                        </span>
                    </a>
                <?php }
                } ?>
            </div>
            <div class="col-xs-12 col-md-2 text-right">
                <a href="<?php echo zen_href_link(FILENAME_ORDERS); ?>" class="btn btn-xs btn-default">
                    <?php echo BOX_ENTRY_VIEW_ALL; ?> <i class="fa fa-angle-double-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="table-responsive recent-orders">
        <table class="table table-hover table-striped mb-0">
            <thead>
            <tr>
                <th><?php echo BOX_ORDERS_ID; ?></th>
                <th><?php echo BOX_ORDERS_CUSTOMER; ?></th>
                <th><?php echo BOX_ORDERS_STATUS; ?></th>
                <th class="text-right"><?php echo BOX_ORDERS_DATE; ?></th>
                <th class="text-right"><?php echo DASHBOARD_TOTAL; ?></th>
                <th class="text-right" style="width: 150px;"><?php echo BOX_ORDERS_ACTIONS; ?></th>
            </tr>
            </thead>
            <tbody>
                <?php
                foreach ($orders as $order) {

                    // prepare data
                    $order['customers_name'] = str_replace('N/A', '', $order['customers_name']);
                    $oID = $order['orders_id'];
                    $name = zen_output_string_protected($order['customers_name']);
		            $date = zen_date_short($orders->fields['date_purchased']);
                    $statusName = $order['orders_status_name'];
                    $statusId = (int)$order['orders_status'];
                    $customColor = $order['orders_status_color_code'] ?? '';
                    $amt = $currencies->format($orders->fields['order_value'], false);
                    if ($orders->fields['currency'] != DEFAULT_CURRENCY) {
                        $amt .= '<br><small class="text-muted">(' . $orders->fields['order_total'] . ')</small>';
                    }

                    $sql = "SELECT op.orders_products_id, op.products_quantity AS qty, op.products_name AS name, op.products_model AS model
                            FROM " . TABLE_ORDERS_PRODUCTS . " op
                            WHERE op.orders_id = " . (int)$oID;

                    $orderProducts = $db->Execute($sql, false, true, 1800);
                    $product_details = '';

                    foreach($orderProducts as $product) {
                        $product_details .= $product['qty'] . ' x ' . $product['name'] . (!empty($product['model']) ? ' (' . $product['model'] . ')' :''). "\n";

                        if ($includeAttributesInPopoverRows) {
                            $sql = "SELECT products_options, products_options_values
                                    FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . "
                                    WHERE orders_products_id = " . (int)$product['orders_products_id'] . " ORDER BY orders_products_attributes_id ASC";
                            $productAttributes = $db->Execute($sql, false, true, 1800);
                            foreach ($productAttributes as $attr) {
                                if (!empty($attr['products_options'])) {
                                    $product_details .= '&nbsp;&nbsp;- ' . $attr['products_options'] . ': ' . zen_output_string_protected($attr['products_options_values']) . "\n";
                                }
                            }
                        }
                        $product_details .= '<hr>';
                    }
                    $product_details = rtrim($product_details);
                    $product_details = preg_replace('~<hr>$~', '', $product_details);
                    $product_details = nl2br($product_details);

                    $inlineStyle = '';
                    $lblClass = 'label-default';

                    if (!empty($customColor)) {
                        $lblClass = 'label';
                        $inlineStyle = 'background-color: ' . $customColor . '; color: #fff;';
                    } else {
                        switch ($statusId) {
                            case 1: $lblClass = 'label-warning'; break;
                            case 2: $lblClass = 'label-info'; break;
                            case 3: $lblClass = 'label-success'; break;
                            default: $lblClass = 'label-default';
                        }
                    }
                ?>
                <tr>
                    <td><strong>#<?php echo $oID; ?></strong></td>

                    <td><a href="<?php echo zen_href_link(FILENAME_ORDERS, 'oID=' . $oID . '&action=edit'); ?>" style="font-weight:600; color:#555;"><?php echo $name; ?></a></td>

                    <td>
                        <span class="label <?php echo $lblClass; ?>" style="<?php echo $inlineStyle; ?>">
                            <?php echo $statusName; ?>
                        </span>
                    </td>

		    <td class="text-right">
                        <?php echo $date; ?>
                    </td>

                    <td class="text-right"><strong><?php echo $amt; ?></strong></td>

                    <td class="text-right">
                        <button tabindex="0" class="btn btn-xs btn-info orderProductsPopover" role="button"
                                data-toggle="popover"
                                data-trigger="focus"
                                data-placement="left"
                                data-html="true"
                                title="<?php echo TEXT_PRODUCT_POPUP_TITLE ?? 'Products'; ?>"
                                data-content="<?php echo zen_output_string($product_details, array('"' => '&quot;', "'" => '&#39;', '<br />' => '<br>')); ?>">
                             <i class="fa fa-eye"></i>
                        </button>

                        <a href="<?php echo zen_href_link(FILENAME_ORDERS_INVOICE, 'oID=' . $oID); ?>" target="_blank" class="btn btn-xs btn-default" title="Print Invoice">
                            <i class="fa fa-print"></i>
                        </a>

                        <a href="<?php echo zen_href_link(FILENAME_ORDERS, 'oID=' . $oID . '&action=edit'); ?>" class="btn btn-xs btn-primary" title="Edit Order">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
