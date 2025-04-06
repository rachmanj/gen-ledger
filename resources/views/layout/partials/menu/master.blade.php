<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-database"></i>
        <p>
            Master Data
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('account_types.index') }}"
                class="nav-link {{ request()->routeIs('account_types.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Account Types</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('accounts.index') }}"
                class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Accounts</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('je-temps.import-form') }}"
                class="nav-link {{ request()->routeIs('je-temps.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Import Journal Entry</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('account-statement.index') }}"
                class="nav-link {{ request()->routeIs('account-statement.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Account Statement</p>
            </a>
        </li>
    </ul>
</li>
