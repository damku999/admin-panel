@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">

        <div style="width: 80%; margin: 0 auto;">
            <canvas id="earningsChart"></canvas>
        </div>
        <!-- Content Row -->
        <div class="row">
            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Current Month - Turn Over (with GST)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    &#8377; {{ number_format($current_month_final_premium_with_gst, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fad fa-rupee-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Current Month - Commission Received</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    &#8377; {{ number_format($current_month_my_commission_amount, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fad fa-rupee-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Current Month - Commission Transferred</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    &#8377; {{ number_format($current_month_transfer_commission_amount, 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fad fa-rupee-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Current Month - Actual Earning</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    &#8377; {{ number_format($current_month_actual_earnings, 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fad fa-rupee-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>

        <!-- Content Row -->
        <div class="row">
            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Last Month - Turn Over (with GST)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    &#8377; {{ number_format($last_month_final_premium_with_gst, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fad fa-rupee-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Last Month - Commission Received</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    &#8377; {{ number_format($last_month_my_commission_amount, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fad fa-rupee-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Last Month - Commission Transferred</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    &#8377; {{ number_format($last_month_transfer_commission_amount, 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fad fa-rupee-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Last Month - Actual Earning</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    &#8377; {{ number_format($last_month_actual_earnings, 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fad fa-rupee-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>

        <!-- Content Row -->
        <div class="row">
            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Life Time - Turn Over (with GST)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    &#8377; {{ number_format($life_time_final_premium_with_gst, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fad fa-rupee-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Life Time - Commission Received</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    &#8377; {{ number_format($life_time_my_commission_amount, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fad fa-rupee-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Life Time - Commission Transferred</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    &#8377; {{ number_format($life_time_transfer_commission_amount, 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fad fa-rupee-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Life Time - Actual Earning</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    &#8377; {{ number_format($life_time_actual_earnings, 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fad fa-rupee-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>

        <!-- Content Row -->
        <div class="row">
            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Policy's</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_customer_insurance }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shield-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Active Policy's</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $active_customer_insurance }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    In Active Policy's</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inactive_customer_insurance }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Expiering (This Month)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expiring_customer_insurance }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>

        <!-- Content Row -->
        <div class="row">
            <div class="col-xl-2 col-md-3 b-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Customer</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_customer }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-3 b-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Active Customers</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $active_customer }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-3 b-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    In Active Customers</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inactive_customer }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-times fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const data = {!! $json_data !!};

        const labels = Object.keys(data);
        const datasets = Object.keys(data[labels[0]]).map(key => ({
            label: key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' '),
            backgroundColor: `rgba(${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, 0.5)`,
            data: labels.map(label => data[label][key]),
        }));

        const config = {
            type: "bar",
            data: {
                labels: labels,
                datasets: datasets,
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: false,
                    },
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        };

        const myChart = new Chart(document.getElementById("earningsChart"), config);
    </script>

@endsection
