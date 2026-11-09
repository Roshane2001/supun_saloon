<?php
// Prepare data arrays for the last 7 days
$labels = [];
$sales_data = [];
$profit_data = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('D (m/d)', strtotime($date)); // Day name and date
    
    // 1. Fetch Total Sales for the day
    $s_query = mysqli_query($con, "SELECT SUM(grand_total) as total FROM tblsale WHERE DATE(sale_date) = '$date' AND sale_status = 'Completed'");
    $s_res = mysqli_fetch_assoc($s_query);
    $day_sales = (float)($s_res['total'] ?? 0);
    $sales_data[] = $day_sales;

    // 2. Fetch Total Cost for the day to calculate Profit
    $c_query = mysqli_query($con, "SELECT SUM(si.quantity * si.purchase_price_at_sale) as total_cost 
                                   FROM tblsale_items si 
                                   JOIN tblsale s ON si.sale_id = s.id 
                                   WHERE DATE(s.sale_date) = '$date' AND s.sale_status = 'Completed'");
    $c_res = mysqli_fetch_assoc($c_query);
    $day_cost = (float)($c_res['total_cost'] ?? 0);
    
    // Profit = Sales - Cost
    $profit_data[] = $day_sales - $day_cost;
}
?>

<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Weekly Sales vs Profit</div>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="multipleBarChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var multipleBarChart = document.getElementById('multipleBarChart').getContext('2d');

        var myMultipleBarChart = new Chart(multipleBarChart, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: "Total Sales",
                    backgroundColor: '#1572E8',
                    borderColor: '#1572E8',
                    data: <?php echo json_encode($sales_data); ?>,
                }, {
                    label: "Net Profit",
                    backgroundColor: '#31CE36',
                    borderColor: '#31CE36',
                    data: <?php echo json_encode($profit_data); ?>,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    yAxes: [{ ticks: { beginAtZero: true } }]
                }
            }
        });
    });
</script>