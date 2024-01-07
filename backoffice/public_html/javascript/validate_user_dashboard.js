jQuery(document).ready(function() {
            // ValidateUser.init();
            
            $('.master_item_box').on('click', function () {
                $(this).parent('label').siblings('.checkbox-child').find('.child_item_box').prop('checked', $(this).is(':checked'));
            });
            $('.child_item_box').on('click', function () {
                var unchecked = ($(this).closest('.checkbox-parent').find('.child_item_box:not(:checked)').length == $(this).closest('.checkbox-parent').find('.child_item_box').length);
                $(this).closest('.checkbox-parent').find('.master_item_box').prop('checked', !unchecked);
            });
        });