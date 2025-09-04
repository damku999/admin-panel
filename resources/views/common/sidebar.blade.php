<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <img src=" {{ asset('images/parth_logo.png') }}" style="max-width: 224px;">
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
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
        Core Management
    </div>

    <!-- Customer Management -->
    <li class="nav-item">
        <a class="nav-link {{ Route::currentRouteName() == 'customers.index' || Route::currentRouteName() == 'customers.create' || Route::currentRouteName() == 'customers.edit' ? '' : 'collapsed' }}"
            href="#" data-toggle="collapse" data-target="#taTpDropDownCustomer"
            aria-expanded="{{ Route::currentRouteName() == 'customers.index' || Route::currentRouteName() == 'customers.create' || Route::currentRouteName() == 'customers.edit' ? 'true' : 'false' }}"
            aria-controls="taTpDropDownCustomer">
            <i class="fas fa-users"></i>
            <span>Customers</span>
        </a>
        <div id="taTpDropDownCustomer"
            class="collapse {{ Route::currentRouteName() == 'customers.index' || Route::currentRouteName() == 'customers.create' || Route::currentRouteName() == 'customers.edit' ? 'show' : '' }}"
            aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Customer Management:</h6>
                <a class="collapse-item {{ Route::currentRouteName() == 'customers.index' ? 'active' : '' }}"
                    href="{{ route('customers.index') }}">List Customers</a>
                <a class="collapse-item {{ Route::currentRouteName() == 'customers.edit' || Route::currentRouteName() == 'customers.create' ? 'active' : '' }} "
                    href="{{ route('customers.create') }}">Add New Customer</a>
            </div>
        </div>
    </li>

    <!-- Customer Insurance -->
    <li class="nav-item">
        <a class="nav-link {{ Route::currentRouteName() == 'customer_insurances.index' || Route::currentRouteName() == 'customer_insurances.create' || Route::currentRouteName() == 'customer_insurances.edit' ? '' : 'collapsed' }}"
            href="#" data-toggle="collapse" data-target="#taTpDropDownCustomerInsurance"
            aria-expanded="{{ Route::currentRouteName() == 'customer_insurances.index' || Route::currentRouteName() == 'customer_insurances.create' || Route::currentRouteName() == 'customer_insurances.edit' ? 'true' : 'false' }}"
            aria-controls="taTpDropDownCustomerInsurance">
            <i class="fas fa-shield-alt"></i>
            <span>Customer Insurance</span>
        </a>
        <div id="taTpDropDownCustomerInsurance"
            class="collapse {{ Route::currentRouteName() == 'customer_insurances.index' || Route::currentRouteName() == 'customer_insurances.create' || Route::currentRouteName() == 'customer_insurances.edit' ? 'show' : '' }}"
            aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Customer Insurance:</h6>
                <a class="collapse-item {{ Route::currentRouteName() == 'customer_insurances.index' ? 'active' : '' }}"
                    href="{{ route('customer_insurances.index') }}">List Policies</a>
                <a class="collapse-item {{ Route::currentRouteName() == 'customer_insurances.edit' || Route::currentRouteName() == 'customer_insurances.create' ? 'active' : '' }}"
                    href="{{ route('customer_insurances.create') }}">Add New Policy</a>
            </div>
        </div>
    </li>

    <!-- Family Groups -->
    <li class="nav-item">
        <a class="nav-link {{ str_contains(Route::currentRouteName(), 'family_groups.') ? '' : 'collapsed' }}"
            href="#" data-toggle="collapse" data-target="#taTpDropDownFamilyGroups"
            aria-expanded="{{ str_contains(Route::currentRouteName(), 'family_groups.') ? 'true' : 'false' }}"
            aria-controls="taTpDropDownFamilyGroups">
            <i class="fas fa-home"></i>
            <span>Family Groups</span>
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

    <!-- Marketing WhatsApp -->
    <li class="nav-item {{ str_contains(Route::currentRouteName(), 'marketing.whatsapp.') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('marketing.whatsapp.index') }}">
            <i class="fab fa-whatsapp"></i>
            <span>WhatsApp Marketing</span>
        </a>
    </li>

    <!-- Quotations Management -->
    @can('quotation-list')
        <li class="nav-item">
            <a class="nav-link {{ str_contains(Route::currentRouteName(), 'quotations.') ? '' : 'collapsed' }}"
                href="#" data-toggle="collapse" data-target="#taTpDropDownQuotations"
                aria-expanded="{{ str_contains(Route::currentRouteName(), 'quotations.') ? 'true' : 'false' }}"
                aria-controls="taTpDropDownQuotations">
                <i class="fas fa-calculator"></i>
                <span>Quotations</span>
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

    <!-- Reports -->
    @hasrole('Admin')
        <li class="nav-item {{ Route::currentRouteName() == 'reports.index' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('reports.index') }}">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span></a>
        </li>
    @endhasrole

    <!-- Divider -->
    <hr class="sidebar-divider">

    @hasrole('Admin')
        <!-- Heading -->
        <div class="sidebar-heading">
            Master Data
        </div>

        <!-- Master Data Dropdown -->
        <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'insurance_companies.index' || Route::currentRouteName() == 'insurance_companies.create' || Route::currentRouteName() == 'insurance_companies.edit' || Route::currentRouteName() == 'policy_type.index' || Route::currentRouteName() == 'policy_type.create' || Route::currentRouteName() == 'policy_type.edit' || str_starts_with(Route::currentRouteName(), 'addon-covers.') || Route::currentRouteName() == 'premium_type.index' || Route::currentRouteName() == 'premium_type.create' || Route::currentRouteName() == 'premium_type.edit' || Route::currentRouteName() == 'fuel_type.index' || Route::currentRouteName() == 'fuel_type.create' || Route::currentRouteName() == 'fuel_type.edit' ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseMasterData"
                aria-expanded="{{ Route::currentRouteName() == 'insurance_companies.index' || Route::currentRouteName() == 'insurance_companies.create' || Route::currentRouteName() == 'insurance_companies.edit' || Route::currentRouteName() == 'policy_type.index' || Route::currentRouteName() == 'policy_type.create' || Route::currentRouteName() == 'policy_type.edit' || str_starts_with(Route::currentRouteName(), 'addon-covers.') || Route::currentRouteName() == 'premium_type.index' || Route::currentRouteName() == 'premium_type.create' || Route::currentRouteName() == 'premium_type.edit' || Route::currentRouteName() == 'fuel_type.index' || Route::currentRouteName() == 'fuel_type.create' || Route::currentRouteName() == 'fuel_type.edit' ? 'true' : 'false' }}" aria-controls="collapseMasterData">
                <i class="fas fa-cogs"></i>
                <span>Master Data</span>
            </a>
            <div id="collapseMasterData" class="collapse {{ Route::currentRouteName() == 'insurance_companies.index' || Route::currentRouteName() == 'insurance_companies.create' || Route::currentRouteName() == 'insurance_companies.edit' || Route::currentRouteName() == 'policy_type.index' || Route::currentRouteName() == 'policy_type.create' || Route::currentRouteName() == 'policy_type.edit' || str_starts_with(Route::currentRouteName(), 'addon-covers.') || Route::currentRouteName() == 'premium_type.index' || Route::currentRouteName() == 'premium_type.create' || Route::currentRouteName() == 'premium_type.edit' || Route::currentRouteName() == 'fuel_type.index' || Route::currentRouteName() == 'fuel_type.create' || Route::currentRouteName() == 'fuel_type.edit' ? 'show' : '' }}" aria-labelledby="headingMasterData" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Configuration:</h6>
                    
                    <!-- Insurance Companies -->
                    <a class="collapse-item {{ Route::currentRouteName() == 'insurance_companies.index' || Route::currentRouteName() == 'insurance_companies.create' || Route::currentRouteName() == 'insurance_companies.edit' ? 'active' : '' }}" 
                       href="{{ route('insurance_companies.index') }}">
                        <i class="fas fa-building mr-1"></i>Insurance Companies
                    </a>

                    <!-- Policy Types -->
                    <a class="collapse-item {{ Route::currentRouteName() == 'policy_type.index' || Route::currentRouteName() == 'policy_type.create' || Route::currentRouteName() == 'policy_type.edit' ? 'active' : '' }}" 
                       href="{{ route('policy_type.index') }}">
                        <i class="fas fa-file-contract mr-1"></i>Policy Types
                    </a>

                    <!-- Add-on Covers -->
                    <a class="collapse-item {{ str_starts_with(Route::currentRouteName(), 'addon-covers.') ? 'active' : '' }}" 
                       href="{{ route('addon-covers.index') }}">
                        <i class="fas fa-plus-circle mr-1"></i>Add-on Covers
                    </a>

                    <!-- Premium Types -->
                    <a class="collapse-item {{ Route::currentRouteName() == 'premium_type.index' || Route::currentRouteName() == 'premium_type.create' || Route::currentRouteName() == 'premium_type.edit' ? 'active' : '' }}" 
                       href="{{ route('premium_type.index') }}">
                        <i class="fas fa-money-bill-wave mr-1"></i>Premium Types
                    </a>

                    <!-- Fuel Types -->
                    <a class="collapse-item {{ Route::currentRouteName() == 'fuel_type.index' || Route::currentRouteName() == 'fuel_type.create' || Route::currentRouteName() == 'fuel_type.edit' ? 'active' : '' }}" 
                       href="{{ route('fuel_type.index') }}">
                        <i class="fas fa-gas-pump mr-1"></i>Fuel Types
                    </a>
                </div>
            </div>
        </li>

        <!-- Settings Dropdown -->
        <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() == 'users.index' || Route::currentRouteName() == 'users.create' || Route::currentRouteName() == 'users.edit' || Route::currentRouteName() == 'roles.index' || Route::currentRouteName() == 'roles.create' || Route::currentRouteName() == 'roles.edit' || Route::currentRouteName() == 'permissions.index' || Route::currentRouteName() == 'permissions.create' || Route::currentRouteName() == 'permissions.edit' || Route::currentRouteName() == 'brokers.index' || Route::currentRouteName() == 'brokers.create' || Route::currentRouteName() == 'brokers.edit' || Route::currentRouteName() == 'relationship_managers.index' || Route::currentRouteName() == 'relationship_managers.create' || Route::currentRouteName() == 'relationship_managers.edit' || Route::currentRouteName() == 'reference_users.index' || Route::currentRouteName() == 'reference_users.create' || Route::currentRouteName() == 'reference_users.edit' ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseSettings"
                aria-expanded="{{ Route::currentRouteName() == 'users.index' || Route::currentRouteName() == 'users.create' || Route::currentRouteName() == 'users.edit' || Route::currentRouteName() == 'roles.index' || Route::currentRouteName() == 'roles.create' || Route::currentRouteName() == 'roles.edit' || Route::currentRouteName() == 'permissions.index' || Route::currentRouteName() == 'permissions.create' || Route::currentRouteName() == 'permissions.edit' || Route::currentRouteName() == 'brokers.index' || Route::currentRouteName() == 'brokers.create' || Route::currentRouteName() == 'brokers.edit' || Route::currentRouteName() == 'relationship_managers.index' || Route::currentRouteName() == 'relationship_managers.create' || Route::currentRouteName() == 'relationship_managers.edit' || Route::currentRouteName() == 'reference_users.index' || Route::currentRouteName() == 'reference_users.create' || Route::currentRouteName() == 'reference_users.edit' ? 'true' : 'false' }}" aria-controls="collapseSettings">
                <i class="fas fa-tools"></i>
                <span>Settings</span>
            </a>
            <div id="collapseSettings" class="collapse {{ Route::currentRouteName() == 'users.index' || Route::currentRouteName() == 'users.create' || Route::currentRouteName() == 'users.edit' || Route::currentRouteName() == 'roles.index' || Route::currentRouteName() == 'roles.create' || Route::currentRouteName() == 'roles.edit' || Route::currentRouteName() == 'permissions.index' || Route::currentRouteName() == 'permissions.create' || Route::currentRouteName() == 'permissions.edit' || Route::currentRouteName() == 'brokers.index' || Route::currentRouteName() == 'brokers.create' || Route::currentRouteName() == 'brokers.edit' || Route::currentRouteName() == 'relationship_managers.index' || Route::currentRouteName() == 'relationship_managers.create' || Route::currentRouteName() == 'relationship_managers.edit' || Route::currentRouteName() == 'reference_users.index' || Route::currentRouteName() == 'reference_users.create' || Route::currentRouteName() == 'reference_users.edit' ? 'show' : '' }}" aria-labelledby="headingSettings" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">System Settings:</h6>
                    
                    <!-- User Management -->
                    <a class="collapse-item {{ Route::currentRouteName() == 'users.index' || Route::currentRouteName() == 'users.create' || Route::currentRouteName() == 'users.edit' ? 'active' : '' }}" 
                       href="{{ route('users.index') }}">
                        <i class="fas fa-users-cog mr-1"></i>User Management
                    </a>

                    <!-- Roles -->
                    <a class="collapse-item {{ Route::currentRouteName() == 'roles.index' || Route::currentRouteName() == 'roles.create' || Route::currentRouteName() == 'roles.edit' ? 'active' : '' }}" 
                       href="{{ route('roles.index') }}">
                        <i class="fas fa-users-cog mr-1"></i>Roles
                    </a>

                    <!-- Permissions -->
                    <a class="collapse-item {{ Route::currentRouteName() == 'permissions.index' || Route::currentRouteName() == 'permissions.create' || Route::currentRouteName() == 'permissions.edit' ? 'active' : '' }}" 
                       href="{{ route('permissions.index') }}">
                        <i class="fas fa-user-shield mr-1"></i>Permissions
                    </a>

                    <!-- Broker Management -->
                    <a class="collapse-item {{ Route::currentRouteName() == 'brokers.index' || Route::currentRouteName() == 'brokers.create' || Route::currentRouteName() == 'brokers.edit' ? 'active' : '' }}" 
                       href="{{ route('brokers.index') }}">
                        <i class="fas fa-handshake mr-1"></i>Broker Management
                    </a>

                    <!-- RM Management -->
                    <a class="collapse-item {{ Route::currentRouteName() == 'relationship_managers.index' || Route::currentRouteName() == 'relationship_managers.create' || Route::currentRouteName() == 'relationship_managers.edit' ? 'active' : '' }}" 
                       href="{{ route('relationship_managers.index') }}">
                        <i class="fas fa-user-tie mr-1"></i>RM Management
                    </a>

                    <!-- Reference Management -->
                    <a class="collapse-item {{ Route::currentRouteName() == 'reference_users.index' || Route::currentRouteName() == 'reference_users.create' || Route::currentRouteName() == 'reference_users.edit' ? 'active' : '' }}" 
                       href="{{ route('reference_users.index') }}">
                        <i class="fas fa-user-plus mr-1"></i>Reference Management
                    </a>
                </div>
            </div>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">
    @endhasrole

    <!-- Logout -->
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="showLogoutModal()">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>
    
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>