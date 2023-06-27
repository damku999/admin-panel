<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div class="sidebar-brand-text mx-3">WebMonks Technologies</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('home') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Management
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#taTpDropDownCustomer"
            aria-expanded="true" aria-controls="taTpDropDownCustomer">
            <i class="fas fa-user-alt"></i>
            <span>Customer Management</span>
        </a>
        <div id="taTpDropDownCustomer" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Customer Management:</h6>
                <a class="collapse-item" href="{{ route('customers.index') }}">List</a>
                <a class="collapse-item" href="{{ route('customers.create') }}">Add New</a>
                {{-- <a class="collapse-item" href="{{ route('users.import') }}">Import Data</a> --}}
            </div>
        </div>
    </li>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#taTpDropDownBroker"
            aria-expanded="true" aria-controls="taTpDropDownBroker">
            <i class="fas fa-user-alt"></i>
            <span>Broker Management</span>
        </a>
        <div id="taTpDropDownBroker" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Broker Management:</h6>
                <a class="collapse-item" href="{{ route('brokers.index') }}">List</a>
                <a class="collapse-item" href="{{ route('brokers.create') }}">Add New</a>
                {{-- <a class="collapse-item" href="{{ route('users.import') }}">Import Data</a> --}}
            </div>
        </div>
    </li>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#taTpDropDownRelationship"
            aria-expanded="true" aria-controls="taTpDropDownRelationship">
            <i class="fas fa-user-alt"></i>
            <span>RM Management</span>
        </a>
        <div id="taTpDropDownRelationship" class="collapse" aria-labelledby="headingTwo"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">RM Management:</h6>
                <a class="collapse-item" href="{{ route('relationship_managers.index') }}">List</a>
                <a class="collapse-item" href="{{ route('relationship_managers.create') }}">Add New</a>
            </div>
        </div>
    </li>
    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#taTpDropDownInsuranceCompany"
            aria-expanded="true" aria-controls="taTpDropDownInsuranceCompany">
            <i class="fas fa-user-alt"></i>
            <span>Insurance Company</span>
        </a>
        <div id="taTpDropDownInsuranceCompany" class="collapse" aria-labelledby="headingTwo"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Insurance Company:</h6>
                <a class="collapse-item" href="{{ route('insurance_companies.index') }}">List</a>
                <a class="collapse-item" href="{{ route('insurance_companies.create') }}">Add New</a>
            </div>
        </div>
    </li>
    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#taTpDropDownCustomerInsurance"
            aria-expanded="true" aria-controls="taTpDropDownCustomerInsurance">
            <i class="fas fa-user-alt"></i>
            <span>Customer Insurance Management</span>
        </a>
        <div id="taTpDropDownCustomerInsurance" class="collapse" aria-labelledby="headingTwo"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Customer Insurance:</h6>
                <a class="collapse-item" href="{{ route('customer_insurances.index') }}">List</a>
                <a class="collapse-item" href="{{ route('customer_insurances.create') }}">Add New</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#taTpDropDown"
            aria-expanded="true" aria-controls="taTpDropDown">
            <i class="fas fa-user-alt"></i>
            <span>User Management</span>
        </a>
        <div id="taTpDropDown" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">User Management:</h6>
                <a class="collapse-item" href="{{ route('users.index') }}">List</a>
                <a class="collapse-item" href="{{ route('users.create') }}">Add New</a>
                {{-- <a class="collapse-item" href="{{ route('users.import') }}">Import Data</a> --}}
            </div>
        </div>
    </li>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#taTpDropDownPolicyType"
            aria-expanded="true" aria-controls="taTpDropDownPolicyType">
            <i class="fas fa-user-alt"></i>
            <span>Policy Type</span>
        </a>
        <div id="taTpDropDownPolicyType" class="collapse" aria-labelledby="headingTwo"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Policy Type:</h6>
                <a class="collapse-item" href="{{ route('policy_type.index') }}">List</a>
                <a class="collapse-item" href="{{ route('policy_type.create') }}">Add New</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#taTpDropDownFuelType"
            aria-expanded="true" aria-controls="taTpDropDownFuelType">
            <i class="fas fa-user-alt"></i>
            <span>Fuel Type</span>
        </a>
        <div id="taTpDropDownFuelType" class="collapse" aria-labelledby="headingTwo"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Fuel Type:</h6>
                <a class="collapse-item" href="{{ route('fuel_type.index') }}">List</a>
                <a class="collapse-item" href="{{ route('fuel_type.create') }}">Add New</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#taTpDropDownPremiumType"
            aria-expanded="true" aria-controls="taTpDropDownPremiumType">
            <i class="fas fa-user-alt"></i>
            <span>Premium Type</span>
        </a>
        <div id="taTpDropDownPremiumType" class="collapse" aria-labelledby="headingTwo"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Premium Type:</h6>
                <a class="collapse-item" href="{{ route('premium_type.index') }}">List</a>
                <a class="collapse-item" href="{{ route('premium_type.create') }}">Add New</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    @hasrole('Admin')
        <!-- Heading -->
        <div class="sidebar-heading">
            Admin Section
        </div>

        <!-- Nav Item - Pages Collapse Menu -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                aria-expanded="true" aria-controls="collapsePages">
                <i class="fas fa-fw fa-folder"></i>
                <span>Masters</span>
            </a>
            <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Role & Permissions</h6>
                    <a class="collapse-item" href="{{ route('roles.index') }}">Roles</a>
                    <a class="collapse-item" href="{{ route('permissions.index') }}">Permissions</a>
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
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>


</ul>
