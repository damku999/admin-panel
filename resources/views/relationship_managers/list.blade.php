<div class="card-body table-responsive p-0" style="max-height: 580px;">
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th style="width: 20%;" class="filter_by_click pointer" sort_field="created_at">
                    Added Date &nbsp; <i class="fas fa-{{ Session::get('relationship_manager_field') == 'created_at' ? (Session::get('relationship_manager_sort') == 'asc' ? 'sort-up' : 'sort-down') : 'sort' }}"></i>
                </th>
                <th style="width: 20%;">
                    Photo
                </th>
                <th style="width: 20%;">
                    Relationship Manager Details
                </th>
                <th style="width: 10%;" class="text-center">
                    Action
                </th>
            </tr>
        </thead>
        <tbody>
            @if (!empty($relationship_managers['data']))
                @foreach ($relationship_managers['data'] as $relationship_manager)
                    <tr>
                        <td>{{ $relationship_manager['created_at'] }}</td>
                        <td>
                            @if(!empty($relationship_manager['user_image']) && file_exists(public_path().'/images/relationship_manager_images/'.$relationship_manager['user_image']))
                                <img src="{{ asset('images/relationship_manager_images/'.$relationship_manager['user_image']) }}" alt="Relationship Manager Image" class="list_image">
                            @else
                                <img src="{{ asset('images/no_image.jpg') }}" alt="Relationship Manager Image" class="list_image">
                            @endif
                        </td>
                        <td>
                            <b>Name : </b> {{ $relationship_manager['first_name'] }}</br>
                            <b>Email : </b> {{ $relationship_manager['email'] }}</br>
                            <b>Phone : </b> {{ $relationship_manager['phone_no'] }}</br>
                        </td>
                        <td class="text-center">
                            <a href="{{url(\Config::get('constants.ADMIN_URL'))}}/relationship_manager/{{$relationship_manager['id']}}/edit"><i class="fa fa-edit"></i></a> &nbsp;
                            <a href="javascript:void(0);" onclick="delete_conf_common('{{$relationship_manager['id']}}','User','users','Relationship Manager','list_relationship_manager','URL');"><i class="fa fa-trash-alt text-danger"></i></a>
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
    @if (!empty($relationship_managers['data']))
        <div class="text-sm fl-left w-50">
            Showing {{ $relationship_managers['from'] }} to {{ $relationship_managers['to'] }} of {{ $relationship_managers['total'] }} entries
        </div>
        <div class="fl-right w-50">
            @php
                $prev_page = $next_page = '';
                if (!empty($relationship_managers['prev_page_url'])) {
                    $prev_page = getPageFromUrl($relationship_managers['prev_page_url']);
                }
                if (!empty($relationship_managers['next_page_url'])) {
                    $next_page = getPageFromUrl($relationship_managers['next_page_url']);
                }
            @endphp

            <ul class="custom_pagination_button pagination-sm m-0 float-right">
                <li class="page-item" pagination_page="{{ $prev_page }}">
                    <a class="page-link {{ !empty($prev_page) ? 'filter_by_click' : 'not-allowed' }}" href="javascript:void(0);" pagination_page="{{ $prev_page }}">&laquo;</a>
                </li>
                @php
                    $start_from = getPageStartCount($relationship_managers['current_page'],$relationship_managers['last_page']);
                @endphp
                @for ($i = $start_from; $i < $start_from + Config::get('constants.PAGINATION_PAGES'); $i++)
                    @if ($i <= $relationship_managers['last_page'] && $i > 0)
                        @if ($i < $relationship_managers['current_page'] + Config::get('constants.PAGINATION_PAGES'))
                            <li class="page-item">
                                <a class="page-link filter_by_click {{ $relationship_managers['current_page'] == $i ? 'current not-allowed' : ''}}" href="javascript:void(0);" pagination_page="{{ $i }}">{{ $i }}</a>
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