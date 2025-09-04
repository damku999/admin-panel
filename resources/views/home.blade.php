@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        function getArrows($currentValue, $previousValue)
        {
            if ($currentValue > $previousValue) {
                return '⬆️';
            } elseif ($currentValue < $previousValue) {
                return '⬇️';
            } else {
                return '';
            }
        }
        $metricLabels = [
            'sum_final_premium' => 'Final Premium',
            'sum_my_commission' => 'My Commission',
            'sum_transfer_commission' => 'Commission Given',
            'sum_actual_earnings' => 'My Earning',
        ];

    @endphp
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card py-1">
                    <div class="card-body overflow-x-auto">
                        <table class="table table-striped table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Today <br>[{{ \Carbon\Carbon::parse($data['date'])->format('d-M-Y') }}]</th>
                                    <th>Yesterday <br>[{{ \Carbon\Carbon::parse($data['yesterday'])->format('d-M-Y') }}]</th>
                                    <th>Ere Yesterday
                                        <br>[{{ \Carbon\Carbon::parse($data['day_before_yesterday'])->format('d-M-Y') }}]
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>Final Premium</th>
                                    <td>
                                        <span>{{ getArrows($data['today_data']['sum_final_premium'], $data['yesterday_data']['sum_final_premium']) }}</span>
                                        {{ number_format($data['today_data']['sum_final_premium'], 2) }}
                                    </td>
                                    <td>
                                        <span>{{ getArrows($data['yesterday_data']['sum_final_premium'], $data['day_before_yesterday_data']['sum_final_premium']) }}</span>
                                        {{ number_format($data['yesterday_data']['sum_final_premium'], 2) }}
                                    </td>
                                    <td>{{ number_format($data['day_before_yesterday_data']['sum_final_premium'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>My Commission</th>
                                    <td>
                                        <span>{{ getArrows($data['today_data']['sum_my_commission'], $data['yesterday_data']['sum_my_commission']) }}</span>
                                        {{ number_format($data['today_data']['sum_my_commission'], 2) }}
                                    </td>
                                    <td>
                                        <span>{{ getArrows($data['yesterday_data']['sum_my_commission'], $data['day_before_yesterday_data']['sum_my_commission']) }}</span>
                                        {{ number_format($data['yesterday_data']['sum_my_commission'], 2) }}
                                    </td>
                                    <td>{{ number_format($data['day_before_yesterday_data']['sum_my_commission'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Commission Given</th>
                                    <td>
                                        <span>{{ getArrows($data['today_data']['sum_transfer_commission'], $data['yesterday_data']['sum_transfer_commission']) }}</span>
                                        {{ number_format($data['today_data']['sum_transfer_commission'], 2) }}
                                    </td>
                                    <td>
                                        <span>{{ getArrows($data['yesterday_data']['sum_transfer_commission'], $data['day_before_yesterday_data']['sum_transfer_commission']) }}</span>
                                        {{ number_format($data['yesterday_data']['sum_transfer_commission'], 2) }}
                                    </td>
                                    <td>{{ number_format($data['day_before_yesterday_data']['sum_transfer_commission'], 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>My Earning</th>
                                    <td>
                                        <span>{{ getArrows($data['today_data']['sum_actual_earnings'], $data['yesterday_data']['sum_actual_earnings']) }}</span>
                                        {{ number_format($data['today_data']['sum_actual_earnings'], 2) }}
                                    </td>
                                    <td>
                                        <span>{{ getArrows($data['yesterday_data']['sum_actual_earnings'], $data['day_before_yesterday_data']['sum_actual_earnings']) }}</span>
                                        {{ number_format($data['yesterday_data']['sum_actual_earnings'], 2) }}
                                    </td>
                                    <td>{{ number_format($data['day_before_yesterday_data']['sum_actual_earnings'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card py-1">
                    <div class="card-body overflow-x-auto">
                        <table class="table table-striped table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Current Year <br>
                                        [ {{ \Carbon\Carbon::parse($data['financial_year_start'])->format('M-Y') }} to
                                        {{ \Carbon\Carbon::parse($data['financial_year_end'])->format('M-Y') }}]
                                    </th>
                                    <th>Last Year <br>
                                        [{{ \Carbon\Carbon::parse($data['previous_financial_year_start'])->format('M-Y') }} to
                                        {{ \Carbon\Carbon::parse($data['previous_financial_year_end'])->format('M-Y') }}]</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>Final Premium</th>
                                    <td>
                                        <span>{{ getArrows($data['current_year_data']['sum_final_premium'], $data['last_year_data']['sum_final_premium']) }}</span>
                                        {{ number_format($data['current_year_data']['sum_final_premium'], 2) }}
                                    </td>
                                    <td>{{ number_format($data['last_year_data']['sum_final_premium'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>My Commission</th>
                                    <td>
                                        <span>{{ getArrows($data['current_year_data']['sum_my_commission'], $data['last_year_data']['sum_my_commission']) }}</span>
                                        {{ number_format($data['current_year_data']['sum_my_commission'], 2) }}
                                    </td>
                                    <td>{{ number_format($data['last_year_data']['sum_my_commission'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Commission Given</th>
                                    <td>
                                        <span>{{ getArrows($data['current_year_data']['sum_transfer_commission'], $data['last_year_data']['sum_transfer_commission']) }}</span>
                                        {{ number_format($data['current_year_data']['sum_transfer_commission'], 2) }}
                                    </td>
                                    <td>{{ number_format($data['last_year_data']['sum_transfer_commission'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>My Earning</th>
                                    <td>
                                        <span>{{ getArrows($data['current_year_data']['sum_actual_earnings'], $data['last_year_data']['sum_actual_earnings']) }}</span>
                                        {{ number_format($data['current_year_data']['sum_actual_earnings'], 2) }}
                                    </td>
                                    <td>{{ number_format($data['last_year_data']['sum_actual_earnings'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row"><br />
            <hr /><br />
        </div>
        <div class="row">
            <div class="card py-1 ml-1">
                <div class="card-body">
                    <table class="table table-striped table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th>#</th>
                                @foreach ($data['quarters_data'] as $quarter)
                                    <th>Quarter {{ $loop->iteration }} <br>
                                        [{{ \Carbon\Carbon::parse($data['quarter_date'][$loop->iteration - 1]['quarter_start'])->format('M-Y') }}
                                        to
                                        {{ \Carbon\Carbon::parse($data['quarter_date'][$loop->iteration - 1]['quarter_end'])->format('M-Y') }}]
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($metricLabels as $metricKey => $metricLabel)
                                <tr>
                                    <th>{{ $metricLabel }}</th>
                                    @foreach ($data['quarters_data'] as $index => $quarter)
                                        <td>
                                            @if ($index < count($data['quarters_data']) - 1)
                                                <span>{{ getArrows($quarter[$metricKey], $data['quarters_data'][$index + 1][$metricKey]) }}
                                                </span>
                                            @endif {{ number_format($quarter[$metricKey], 2) }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row"><br />
            <hr /><br />
        </div>
        <!-- Content Row -->
        <div class="row">
            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-primary shadow h-100 py-2" onclick="redirectToCustomerInsuranceIndex()">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Renewing This Month</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $total_renewing_this_month }}
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
                <div class="card border-left-success shadow h-100 py-2" onclick="redirectToCustomerInsuranceIndex(1)">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Already Renewed This Month</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $already_renewed_this_month }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fas fa-rupee-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-4 b-4">
                <div class="card border-left-warning shadow h-100 py-2" onclick="redirectToCustomerInsuranceIndex(0,1)">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Pending Renewal This Month</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $pending_renewal_this_month }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fas fa-rupee-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row"><br />
            <hr /><br />
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
                                <i class="fa fas fa-rupee-sign fa-2x text-gray-300"></i>
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
                                <i class="fa fas fa-rupee-sign fa-2x text-gray-300"></i>
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
                                <i class="fa fas fa-rupee-sign fa-2x text-gray-300"></i>
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
                                <i class="fa fas fa-rupee-sign fa-2x text-gray-300"></i>
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
                                <i class="fa fas fa-rupee-sign fa-2x text-gray-300"></i>
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
                                <i class="fa fas fa-rupee-sign fa-2x text-gray-300"></i>
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
                                <i class="fa fas fa-rupee-sign fa-2x text-gray-300"></i>
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
                                <i class="fa fas fa-rupee-sign fa-2x text-gray-300"></i>
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
                                <i class="fa fas fa-rupee-sign fa-2x text-gray-300"></i>
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
                                <i class="fa fas fa-rupee-sign fa-2x text-gray-300"></i>
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
                                <i class="fa fas fa-rupee-sign fa-2x text-gray-300"></i>
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
                                <i class="fa fas fa-rupee-sign fa-2x text-gray-300"></i>
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

        <div style="width: 80%; margin: 0 auto;">
            <canvas id="earningsChart"></canvas>
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

        function redirectToCustomerInsuranceIndex(already_renewed_this_month = 0, pending_renewal_this_month = 0) {
            var date = new Date();

            // Start of the month
            var start = new Date(date.getFullYear(), date.getMonth(), 1);
            var startDate = ("0" + start.getDate()).slice(-2) + "-" +
                ("0" + (start.getMonth() + 1)).slice(-2) + "-" +
                start.getFullYear();

            // End of the month
            var end = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            var endDate = ("0" + end.getDate()).slice(-2) + "-" +
                ("0" + (end.getMonth() + 1)).slice(-2) + "-" +
                end.getFullYear();

            const url =
                `{{ route('customer_insurances.index') }}?start_date=${startDate}&end_date=${endDate}&already_renewed_this_month=${already_renewed_this_month}&pending_renewal_this_month=${pending_renewal_this_month}`;
            window.location.href = url;
        }
    </script>

@endsection
