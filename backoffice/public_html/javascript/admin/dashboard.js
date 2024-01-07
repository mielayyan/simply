let barChart = null;
let joininigChart = null;

$('.filter-dashboard-tiles li a').on('click', function(e) {
    e.preventDefault();
    var filter = $(this).data('value');
    var type   = $(this).data('type');
    $.ajax({
        method: 'GET',
        data: {
        	'filter': filter,
        	'type': type
        },
        url: $('#base_url').val()+'admin/home/get_user_dashbord_data',
        success: function(response) {
        	data = JSON.parse(response);
        	if(type == "income") {
        		$('#total_incom_value').text(data.income);
        	}
        	if(type == "bonus") {
        		$('#total_bonus_value').text(data.bonus_value);
        	}
        	if(type == "paid") {
        		$('#total_payout_paid').text(data.payout_paid);
        	}
        }
   });
});

$('.filter-dashboard-graph li a').on('click', function(e) {
    e.preventDefault();
    var filter = $(this).data('value');
    var graph   = $(this).data('graph');
    $.ajax({
        method: 'GET',
        data: {
            'filter': filter,
            'graph': graph
        },
        url: $('#base_url').val()+'admin/home/get_user_dashbord_graph_data',
        success: function(response) {
            data = JSON.parse(response);
            if(graph == "income_commission") {
                renderBarChartData(data.bar_chart_data);
            }
            if(graph == "joinings") {
                renderJoiningGraph(data.joining_graph_data)
            }
            
        }
   });
});

function renderJoiningGraph(joiningLineGraphData) {
    // Line chart
    var lineData = {};
    if($("#mlm_plan").val() == "Binary") {
        var lineData = {
            labels: joiningLineGraphData.labels,
            datasets: [
                {
                    label: trans('left_joinings'),
                    backgroundColor: '#a2b969',
                    borderColor: "#a2b969",
                    pointBackgroundColor: "rgba(26,179,148,1)",
                    pointBorderColor: "#99b35a",
                    data: joiningLineGraphData.leftJoinArray,
                    fill: false
                }, {
                    label: trans('right_joinings'),
                    backgroundColor: '#0d95bc',
                    borderColor: "#0aa1cc",
                    pointBorderColor: "#0385aa",
                    data: joiningLineGraphData.rightJoinArray,
                    fill: false
                }
            ]
        };
    } else {
        var lineData = {
            labels: joiningLineGraphData.labels,
            datasets: [
                {
                    label: trans('joinings'),
                    backgroundColor: '#a2b969',
                    borderColor: "#a2b969",
                    pointBackgroundColor: "rgba(26,179,148,1)",
                    pointBorderColor: "#99b35a",
                    data: joiningLineGraphData.joinArray,
                    fill: false
                }
            ]
        };
    }
    

    var lineOptions = {
        responsive: true,
        maintainAspectRatio: false
    };
    if($('#lineChart').length) {
        var ctx = document.getElementById("lineChart").getContext("2d");
        if (!joininigChart) {
           joininigChart = new Chart(ctx, { type: 'line', data: lineData, options: lineOptions });
           joininigChart.render();
        } else {
            joininigChart.destroy();
            joininigChart = new Chart(ctx, { type: 'line', data: lineData, options: lineOptions }); 
            joininigChart.render();
        }
    }
}

function renderBarChartData(barChartData) {
    // bar chart
    var barData = {
        labels: barChartData.labels,
        datasets: [
            {
                label: trans("income"),
                backgroundColor: '#008697',
                borderColor: "#008697",
                pointBorderColor: "#fff",
                data: barChartData.incomeArray,
                data_formatted: barChartData.incomeStringArray
                //fill: false
            },
            {
                label: trans("commission"),
                backgroundColor: '#31d5f5',
                borderColor: "#31d5f5",
                pointBackgroundColor: "rgba(115,69,112,0.8)",
                pointBorderColor: "#fff",
                data: barChartData.bonusArray,
                data_formatted: barChartData.bonusStringArray
                //fill: false
            }
        ]
    };
    
    var barOptions = {
        responsive: true,
        maintainAspectRatio: false,
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    let currency_value = data.datasets[tooltipItem.datasetIndex].data_formatted[tooltipItem.index]
                    return data.datasets[tooltipItem.datasetIndex].label + ': ' + currency_value;
                }
            }
        }
    };
    
    if($('#barChart').length) {
    var ctx2 = document.getElementById("barChart").getContext("2d");
        if (!barChart) {
           barChart = new Chart(ctx2, { type: 'bar', data: barData, options: barOptions }); 
           barChart.render();
        } else {
            barChart.destroy();
            barChart = new Chart(ctx2, { type: 'bar', data: barData, options: barOptions }); 
            barChart.render();
        }
    }
}

$('.filter-dashboard-tiles-all li a').on('click', function(e) {
    e.preventDefault();
    var filter = $(this).data('value');
    $.ajax({
        method: 'GET',
        data: {
            'filter': filter,
        },
        url: $('#base_url').val()+'admin/home/get_user_dashbord_data',
        success: function(response) {
            data = JSON.parse(response);
            $('#total_incom_value').text(data.income);
            $('#total_bonus_value').text(data.bonus_value);
            $('#total_payout_paid').text(data.payout_paid);
        }
   });
});

$(document).ready(function() {
    renderJoiningGraph(joiningLineGraphData);
    renderBarChartData(barChartData);
});