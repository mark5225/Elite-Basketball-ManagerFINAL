<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1 class="wp-heading-inline">Teams Management</h1>
    <a href="?page=basketball-manager-teams&action=add" class="page-title-action">Add New Team</a>
    
    <?php
    if (isset($_GET['message'])) {
        switch ($_GET['message']) {
            case 'team_saved':
                echo '<div class="notice notice-success is-dismissible"><p>Team saved successfully.</p></div>';
                break;
            case 'team_deleted':
                echo '<div class="notice notice-success is-dismissible"><p>Team deleted successfully.</p></div>';
                break;
        }
    }
    
    $team_list = new \EBM\Admin\TeamList();
    $team_list->prepare_items();
    ?>

    <form method="post">
        <?php
        $team_list->search_box('Search Teams', 'team_search');
        $team_list->display();
        ?>
    </form>
</div>