<style>
    :root {
        --primary-color: #0d6efd;
        --secondary-color: #6c757d;
        --success-color: #198754;
        --info-color: #0dcaf0;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --dark-color: #212529;
        --light-color: #f8f9fa;
    }

    body {
        font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background-color: var(--dark-color);
        color: var(--light-color);
    }

    .app-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .app-main {
        flex: 1;
        margin-top: 3.5rem;
    }

    .app-header {
        position: fixed;
        top: 0;
        right: 0;
        left: 0;
        z-index: 1030;
        background-color: #1a1d20;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        height: 3.5rem;
    }

    .app-header .navbar {
        height: 100%;
        background-color: #1a1d20;
    }

    .app-header .navbar-nav .nav-link {
        color: #fff;
        padding: 0.5rem 1rem;
    }

    .app-header .navbar-nav .nav-link:hover {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.1);
    }

    .app-header .navbar-nav .dropdown-menu {
        background-color: #1a1d20;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .app-header .navbar-nav .dropdown-item {
        color: #fff;
    }

    .app-header .navbar-nav .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    .app-header .navbar-nav .dropdown-header {
        color: rgba(255, 255, 255, 0.8);
    }

    .app-header .navbar-nav .dropdown-footer {
        color: rgba(255, 255, 255, 0.8);
    }

    .app-header .navbar-nav .navbar-badge {
        background-color: #fff;
        color: #1a1d20;
    }

    .app-sidebar {
        position: fixed;
        top: 3.5rem;
        left: 0;
        bottom: 0;
        width: 250px;
        background-color: var(--dark-color);
        border-right: 1px solid rgba(255, 255, 255, 0.1);
        overflow-y: auto;
    }

    .sidebar-brand {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1rem;
        height: 3.5rem;
    }

    .brand-link {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-decoration: none;
        height: 100%;
    }

    .brand-text {
        font-weight: 600;
        font-size: 1.2rem;
        margin-left: 0.5rem;
    }

    .nav-sidebar .nav-item .nav-link {
        color: rgba(255, 255, 255, 0.8);
        border-radius: 0.35rem;
        margin: 0.2rem 0.5rem;
        padding: 0.5rem 1rem;
    }

    .nav-sidebar .nav-item .nav-link:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.1);
    }

    .nav-sidebar .nav-item .nav-link.active {
        color: #fff;
        background: rgba(255, 255, 255, 0.2);
    }

    .nav-sidebar .nav-item .nav-link i {
        margin-right: 0.5rem;
    }

    .nav-sidebar .nav-treeview {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 0.35rem;
        margin: 0.5rem;
    }

    .nav-sidebar .nav-treeview .nav-link {
        padding-left: 2rem;
    }

    .card {
        background-color: var(--dark-color);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.35rem;
        margin-top: 0.5rem;
    }

    .card-header {
        background-color: rgba(0, 0, 0, 0.2);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .app-footer {
        background-color: var(--dark-color);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1rem 0;
    }

    .dropdown-menu {
        background-color: var(--dark-color);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .dropdown-item {
        color: rgba(255, 255, 255, 0.8);
    }

    .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    .dropdown-divider {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .breadcrumb {
        background-color: transparent;
        margin-bottom: 0.5rem;
    }

    .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.8);
    }

    .breadcrumb-item.active {
        color: #fff;
    }

    .alert {
        background-color: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    .app-content {
        margin-left: 250px;
        padding: 0.5rem;
    }

    .app-content-header {
        padding: 0.25rem 0;
        margin-bottom: 0.5rem;
    }

    .app-content-header h3 {
        color: #fff;
        font-weight: 600;
        margin: 0;
    }

    .app-content-header .breadcrumb {
        background-color: transparent;
        margin-bottom: 0;
    }

    .app-content-header .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
    }

    .app-content-header .breadcrumb-item.active {
        color: #fff;
    }

    .app-content-header .breadcrumb-item+.breadcrumb-item::before {
        color: rgba(255, 255, 255, 0.5);
    }

    .container-fluid {
        padding: 0 1rem;
    }

    @media (max-width: 767.98px) {
        .app-sidebar {
            width: 100%;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }

        .app-sidebar.show {
            transform: translateX(0);
        }

        .app-content {
            margin-left: 0;
        }
    }

    .app-content h2 {
        color: #fff;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .app-content .col h2 {
        color: #fff;
        font-weight: 600;
        margin-bottom: 1rem;
    }
</style>
