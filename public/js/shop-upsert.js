$(document).ready(function(){
    if(chart_data != null){
        generateItemsGroupChart();
        generateProductGroupChart();
    }
});

this.generateProductGroupChart = function(){
    var colors = [ "#1f77b4", "#ff7f0e", "#2ca02c", "#d62728",
        "#9467bd", "#8c564b", "#e377c2", "#ffbb78",
        "#7f7f7f", "#bcbd22", "#17becf", "#aec7e8",
        "#98df8a", "#c5b0d5", "#c49c94", "#f7b6d2",
        "#c7c7c7", "#dbdb8d", "#9edae5", "#ff9896"
    ];
    var items_group_chart = document.getElementById("product_group").getContext("2d");
    var product_group_labels = [];
    var product_group_values = [];
    let product_group_colors = [];
    $.each(chart_data['product_group']['data'], function(label, amount){
        product_group_labels.push(label);
        product_group_values.push(amount);
        product_group_colors.push(colors.shift());
    })
    var chart1 = new Chart(items_group_chart, {
        type: 'pie',
        data: {
            labels: product_group_labels,
            datasets: [{
                data: product_group_values,
                backgroundColor: product_group_colors,
                hoverOffset: 5
            }],
        },
        options: {
            responsive: false,
        },
    });
}

this.generateItemsGroupChart = function(){
    var colors = [ "#1f77b4", "#ff7f0e", "#2ca02c", "#d62728",
        "#9467bd", "#8c564b", "#e377c2", "#ffbb78",
        "#7f7f7f", "#bcbd22", "#17becf", "#aec7e8",
        "#98df8a", "#c5b0d5", "#c49c94", "#f7b6d2",
        "#c7c7c7", "#dbdb8d", "#9edae5", "#ff9896"
    ];
    var items_chart = document.getElementById("items_group").getContext("2d");
    var items_labels = [];
    var items_values = [];
    let items_colors = [];
    $.each(chart_data['item_group']['data'], function(label, amount){
        items_labels.push(label);
        items_values.push(amount);
        items_colors.push(colors.shift());
    })
    var chart1 = new Chart(items_chart, {
        type: 'pie',
        data: {
            labels: items_labels,
            datasets: [{
                data: items_values,
                backgroundColor: items_colors,
                hoverOffset: 5
            }],
        },
        options: {
            responsive: false,
        },
    });
}
