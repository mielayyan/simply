
var curr_url = $('#current_url_full').val();
curr_url = curr_url.replace("index", '');
var base_path = $('#base_url').val() + curr_url;

$("#payout_dash li a").click(function () {
    var id = this.id;
    var external_path = base_path + '/ajax_payout/' + id;
    $("#payout_dash li").removeClass("active");
    $.ajax({
        type: 'POST',
        url: external_path,
        success: function (data) {
            $('#total_payout').html(data);
            $('#' + id).closest('li').addClass('active');
        }
    });
});

$("#commission_dash li a").click(function () {
    var id = this.id;
    var external_path = base_path + '/ajax_commission/' + id;
    $("#commission_dash li").removeClass("active");
    $.ajax({
        type: 'POST',
        url: external_path,
        success: function (data) {
            $('#total_commission').html(data);
            $('#' + id).closest('li').addClass('active');
        }
    });
});

$("#sales_dash li a").click(function () {
    var id = this.id;
    var external_path = base_path + '/ajax_sales/' + id;
    $("#sales_dash li").removeClass("active");
    $.ajax({
        type: 'POST',
        url: external_path,
        success: function (data) {
            $('#sales_total').html(data);
            $('#' + id).closest('li').addClass('active');
        }
    });
});


$("#mail_dash li a").click(function () {
    var id = this.id;
    var external_path = base_path + '/ajax_mail/' + id;
    $("#mail_dash li").removeClass("active");
    $.ajax({
        type: 'POST',
        url: external_path,
        dataType: 'json',
        success: function (data) {
            $('#mail_total').html(data['mail_total']);
            $('#' + id).closest('li').addClass('active');
        }
    });
});

$(function() {
    // country vector graph
    var country_graph = $('#country_graph');
    var country_graph_max = Math.max.apply(null, Object.keys(country_map_data).map(function (key) { return country_map_data[key]; }));
    var country_graph_options = [{
        map: 'world_mill_en',
        backgroundColor: '#fff',
        regionStyle: {
            initial: {
                fill: '#c7dde0'
            },
            hover: {
                fill: '#7266ba'
            },
        },
        series: {
            regions: [{
                values: country_map_data,
                scale: ['#bfe2e8', '#7266ba'],
                normalizeFunction: 'polynomial',
            }]
        },
        onRegionTipShow: function (e, el, code) {
            el.html(el.html() + ' (' + $('#join').html() +' - ' + country_map_data[code] + ')');
        },
    }];
    if (country_graph_max == 0) {
        country_graph_options[0].series.regions[0].max = 1;
    }
    uiLoad.load(jp_config['vectorMap']).then(function () {
        country_graph['vectorMap'].apply(country_graph, country_graph_options);
    });

    // joining graph
    var joining_graph = $('#joining_graph_div');
    var mlm_plan = $('#mlm_plan').val();
    var external_path = base_path + '/ajax_joinings_chart/';
    uiLoad.load(jp_config['plot']).then(function () {
        $('#joinings_graph li a#monthly_joining_graph').click();
    });

    window.join_chart = null;
    $('#joinings_graph li a').on('click', function () {
        var id = this.id;
        $("#joinings_graph li").removeClass("active");
        $.ajax({
            type: 'POST',
            url: external_path + id,
            dataType: "JSON",
            success: function (response_data) {
                var joining_graph_data;
                var xaxis_label = [];
                var yaxis_top = 0;
                if (mlm_plan == 'Binary') {
                    // var left_data = [];
                    // var right_data = [];
                    var left_data2 = [];
                    var right_data2 = [];
                    for (i = 0; i < response_data.length; i++) {
                        // left_data.push([response_data[i].x, response_data[i].y]);
                        left_data2.push(response_data[i].y);
                        // right_data.push([response_data[i].x, response_data[i].z]);
                        right_data2.push(response_data[i].z);
                        // xaxis_label.push([response_data[i].x, response_data[i].x_label]);
                        xaxis_label.push([response_data[i].x_label]);
                        if (response_data[i].y > yaxis_top || response_data[i].z > yaxis_top) {
                            yaxis_top = response_data[i].y;
                            if (response_data[i].z > response_data[i].y) {
                                yaxis_top = response_data[i].z;
                            }
                        }
                    }
                    joining_graph_data = [
                        {
                            label: $('#left_join').html(),
                            backgroundColor: 'rgba(149, 139, 204,0.3)',
                            borderColor: 'rgb(114, 101, 186)',
                            pointBorderColor: 'rgb(114, 101, 186)',
                            pointBorderWidth: 3,
                            pointStyle: 'rect',
                            borderWidth: 1,
                            pointRadius: 3,
                            pointHoverRadius: 2,
                            data: left_data2,
                            fill: true
                        },
                        {
                            label: $('#right_join').html(),
                            backgroundColor: 'rgba(71, 172, 222,0.3)',
                            borderColor: 'rgb(65, 183, 229)',
                            pointBackgroundColor: 'rgb(65, 183, 229)',
                            pointBorderColor: 'rgb(65, 183, 229)',
                            pointBorderWidth: 3,
                            pointStyle: 'rect',
                            borderWidth: 1,
                            pointRadius: 3,
                            pointHoverRadius: 2,
                            data: right_data2,
                            fill: true
                        }
                    ];
                }
                else {
                    var data = [];
                    for (i = 0; i < response_data.length; i++) {
                        // data.push([response_data[i].x, response_data[i].y]);
                        data.push([response_data[i].y]);
                        // xaxis_label.push([response_data[i].x, response_data[i].x_label]);
                        xaxis_label.push([response_data[i].x_label]);
                        if (response_data[i].y > yaxis_top) {
                            yaxis_top = response_data[i].y;
                        }
                    }
                    joining_graph_data = [
                        {
                            /*data: data,
                            label: $('#join').html(),
                            points: {show: true, radius: 1},
                            splines: {show: true, tension: 0.4, lineWidth: 1, fill: 0.8}*/
                            label: $('#join').html(),
                            backgroundColor: 'rgba(149, 139, 204,0.3)',
                            borderColor: 'rgb(114, 101, 186)',
                            pointBorderColor: 'rgb(114, 101, 186)',
                            pointBorderWidth: 3,
                            pointStyle: 'rect',
                            borderWidth: 1,
                            pointRadius: 3,
                            pointHoverRadius: 2,
                            data: data,
                            fill: true
                        }
                    ];
                }
                /* area chart */
                var config = {
                    type: 'line',
                    data: {
                        labels: xaxis_label,
                        datasets: joining_graph_data,
                    },
                    options: {
                        responsive: true,
                        legend: {
                            labels: {
                                usePointStyle: true,
                            }
                        },
                        title: {
                            display: false,
                            text: 'Area Chart'
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false,
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: false,
                                    labelString: '6 months forecast'
                                },
                                gridLines: {
                                    display: false,
                                    color: "#f2f2f2"
                                },
                                ticks: {
                                    beginAtZero: true,
                                    fontSize: 11
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: false,
                                    labelString: 'Profit margin (approx)'
                                },
                                gridLines: {
                                    display: false,
                                    color: "#f2f2f2"
                                },
                                ticks: {
                                    beginAtZero: true,
                                    fontSize: 11,
                                    precision: 0
                                }
                            }]
                        }
                    }
                };
                var ctx = document.getElementById('join_chart').getContext('2d');
                if(window.join_chart) {
                    window.join_chart.destroy();
                }
                window.join_chart = new Chart(ctx, config);
            },
        });
    });
    
    $(document).ready(function() {
        // getFirstChartData();
    });
    function getFirstChartData() {
        $.ajax({
            type: 'POST',
            url: external_path + id,
            dataType: "JSON",
            success: function (response_data) {
                var joining_graph_data;
                var xaxis_label = [];
                var yaxis_top = 0;
                if (mlm_plan == 'Binary') {
                    // var left_data = [];
                    // var right_data = [];
                    var left_data2 = [];
                    var right_data2 = [];
                    for (i = 0; i < response_data.length; i++) {
                        // left_data.push([response_data[i].x, response_data[i].y]);
                        left_data2.push(response_data[i].y);
                        // right_data.push([response_data[i].x, response_data[i].z]);
                        right_data2.push(response_data[i].z);
                        // xaxis_label.push([response_data[i].x, response_data[i].x_label]);
                        xaxis_label.push([response_data[i].x_label]);
                        if (response_data[i].y > yaxis_top || response_data[i].z > yaxis_top) {
                            yaxis_top = response_data[i].y;
                            if (response_data[i].z > response_data[i].y) {
                                yaxis_top = response_data[i].z;
                            }
                        }
                    }
                    joining_graph_data = [
                        {
                            label: $('#left_join').html(),
                            backgroundColor: 'rgba(149, 139, 204,0.3)',
                            borderColor: 'rgb(114, 101, 186)',
                            pointBorderColor: 'rgb(114, 101, 186)',
                            pointBorderWidth: 2,
                            borderWidth: 1,
                            pointRadius: 3,
                            pointHoverRadius: 2,
                            data: left_data2,
                            fill: true
                        },
                        {
                            label: $('#right_join').html(),
                            backgroundColor: 'rgba(71, 172, 222,0.3)',
                            borderColor: 'rgb(65, 183, 229)',
                            pointBackgroundColor: 'rgb(65, 183, 229)',
                            pointBorderColor: 'rgb(65, 183, 229)',
                            pointBorderWidth: 3,
                            pointStyle: 'rect',
                            borderWidth: 1,
                            pointRadius: 3,
                            pointHoverRadius: 2,
                            data: right_data2,
                            fill: true
                        }
                    ];
                }
                else {
                    var data = [];
                    for (i = 0; i < response_data.length; i++) {
                        // data.push([response_data[i].x, response_data[i].y]);
                        data.push([response_data[i].y]);
                        // xaxis_label.push([response_data[i].x, response_data[i].x_label]);
                        xaxis_label.push([response_data[i].x_label]);
                        if (response_data[i].y > yaxis_top) {
                            yaxis_top = response_data[i].y;
                        }
                    }
                    joining_graph_data = [
                        {
                            data: data,
                            label: $('#join').html(),
                            points: {show: true, radius: 1},
                            splines: {show: true, tension: 0.4, lineWidth: 1, fill: 0.8}
                        }
                    ];
                }
                /* area chart */
                var config = {
                    type: 'line',
                    data: {
                        labels: xaxis_label,
                        datasets: joining_graph_data,
                    },
                    options: {
                        responsive: true,
                        legend: {
                            labels: {
                                // usePointStyle: true,
                            }
                        },
                        title: {
                            display: false,
                            text: 'Area Chart'
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false,
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: false,
                                    labelString: '6 months forecast'
                                },
                                gridLines: {
                                    display: true,
                                    color: "#f2f2f2"
                                },
                                ticks: {
                                    beginAtZero: true,
                                    fontSize: 11
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: false,
                                    labelString: 'Profit margin (approx)'
                                },
                                gridLines: {
                                    display: true,
                                    color: "#f2f2f2"
                                },
                                ticks: {
                                    beginAtZero: true,
                                    fontSize: 11
                                }
                            }]
                        }
                    }
                };
                var ctx = document.getElementById('join_chart').getContext('2d');
                if(window.join_chart != null) {
                    chart.destroy();
                }
                window.join_chart = new Chart(ctx, config);
            },
        });
    }
    // pie chart - todo-list
    var todo_pending = $('#todo_pending');
    var todo_done = $('#todo_done');
    var todo_done_options = [{
        percent: $('#todo_done_percent').val(),
        lineWidth: 4,
        trackColor: '#e8eff0',
        barColor: '#7266ba',
        scaleColor: false,
        size: 118,
        rotate: 90,
        lineCap: 'butt'
    }];
    var todo_pending_options = [{
        percent: $('#todo_pending_percent').val(),
        lineWidth: 4,
        trackColor: '#e8eff0',
        barColor: '#23b7e5',
        scaleColor: false,
        size: 118,
        rotate: 180,
        lineCap: 'butt'
    }];
    uiLoad.load(jp_config['easyPieChart']).then(function () {
        todo_pending['easyPieChart'].apply(todo_pending, todo_pending_options);
        todo_done['easyPieChart'].apply(todo_done, todo_done_options);
    });

});
$(document).ready(function() {
    $("#news_carousel").each(function(){
        $(this).owlCarousel({
          loop:false,
            margin:10,
            responsiveClass:true,
            nav:true,        
            responsive:{
                0:{
                    items:1
                },
                900:{
                    items:2
                },
                1025:{
                    items:3
                }
            }
        });
    });
    $("#sales_dash li a#yearly_sales").click();
    $('#news_div').fadeIn(500);
    $('button.news-hide').click(function() {
        $("#news_div").fadeOut("slow", function() {
            $("#news_div").removeClass("news-fixed").fadeIn();
        });
    });
    setTimeout(function() {
       $("#news_div").fadeOut("slow", function() {
            $("#news_div").removeClass("news-fixed").fadeIn();
        }); 
    }, 30000)
})
