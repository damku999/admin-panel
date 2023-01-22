<div class="card-body table-responsive p-0" style="max-height: 580px;">
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th style="width: 20%;" class="filter_by_click pointer" sort_field="created_at">
                    Added Date &nbsp; <i class="fas fa-{{ Session::get('customer_field') == 'created_at' ? (Session::get('customer_sort') == 'asc' ? 'sort-up' : 'sort-down') : 'sort' }}"></i>
                </th>
                <th style="width: 20%;">
                    Photo
                </th>
                <th style="width: 20%;">
                    Customer Details
                </th>
                <th style="width: 10%;" class="text-center">
                    Action
                </th>
            </tr>
        </thead>
        <tbody>
            @if (!empty($customers['data']))
                @foreach ($customers['data'] as $customer)
                    <tr>
                        <td>{{ $customer['created_at'] }}</td>
                        <td>
                            @if(!empty($customer['user_image']) && file_exists(public_path().'/images/customer_images/'.$customer['user_image']))
                                <img src="{{ asset('images/customer_images/'.$customer['user_image']) }}" alt="Customer Image" class="list_image">
                            @else
                                <img src="{{ asset('images/no_image.jpg') }}" alt="Customer Image" class="list_image">
                            @endif
                        </td>
                        <td>
                            <b>Name : </b> {{ $customer['first_name'] }}</br>
                            <b>Email : </b> {{ $customer['email'] }}</br>
                            <b>Phone : </b> {{ $customer['phone_no'] }}</br>
                        </td>
                        <td class="text-center">
                            <a href="{{url(\Config::get('constants.ADMIN_URL'))}}/customer/{{$customer['id']}}/edit"><i class="fa fa-edit"></i></a> &nbsp;
                            <a href="javascript:void(0);" onclick="delete_conf_common('{{$customer['id']}}','User','users','Customer','list_customer','URL');"><i class="fa fa-trash-alt text-danger"></i></a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="center">No Record found.</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<div class="card-footer clearfix">
    @if (!empty($customers['data']))
        <div class="text-sm fl-left w-50">
            Showing {{ $customers['from'] }} to {{ $customers['to'] }} of {{ $customers['total'] }} entries
        </div>
        <div class="fl-right w-50">
            @php
                $prev_page = $next_page = '';
                if (!empty($customers['prev_page_url'])) {
                    $prev_page = getPageFromUrl($customers['prev_page_url']);
                }
                if (!empty($customers['next_page_url'])) {
                    $next_page = getPageFromUrl($customers['next_page_url']);
                }
            @endphp

            <ul class="custom_pagination_button pagination-sm m-0 float-right">
                <li class="page-item" pagination_page="{{ $prev_page }}">
                    <a class="page-link {{ !empty($prev_page) ? 'filter_by_click' : 'not-allowed' }}" href="javascript:void(0);" pagination_page="{{ $prev_page }}">&laquo;</a>
                </li>
                @php
                    $start_from = getPageStartCount($customers['current_page'],$customers['last_page']);
                @endphp
                @for ($i = $start_from; $i < $start_from + Config::get('constants.PAGINATION_PAGES'); $i++)
                    @if ($i <= $customers['last_page'] && $i > 0)
                        @if ($i < $customers['current_page'] + Config::get('constants.PAGINATION_PAGES'))
                            <li class="page-item">
                                <a class="page-link filter_by_click {{ $customers['current_page'] == $i ? 'current not-allowed' : ''}}" href="javascript:void(0);" pagination_page="{{ $i }}">{{ $i }}</a>
                            </li>
                        @endif
                    @endif
                @endfor
                <li class="page-item">
                    <a class="page-link {{ !empty($next_page) ? 'filter_by_click' : 'not-allowed' }}" href="javascript:void(0);" pagination_page="{{ $next_page }}">&raquo;</a>
                </li>
            </ul>
        </div>
    @endif
</div>