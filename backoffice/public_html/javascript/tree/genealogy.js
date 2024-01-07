var root_user_name = $('#root_user_name').val();
var tree_url = $('#tree_url').val();

jQuery(document).ready(function() {
    var responsive_tree = $('#responsive_tree').val();
    

    $('#tree.orgChart').data('username', root_user_name);
    $("#tree_view").jOrgChart({
        chartElement: '#tree',
        dragAndDrop: false
    });
    generateTooltips();
    $(window).on('resize', treeUpdateOnResize);
    $('#content').widthChanged(treeUpdateOnResize);
});

function getGenologyTree(user_name, event, tree_level = '') {
    if (event && $(event.target).hasClass('tree_up_icon')) {
        if ($(event.target).closest('.jOrgChart').parent('.orgChart').parent().hasClass('tree-expand')) {
            shrinkGenologyTree(user_name, event);
            return;
        }
    }
    $.ajax({
        type: "POST",
        url: tree_url,
        data: {
            user_name: user_name,
            tree_level : tree_level
        },
        beforeSend: function() {

        },
        success: function(data) {
            if (data == 'invalid')
                location.reload();
            $('#summary').html(data);
            $("#tree_view").jOrgChart({
                chartElement: '#tree',
                dragAndDrop: false
            });

        }
    });
}

function shrinkGenologyTree(user_name, event) {
    var target_node = $(event.target).parent('div').parent('div.node');
    if ($(target_node).closest('.jOrgChart').parent('.orgChart').parent('.tree-expand').prev('.tree-expand').length) {
        var orig_div = $(target_node).closest('.jOrgChart').parent('.orgChart').parent('.tree-expand').prev('.tree-expand');
    }
    else {
        var orig_div = $(target_node).closest('.jOrgChart').parent('.orgChart').parent('.tree-expand').prev('.jOrgChart');
    }
    $(orig_div).find('.node-expand').siblings('.line-expand').remove();
    $(orig_div).find('.node-expand').siblings('.line-expand-right').remove();
    $(orig_div).find('.node-expand').siblings('.line-expand-down').remove();
    $(orig_div).find('.node-expand').removeClass('node-expand');
    $(target_node).closest('.jOrgChart').parent('.orgChart').parent('.tree-expand').nextAll('.tree-expand').remove();
    $(target_node).closest('.jOrgChart').parent('.orgChart').parent('.tree-expand').remove();
}

function expandGenologyTree(user_name, event) {
   userName = user_name.replaceAll(".", "_");
    var target_node = $(event.target).parent('div').parent('div.node');
    if ($(target_node).closest('.jOrgChart').parent('.orgChart').parent().hasClass('tree-expand')) {
        $(target_node).closest('.jOrgChart').parent('.orgChart').parent('.tree-expand').nextAll('.tree-expand').remove();
    }
    else {
        $('.tree-expand').remove();
    }
    $(target_node).closest('.jOrgChart').find('.node-expand').siblings('.line-expand').remove();
    $(target_node).closest('.jOrgChart').find('.node-expand').siblings('.line-expand-right').remove();
    $(target_node).closest('.jOrgChart').find('.node-expand').siblings('.line-expand-down').remove();
    $(target_node).closest('.jOrgChart').find('.node-expand').removeClass('node-expand');
    $(target_node).addClass('node-expand');
    $(target_node).before("<div class='line left line-expand'></div><div class='line right top line-expand-right'></div><div class='line left line-expand-down'></div>");

    var responsive_tree = $('#responsive_tree').val();
    if ($('#responsive_tree').length && responsive_tree == "1") {
        var left_position = $(target_node).siblings('.line-expand').offset().left;
        var right_position = $(target_node).siblings('.line-expand-down').offset().left;
        var width = right_position - left_position;
        $(target_node).closest('.jOrgChart').find('.node-expand').siblings('.line-expand-right').width(Math.abs(width));
        if (width >= 0) {
            $(target_node).closest('.jOrgChart').find('.node-expand').siblings('.line-expand-right').css('right', '50%');
            $(target_node).closest('.jOrgChart').find('.node-expand').siblings('.line-expand-right').css('margin-right', '2px');
        } else {
            $(target_node).closest('.jOrgChart').find('.node-expand').siblings('.line-expand-right').css('left', '50%');
        }
    }
    else {
        var root_left = $(target_node).closest('.jOrgChart').find('img.root_node').offset().left;
        var line_left = $(target_node).siblings('.line-expand-right').offset().left;
        var right_expand_width = root_left - line_left + 34;
        if (right_expand_width < 0) {
            var margin_left = $(target_node).closest('.jOrgChart').find('.node-expand').siblings('.line-expand-right').css('margin-left').replace(/[^-\d\.]/g, '');
            var new_margin_left = right_expand_width + Math.abs(margin_left) - 2;
            $(target_node).closest('.jOrgChart').find('.node-expand').siblings('.line-expand-right').width(Math.abs(right_expand_width) + 4);
            $(target_node).closest('.jOrgChart').find('.node-expand').siblings('.line-expand-right').css('margin-left', new_margin_left);
            $(target_node).closest('.jOrgChart').find('.node-expand').siblings('.line-expand-down').css('margin-left', new_margin_left);
        }
        else {
            $(target_node).closest('.jOrgChart').find('.node-expand').siblings('.line-expand-right').width(right_expand_width);
            $(target_node).closest('.jOrgChart').find('.node-expand').siblings('.line-expand-down').width(right_expand_width);
        }
    }
    

    $('#tree').append("<div id='summary-" + userName + "' class='tree-expand'></div>");

    $.ajax({
        type: "POST",
        url: tree_url,
        data: {
            user_name: user_name
        },
        beforeSend: function () {

        },
        success: function (data) {
            if (data == 'invalid') {
                location.reload();
            }
            $('#summary-' + userName).html(data);
            $('#summary-' + userName).find('#tooltip_div').attr('id', 'tooltip_div-' + userName);
            $('#summary-' + userName).find('#tree_view').attr('id', 'tree_view-' + userName);
            $('#summary-' + userName).find('#tree').attr('id', 'tree-' + userName);
            $('#summary-' + userName).find('#tree-' + userName).data('username', user_name);
            $("#tree_view-" + userName).jOrgChart({
                chartElement: '#tree-' + userName,
                dragAndDrop: false
            });
        }
    });
}

function goToLink(url) {
    window.location.href = url;
}

function generateTooltips() {
    $('body').on('mouseover', '.tree_icon.with_tooltip', function() {
        if ($.tooltipster.instances($(this)).length == 0) {
            $(this).tooltipster({
                // custom trigger to solve issues in tooltips on touch devices
                trigger: 'custom',
                triggerOpen: {
                    mouseenter: true,
                    touchstart: true
                },
                triggerClose: {
                    mouseleave: true,
                    //originClick: true,
                    // touchleave: true
                },
                ////
                theme: 'tooltipster-shadow',
                contentAsHTML: true,
                delay: 100,
                interactive: true,
                arrow: false,
                side: ['top', 'bottom'],
    
            });
        }
        $(this).tooltipster('show');
    });
}

function treeUpdateOnResize() {
    var responsive_tree = $('#responsive_tree').val();
    if ($('#responsive_tree').length && responsive_tree == "1") {
        $('.line-expand-right').each(function() {
            var left_position = $(this).siblings('.line-expand').offset().left;
            var right_position = $(this).siblings('.line-expand-down').offset().left;
            var width = right_position - left_position;
            $(this).width(Math.abs(width));
        });
    }
}
