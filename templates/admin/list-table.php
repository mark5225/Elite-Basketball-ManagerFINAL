<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo esc_html($title); ?>
    </h1>
    
    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=' . $post_type)); ?>" class="page-title-action">
        <?php _e('Add New', 'elite-basketball-manager'); ?>
    </a>

    <hr class="wp-header-end">

    <?php if (isset($message)) : ?>
        <div id="message" class="updated notice is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <form id="posts-filter" method="get">
        <input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>">
        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>">

        <?php
        $list_table->prepare_items();
        $list_table->search_box(__('Search', 'elite-basketball-manager'), 'search');
        $list_table->views();
        $list_table->display();
        ?>
    </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Handle bulk actions
    $('#doaction, #doaction2').click(function(e) {
        var action = $(this).prev('select').val();
        
        if (action === 'delete') {
            if (!confirm('<?php _e('Are you sure you want to delete the selected items?', 'elite-basketball-manager'); ?>')) {
                e.preventDefault();
            }
        }
    });

    // Handle individual delete actions
    $('.row-actions .delete a').click(function(e) {
        if (!confirm('<?php _e('Are you sure you want to delete this item?', 'elite-basketball-manager'); ?>')) {
            e.preventDefault();
        }
    });

    // Quick edit functionality
    $('.editinline').click(function() {
        var id = $(this).closest('tr').attr('id').replace('post-', '');
        var row = $('#edit-' + id);
        var originalRow = $('#post-' + id);

        // Populate quick edit fields
        row.find('input[name="post_title"]').val(originalRow.find('.column-title .row-title').text());
        
        if (typeof updateQuickEdit === 'function') {
            updateQuickEdit(id, row, originalRow);
        }
    });

    // Sortable rows for ordering
    if (typeof $.fn.sortable !== 'undefined') {
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };

        $('.wp-list-table tbody').sortable({
            helper: fixHelper,
            axis: 'y',
            handle: '.column-order .handle',
            update: function(event, ui) {
                var order = [];
                $('.wp-list-table tbody tr').each(function() {
                    order.push($(this).attr('id').replace('post-', ''));
                });

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'ebm_update_post_order',
                        order: order,
                        post_type: '<?php echo esc_js($post_type); ?>',
                        nonce: '<?php echo wp_create_nonce('ebm-update-order'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update order numbers
                            $('.column-order .order-number').each(function(index) {
                                $(this).text(index + 1);
                            });
                        }
                    }
                });
            }
        });
    }
});
</script>