<?php
/**
 * @copyright Copyright 2003-2025 Zen Cart Development Team
 * @version Modern Dynamic Dashboard 2026
 * @author ZenExpert - https://zenexpert.com
 */

if (!zen_is_superuser() && !check_page(FILENAME_STATS_PRODUCTS_PURCHASED, '')) return;

// prepare data (last 30 days)
$sql = "SELECT p.products_id, pd.products_name, p.products_image, p.products_model, SUM(op.products_quantity) as total_sold
        FROM " . TABLE_ORDERS_PRODUCTS . " op
        JOIN " . TABLE_ORDERS . " o ON op.orders_id = o.orders_id
        JOIN " . TABLE_PRODUCTS . " p ON op.products_id = p.products_id
        JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (p.products_id = pd.products_id AND pd.language_id = " . (int)$_SESSION['languages_id'] . ")
        WHERE o.date_purchased >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY p.products_id, pd.products_name, p.products_image, p.products_model
        ORDER BY total_sold DESC
        LIMIT 5";

$top_products = $db->Execute($sql);
?>

<div class="panel widget-wrapper">
    <div class="panel-heading">
        <i class="fa fa-trophy"></i> <?php echo BOX_LABEL_TOP_SELLERS; ?> <small class="text-muted"><?php echo BOX_LABEL_TOP_SELLERS_PERIOD; ?></small>
    </div>

    <?php if ($top_products->RecordCount() > 0) { ?>
        <ul class="list-group">
            <?php while (!$top_products->EOF) {
                $pID = $top_products->fields['products_id'];
                $name = $top_products->fields['products_name'];
                $sold = $top_products->fields['total_sold'];
                $img  = $top_products->fields['products_image'];
                $model = $top_products->fields['products_model'];

                $img_path = DIR_WS_CATALOG_IMAGES . $img;
                if (empty($img) || !file_exists(DIR_FS_CATALOG_IMAGES . $img)) {
                    // fallback placeholder if no image found
                    $thumb = '<div class="most-popular-fallback-image"><i class="fa fa-image"></i></div>';
                } else {
                    $thumb = '<img class="most-popular-main-image" src="' . $img_path . '">';
                }
                ?>
                <li class="list-group-item most-popular-item">
                    <div class="media">
                        <div class="media-left media-middle">
                            <a href="<?php echo zen_href_link(FILENAME_PRODUCT, 'action=new_product&pID=' . $pID); ?>">
                                <?php echo $thumb; ?>
                            </a>
                        </div>
                        <div class="media-body media-middle">
                            <h5 class="media-heading">
                                <a href="<?php echo zen_href_link(FILENAME_PRODUCT, 'action=new_product&pID=' . $pID); ?>">
                                    <?php echo zen_trunc_string($name, 35, true); ?>
                                </a>
                            </h5>
                            <small class="text-muted"><?php echo BOX_LABEL_PRODUCTS_ID . $pID . (!empty($model) ? ' | ' . BOX_LABEL_PRODUCTS_MODEL . $model : '' ); ?></small>
                        </div>
                        <div class="media-right media-middle text-right">
                        <span class="badge bg-green">
                            <?php echo $sold . BOX_LABEL_SOLD; ?>
                        </span>
                        </div>
                    </div>
                </li>
                <?php $top_products->MoveNext(); } ?>
        </ul>
        <div class="panel-footer text-center">
            <a href="<?php echo zen_href_link(FILENAME_STATS_PRODUCTS_PURCHASED); ?>" class="btn btn-default btn-xs btn-block"><?php echo BOX_LABEL_VIEW_FULL_REPORT; ?></a>
        </div>
    <?php } else { ?>
        <div class="panel-body text-center text-muted">
            <br><i class="fa fa-frown-o fa-2x"></i><br><br><?php echo BOX_LABEL_NO_SALES; ?>
        </div>
    <?php } ?>
</div>
