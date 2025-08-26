<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        {{-- <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-shield-alt"></i>
        </div> --}}
        <img src=" {{ asset('images/parth_logo.png') }}" style="max-width: 224px;">
    </a>

    <hr class="sidebar-divider my-0">

    @hasrole('Admin')
        <li class="nav-item {{ Route::currentRouteName() == 'home' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('home') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>
    @endhasrole

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Management
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link {{ Route::currentRouteName() == 'customers.index' || Route::currentRouteName() == 'customers.create' || Route::currentRouteName() == 'customers.edit' ? '' : 'collapsed' }}"
            href="#" data-toggle="collapse" data-target="#taTpDropDownCustomer"
            aria-expanded="{{ Route::currentRouteName() == 'customers.index' || Route::currentRouteName() == 'customers.create' || Route::currentRouteName() == 'customers.edit' ? 'true' : 'false' }}"
            aria-controls="taTpDropDownCustomer">
            <i class="fas fa-user-alt"></i>
            <span>Customer Management</span>
        </a>
        <div id="taTpDropDownCustomer"
            class="collapse {{ Route::currentRouteName() == 'customers.index' || Route::currentRouteName() == 'customers.create' || Route::currentRouteName() == 'customers.edit' ? 'show' : '' }}"
            aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Customer Management:</h6>
                <a class="collapse-item {{ Route::currentRouteName() == 'customers.index' ? 'active' : '' }}"
                    href="{{ route('customers.index') }}">List</a>
                <a class="collapse-item {{ Route::currentRouteName() == 'customers.edit' || Route::currentRouteName() == 'customers.create' ? 'active' : '' }} "
                    href="{{ route('customers.create') }}">Add New</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link {{ Route::currentRouteName() == 'customer_insurances.index' || Route::currentRouteName() == 'customer_insurances.create' || Route::currentRouteName() == 'customer_insurances.edit' ? '' : 'collapsed' }}"
            href="#" data-toggle="collapse" data-target="#taTpDropDownCustomerInsurance"
            aria-expanded="{{ Route::currentRouteName() == 'customer_insurances.index' || Route::currentRouteName() == 'customer_insurances.create' || Route::currentRouteName() == 'customer_insurances.edit' ? 'true' : 'false' }}"
            aria-controls="taTpDropDownCustomerInsurance">
            <i class="fas fa-user-alt"></i>
            <span>Customer Insurance Management</span>
        </a>
        <div id="taTpDropDownCustomerInsurance"
            class="collapse {{ Route::currentRouteName() == 'customer_insurances.index' || Route::currentRouteName() == 'customer_insurances.create' || Route::currentRouteName() == 'customer_insurances.edit' ? 'show' : '' }}"
            aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Customer Insurance:</h6>
                <a class="collapse-item {{ Route::currentRouteName() == 'customer_insurances.index' ? 'active' : '' }}"
                    href="{{ route('customer_insurances.index') }}">List</a>
                <a class="collapse-item {{ Route::currentRouteName() == 'customer_insurances.edit' || Route::currentRouteName() == 'customer_insurances.create' ? 'active' : '' }}"
                    href="{{ route('customer_insurances.create') }}">Add New</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Family Groups Management -->
    <li class="nav-item">
        <a class="nav-link {{ str_contains(Route::currentRouteName(), 'family_groups.') ? '' : 'collapsed' }}"
            href="#" data-toggle="collapse" data-target="#taTpDropDownFamilyGroups"
            aria-expanded="{{ str_contains(Route::currentRouteName(), 'family_groups.') ? 'true' : 'false' }}"
            aria-controls="taTpDropDownFamilyGroups">
            <i class="fas fa-users"></i>
            <span>Family Groups Management</span>
        </a>
        <div id="taTpDropDownFamilyGroups"
            class="collapse {{ str_contains(Route::currentRouteName(), 'family_groups.') ? 'show' : '' }}"
            aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Family Groups:</h6>
                <a class="collapse-item {{ Route::currentRouteName() == 'family_groups.index' ? 'active' : '' }}"
                    href="{{ route('family_groups.index') }}">List Groups</a>
                <a class="collapse-item {{ str_contains(Route::currentRouteName(), 'family_groups.create') ? 'active' : '' }}"
                    href="{{ route('family_groups.create') }}">Create New Group</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Quotations Management -->
    @can('quotation-list')
        <li class="nav-item">
            <a class="nav-link {{ str_contains(Route::currentRouteName(), 'quotations.') ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#taTpDropDownQuotations"
                aria-expanded="{{ str_contains(Route::currentRouteName(), 'quotations.') ? 'true' : 'false' }}"
                aria-controls="taTpDropDownQuotations">
                <i class="fas fa-file-alt"></i>
                <span>Quotation Management</span>
            </a>
            <div id="taTpDropDownQuotations"
                class="collapse {{ str_contains(Route::currentRouteName(), 'quotations.') ? 'show' : '' }}"
                aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Insurance Quotations:</h6>
                    <a class="collapse-item {{ Route::currentRouteName() == 'quotations.index' ? 'active' : '' }}"
                        href="{{ route('quotations.index') }}">List Quotations</a>
                    @can('quotation-create')
                        <a class="collapse-item {{ str_contains(Route::currentRouteName(), 'quotations.create') ? 'active' : '' }}"
                            href="{{ route('quotations.create') }}">Create New Quote</a>
                    @endcan
                </div>
            </div>
        </li>
    @endcan

    <!-- Divider -->
    <hr class="sidebar-divider">

    @hasrole('Admin')
        <!-- Heading -->
        <div class="sidebar-heading">
            Admin Section
        </div>

        <li class="nav-item {{ Route::currentRouteName() == 'reports.index' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('reports.index') }}">
                <i class="fas fa-fw fa-chart-line"></i>
                <span>Reports</span></a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'brokers.index' || Route::currentRouteName() == 'brokers.create' || Route::currentRouteName() == 'brokers.edit' ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#taTpDropDownBroker"
                aria-expanded="{{ Route::currentRouteName() == 'brokers.index' || Route::currentRouteName() == 'brokers.create' || Route::currentRouteName() == 'brokers.edit' ? 'true' : 'false' }}"
                aria-controls="taTpDropDownBroker">
                <i class="fas fa-user-alt"></i>
                <span>Broker Management</span>
            </a>
            <div id="taTpDropDownBroker"
                class="collapse {{ Route::currentRouteName() == 'brokers.index' || Route::currentRouteName() == 'brokers.create' || Route::currentRouteName() == 'brokers.edit' ? 'show' : '' }}"
                aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Broker Management:</h6>
                    <a class="collapse-item {{ Route::currentRouteName() == 'brokers.index' ? 'active' : '' }}"
                        href="{{ route('brokers.index') }}">List</a>
                    <a class="collapse-item {{ Route::currentRouteName() == 'brokers.edit' || Route::currentRouteName() == 'brokers.create' ? 'active' : '' }}"
                        href="{{ route('brokers.create') }}">Add New</a>
                </div>
            </div>
        </li>


        <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'relationship_managers.index' || Route::currentRouteName() == 'relationship_managers.create' || Route::currentRouteName() == 'relationship_managers.edit' ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#taTpDropDownRelationship"
                aria-expanded="{{ Route::currentRouteName() == 'relationship_managers.index' || Route::currentRouteName() == 'relationship_managers.create' || Route::currentRouteName() == 'relationship_managers.edit' ? 'true' : 'false' }}"
                aria-controls="taTpDropDownRelationship">
                <i class="fas fa-user-alt"></i>
                <span>RM Management</span>
            </a>
            <div id="taTpDropDownRelationship"
                class="collapse {{ Route::currentRouteName() == 'relationship_managers.index' || Route::currentRouteName() == 'relationship_managers.create' || Route::currentRouteName() == 'relationship_managers.edit' ? 'show' : '' }}"
                aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">RM Management:</h6>
                    <a class="collapse-item {{ Route::currentRouteName() == 'relationship_managers.index' ? 'active' : '' }}"
                        href="{{ route('relationship_managers.index') }}">List</a>
                    <a class="collapse-item {{ Route::currentRouteName() == 'relationship_managers.edit' || Route::currentRouteName() == 'relationship_managers.create' ? 'active' : '' }}"
                        href="{{ route('relationship_managers.create') }}">Add New</a>
                </div>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'insurance_companies.index' || Route::currentRouteName() == 'insurance_companies.create' || Route::currentRouteName() == 'insurance_companies.edit' ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#taTpDropDownInsuranceCompany"
                aria-expanded="{{ Route::currentRouteName() == 'insurance_companies.index' || Route::currentRouteName() == 'insurance_companies.create' || Route::currentRouteName() == 'insurance_companies.edit' ? 'true' : 'false' }}"
                aria-controls="taTpDropDownInsuranceCompany">
                <i class="fas fa-user-alt"></i>
                <span>Insurance Company</span>
            </a>
            <div id="taTpDropDownInsuranceCompany"
                class="collapse {{ Route::currentRouteName() == 'insurance_companies.index' || Route::currentRouteName() == 'insurance_companies.create' || Route::currentRouteName() == 'insurance_companies.edit' ? 'show' : '' }}"
                aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Insurance Company:</h6>
                    <a class="collapse-item {{ Route::currentRouteName() == 'insurance_companies.index' ? 'active' : '' }}"
                        href="{{ route('insurance_companies.index') }}"
                        href="{{ route('insurance_companies.index') }}">List</a>
                    <a class="collapse-item {{ Route::currentRouteName() == 'insurance_companies.edit' || Route::currentRouteName() == 'insurance_companies.create' ? 'active' : '' }}"
                        href="{{ route('insurance_companies.create') }}">Add New</a>
                </div>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'policy_type.index' || Route::currentRouteName() == 'policy_type.create' || Route::currentRouteName() == 'policy_type.edit' ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#taTpDropDownPolicyType"
                aria-expanded="{{ Route::currentRouteName() == 'policy_type.index' || Route::currentRouteName() == 'policy_type.create' || Route::currentRouteName() == 'policy_type.edit' ? 'true' : 'false' }}"
                aria-controls="taTpDropDownPolicyType">
                <i class="fas fa-user-alt"></i>
                <span>Policy Type</span>
            </a>
            <div id="taTpDropDownPolicyType"
                class="collapse {{ Route::currentRouteName() == 'policy_type.index' || Route::currentRouteName() == 'policy_type.create' || Route::currentRouteName() == 'policy_type.edit' ? 'show' : '' }}"
                aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Policy Type:</h6>
                    <a class="collapse-item {{ Route::currentRouteName() == 'policy_type.index' ? 'active' : '' }}"
                        href="{{ route('policy_type.index') }}">List</a>
                    <a class="collapse-item {{ Route::currentRouteName() == 'policy_type.edit' || Route::currentRouteName() == 'policy_type.create' ? 'active' : '' }}"
                        href="{{ route('policy_type.create') }}">Add New</a>
                </div>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'fuel_type.index' || Route::currentRouteName() == 'fuel_type.create' || Route::currentRouteName() == 'fuel_type.edit' ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#taTpDropDownFuelType"
                aria-expanded="{{ Route::currentRouteName() == 'fuel_type.index' || Route::currentRouteName() == 'fuel_type.create' || Route::currentRouteName() == 'fuel_type.edit' ? 'true' : 'false' }}"
                aria-controls="taTpDropDownFuelType">
                <i class="fas fa-user-alt"></i>
                <span>Fuel Type</span>
            </a>
            <div id="taTpDropDownFuelType"
                class="collapse {{ Route::currentRouteName() == 'fuel_type.index' || Route::currentRouteName() == 'fuel_type.create' || Route::currentRouteName() == 'fuel_type.edit' ? 'show' : '' }}"
                aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Fuel Type:</h6>
                    <a class="collapse-item {{ Route::currentRouteName() == 'fuel_type.index' ? 'active' : '' }}"
                        href="{{ route('fuel_type.index') }}" href="{{ route('fuel_type.index') }}">List</a>
                    <a class="collapse-item {{ Route::currentRouteName() == 'fuel_type.edit' || Route::currentRouteName() == 'fuel_type.create' ? 'active' : '' }}"
                        href="{{ route('fuel_type.create') }}">Add New</a>
                </div>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'premium_type.index' || Route::currentRouteName() == 'premium_type.create' || Route::currentRouteName() == 'premium_type.edit' ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#taTpDropDownPremiumType"
                aria-expanded="{{ Route::currentRouteName() == 'premium_type.index' || Route::currentRouteName() == 'premium_type.create' || Route::currentRouteName() == 'premium_type.edit' ? 'true' : 'false' }}"
                aria-controls="taTpDropDownPremiumType">
                <i class="fas fa-user-alt"></i>
                <span>Premium Type</span>
            </a>
            <div id="taTpDropDownPremiumType"
                class="collapse {{ Route::currentRouteName() == 'premium_type.index' || Route::currentRouteName() == 'premium_type.create' || Route::currentRouteName() == 'premium_type.edit' ? 'show' : '' }}"
                aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Premium Type:</h6>
                    <a class="collapse-item {{ Route::currentRouteName() == 'premium_type.index' ? 'active' : '' }}"
                        href="{{ route('premium_type.index') }}">List</a>
                    <a class="collapse-item {{ Route::currentRouteName() == 'premium_type.edit' || Route::currentRouteName() == 'premium_type.create' ? 'active' : '' }}"
                        href="{{ route('premium_type.create') }}">Add New</a>
                </div>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'users.index' || Route::currentRouteName() == 'users.create' || Route::currentRouteName() == 'users.edit' ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#taTpDropDown"
                aria-expanded="{{ Route::currentRouteName() == 'users.index' || Route::currentRouteName() == 'users.create' || Route::currentRouteName() == 'users.edit' ? 'true' : 'false' }}"
                aria-controls="taTpDropDown">
                <i class="fas fa-user-alt"></i>
                <span>User Management</span>
            </a>
            <div id="taTpDropDown"
                class="collapse {{ Route::currentRouteName() == 'users.index' || Route::currentRouteName() == 'users.create' || Route::currentRouteName() == 'users.edit' ? 'show' : '' }}"
                aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">User Management:</h6>
                    <a class="collapse-item {{ Route::currentRouteName() == 'users.index' ? 'active' : '' }}"
                        href="{{ route('users.index') }}">List</a>
                    <a class="collapse-item {{ Route::currentRouteName() == 'users.edit' || Route::currentRouteName() == 'users.create' ? 'active' : '' }}"
                        href="{{ route('users.create') }}">Add New</a>
                </div>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'reference_users.index' || Route::currentRouteName() == 'reference_users.create' || Route::currentRouteName() == 'reference_users.edit' ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#taTpDropDown"
                aria-expanded="{{ Route::currentRouteName() == 'reference_users.index' || Route::currentRouteName() == 'reference_users.create' || Route::currentRouteName() == 'reference_users.edit' ? 'true' : 'false' }}"
                aria-controls="taTpDropDown">
                <i class="fas fa-user-alt"></i>
                <span>Reference Management</span>
            </a>
            <div id="taTpDropDown"
                class="collapse {{ Route::currentRouteName() == 'reference_users.index' || Route::currentRouteName() == 'reference_users.create' || Route::currentRouteName() == 'reference_users.edit' ? 'show' : '' }}"
                aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Reference By:</h6>
                    <a class="collapse-item {{ Route::currentRouteName() == 'reference_users.index' ? 'active' : '' }}"
                        href="{{ route('reference_users.index') }}">List</a>
                    <a class="collapse-item {{ Route::currentRouteName() == 'reference_users.edit' || Route::currentRouteName() == 'reference_users.create' ? 'active' : '' }}"
                        href="{{ route('reference_users.create') }}">Add New</a>
                </div>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'permissions.index' || Route::currentRouteName() == 'permissions.create' || Route::currentRouteName() == 'permissions.edit' ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#collapsePages"
                aria-expanded="{{ Route::currentRouteName() == 'permissions.index' || Route::currentRouteName() == 'permissions.create' || Route::currentRouteName() == 'permissions.edit' ? 'true' : 'false' }}"
                aria-controls="collapsePages">
                <i class="fas fa-fw fa-folder"></i>
                <span>Masters</span>
            </a>
            <div id="collapsePages"
                class="collapse {{ Route::currentRouteName() == 'permissions.index' || Route::currentRouteName() == 'permissions.create' || Route::currentRouteName() == 'permissions.edit' || Route::currentRouteName() == 'roles.index' || Route::currentRouteName() == 'roles.create' || Route::currentRouteName() == 'roles.edit' ? 'show' : '' }}"
                aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Role & Permissions</h6>
                    <a class="collapse-item {{ Route::currentRouteName() == 'roles.index' || Route::currentRouteName() == 'roles.create' || Route::currentRouteName() == 'roles.edit' ? 'active' : '' }}"
                        href="{{ route('roles.index') }}">Roles</a>
                    <a class="collapse-item {{ Route::currentRouteName() == 'permissions.index' || Route::currentRouteName() == 'permissions.create' || Route::currentRouteName() == 'permissions.edit' ? 'active' : '' }}"
                        href="{{ route('permissions.index') }}">Permissions</a>
                </div>
            </div>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">
    @endhasrole

    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
