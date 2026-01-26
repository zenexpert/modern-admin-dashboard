<?php
/**
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * @version Modern Dynamic Dashboard 2026
 * @author ZenExpert - https://zenexpert.com
 */
if (!defined('IS_ADMIN_FLAG')) die('Illegal Access');

$menuTitles = zen_get_menu_titles();
$adminMenu  = zen_get_admin_menu_for_user();
?>
<nav class="navbar navbar-default main-tier" role="navigation">
    <div class="container-fluid">

        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-adm1-collapse">
                <span class="sr-only"><?php echo HEADER_TOGGLE_NAVIGATION; ?></span>
                <i class="fa fa-bars"></i> <?php echo HEADER_TITLE_MENU; ?>
            </button>
            <a class="navbar-brand visible-xs" href="#"><?php echo HEADER_TITLE_MENU; ?></a>
        </div>

        <div class="collapse navbar-collapse navbar-adm1-collapse">
            <ul class="nav navbar-nav">

                <?php
                // DEBUG: if menu is empty, show a warning
                if (empty($adminMenu)) {
                    echo '<li><a href="#" style="color:red;">' . HEADER_TITLE_MENU_ERROR . '</a></li>';
                }

                foreach ($adminMenu as $menuKey => $pages) {
                    ?>
                    <li class="dropdown">
                        <a href="<?php echo zen_href_link(FILENAME_ALT_NAV) ?>" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo $menuTitles[$menuKey]; ?> <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach ($pages as $page) { ?>
                                <li>
                                    <a href="<?php echo zen_href_link($page['file'], $page['params']); ?>">
                                        <?php echo $page['name']; ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <li class="dropdown visible-xs">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo HEADER_TITLE_QUICK_ACTIONS; ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo zen_href_link(FILENAME_DEFAULT); ?>"><?php echo HEADER_TITLE_TOP; ?></a></li>
                        <li><a href="<?php echo zen_catalog_href_link(FILENAME_DEFAULT); ?>" target="_blank"><?php echo HEADER_TITLE_ONLINE_CATALOG; ?></a></li>
                        <li><a href="<?php echo zen_href_link(FILENAME_LOGOFF); ?>"><?php echo HEADER_TITLE_LOGOFF; ?></a></li>
                    </ul>
                </li>

            </ul>

            <?php if ($url = page_has_help()) { ?>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="<?php echo $url; ?>" target="_blank" class="text-info">
                            <i class="fa fa-question-circle"></i> <?php echo IMAGE_MODULE_HELP; ?>
                        </a>
                    </li>
                </ul>
            <?php } ?>

        </div>
    </div>
</nav>
