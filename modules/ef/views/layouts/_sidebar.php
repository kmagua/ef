<?php
use yii\helpers\Html;
 $web = Yii::getAlias('@web');
 $backend = yii\helpers\Url::to(['/ef/default/dashboard']);
 $efProjectUrl = Yii::$app->urlManager->createUrl(['/ef/ef-project/index']);
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
@import url('https://cdn.jsdelivr.net/npm/@mdi/font@6.5.95/css/materialdesignicons.min.css');

:root {
    --sidebar-width: 280px;
    --sidebar-background: #008a8a !important;
    --sidebar-text-color: #FFFFFF;
    --sidebar-active-color: #C8E6C9;
    --scrollbar-thumb-color: #A5D6A7;
    --scrollbar-track-color: #E8F5E9;
    --font-family: 'Poppins', sans-serif;
    --transition-speed: 0.3s;
    --border-radius: 6px;
    --shadow-light: 0 2px 8px rgba(0, 0, 0, 0.1);
    --shadow-medium: 0 4px 16px rgba(0, 0, 0, 0.15);
    --shadow-heavy: 0 8px 24px rgba(0, 0, 0, 0.2);
    --logo-bg: #008a8a;
    --logo-hover: #007373;
}

body, .sidebar {
    font-family: var(--font-family);
}

/* Main layout */
.wrapper {
    display: flex;
    min-height: 100vh;
}
.sidebar .nav-link {
    position: relative;
    z-index: 2;
}
.sidebar .nav-item::before {
    z-index: 0;
}

.sidebar {
    background: var(--sidebar-background);
    color: var(--sidebar-text-color);
    width: var(--sidebar-width);
    transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
    overflow-y: auto;
    overflow-x: hidden;
    box-shadow: var(--shadow-medium);
    flex-shrink: 0;
    position: relative;
    z-index: 1000;
}

.main-content {
    flex: 1;
    margin-left: var(--sidebar-width);
    transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
    padding: 20px;
    background: #f8f9fa;
}

/* Header / Navbar */
.navbar {
    background: #008a8a !important;
    color: #FFFFFF !important;
    padding: 10px 15px;
    box-shadow: var(--shadow-light);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
}

/* FIX: remove brown/gold block behind logo */
.navbar-brand-wrapper,
.navbar-brand-wrapper .navbar-brand {
    background: #008a8a !important;
    padding: 0 15px;
    margin: 0;
    display: flex;
    align-items: center;
}

.navbar-brand-wrapper .navbar-brand img {
    height: 50px;
    width: auto;
}

/* Standard brand image rule (still works if wrapper not used) */
.navbar .navbar-brand img {
    height: 50px;
    width: auto;
    transition: transform var(--transition-speed);
}

.navbar .navbar-brand:hover img {
    transform: scale(1.05);
}

.navbar .navbar-nav {
    display: flex;
    align-items: center;
    margin-left: auto;
}

.navbar .navbar-nav .nav-link {
    color: #FFFFFF !important;
    font-weight: 500;
    margin-right: 15px;
    padding: 8px 12px;
    border-radius: var(--border-radius);
 
}

.navbar .navbar-nav .nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
}

.navbar .navbar-nav .nav-link i {
    font-size: 1.2rem;
    margin-right: 5px;
}

/* User Profile Section */
.navbar .user-profile {
    display: flex;
    align-items: center;
    padding: 5px 10px;
    border-radius: 25px;
    background: rgba(255, 255, 255, 0.1);
    transition: all var(--transition-speed);
}

.navbar .user-profile img {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    margin-right: 8px;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.navbar .user-profile:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

/* Optional: breadcrumb styling for professional look */
.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 10px;
    font-size: 0.9rem;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "/";
    padding: 0 6px;
    color: #9e9e9e;
}

.breadcrumb a {
    color: #008a8a;
    font-weight: 500;
}

/* Sidebar Brand Logo (sidebar, not navbar) */
.sidebar-brand-wrapper {
    background: linear-gradient(135deg, var(--logo-bg), #009696) !important;
    padding: 20px 15px;
    height: 90px;
    z-index: 1050;
    border-bottom: 3px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    position: sticky;
    top: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: all var(--transition-speed);
    overflow: hidden;
    position: relative;
    cursor: pointer;
}

.sidebar-brand-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="none"/><path d="M0,0 L100,100 M100,0 L0,100" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></svg>');
    background-size: 20px 20px;
    opacity: 0.5;
    z-index: 0;
}

.sidebar-brand-wrapper img {
    width: 220px;
    height: auto;
    max-height: 60px;
    object-fit: contain;
    transition: all var(--transition-speed);
    z-index: 1;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    animation: logoLoad 0.8s ease-out;
}

.sidebar-brand-wrapper:hover img {
    transform: scale(1.1);
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
}

/* Add a subtle glow effect on hover */
.sidebar-brand-wrapper::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at center, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
    opacity: 0;
    transition: opacity var(--transition-speed);
    z-index: 1;
}

.sidebar-brand-wrapper:hover::after {
    opacity: 1;
}

/* Tooltip for logo */
.sidebar-brand-wrapper .brand-logo::after {
    content: 'Return to Dashboard';
    position: absolute;
    bottom: -30px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    transition: opacity 0.3s, bottom 0.3s;
    pointer-events: none;
    z-index: 10;
}

.sidebar-brand-wrapper:hover .brand-logo::after {
    opacity: 1;
    bottom: -25px;
}

/* Sidebar Navigation */
.sidebar .nav {
    padding-top: 20px;
    padding-bottom: 20px;
}

.sidebar .nav-item {
    transition: all var(--transition-speed);
    margin-bottom: 16px; /* Increased spacing between menu items */
    position: relative;
    overflow: visible;
}

/* Fixed highlight animation for nav items */
.sidebar .nav-item {
    position: relative;
    overflow: visible; /* prevents clipping during the animation */
}

/* Prevent submenu animation from covering text */
.sidebar .nav-item {
    overflow: visible !important;
}

.sidebar .sub-menu {
    overflow: visible !important;
    position: relative;
    z-index: 1;
}

/* make sure text is above the green gradient animation */
.sidebar .sub-menu li a {
    position: relative;
    z-index: 2;
}

.sidebar .nav-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 0;
    background: linear-gradient(90deg, var(--sidebar-active-color), transparent);
    transition: width var(--transition-speed);
    z-index: 0;  /* fixed: was -1 */
}

/* Keep text above the animated background */
.sidebar .nav-link {
    position: relative;
    z-index: 2;
}

.sidebar .nav-item:hover::before,
.sidebar .nav-item.active::before {
    width: 100%;
}

.sidebar .nav-item:hover,
.sidebar .nav-item.active {
    background: var(--sidebar-hover-background);
    border-left: 4px solid var(--sidebar-active-color);
    transform: translateX(5px);
}

.sidebar .nav-link {
    color: var(--sidebar-text-color) !important;
    font-size: 1rem;
    display: flex;
    align-items: center;
    font-weight: 500;
    padding: 14px 20px; /* Increased padding for better touch targets */
    transition: all var(--transition-speed);
    white-space: normal;
    word-break: break-word;
    overflow: hidden;
    text-overflow: ellipsis;
    position: relative;
    border-radius: 8px; /* Added rounded corners */
}

.sidebar .nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 20px;
    right: 20px;
    height: 1px;
    background: rgba(255, 255, 255, 0.1);
    transform: scaleX(0);
}

/* Force all sidebar items and submenus to start fully visible */
.sidebar .nav-item,
.sidebar .nav-link,
.sidebar .sub-menu li a {
    opacity: 1 !important;
    color: #fff !important;
}

/* Disable the fade/slide animation that causes washed-out text */
.sidebar .nav-item {
    animation: none !important;
    transform: none !important;
}

/* Prevent gradient overlays from dimming submenu text */
.sidebar .sub-menu {
    background: none !important;
    overflow: visible !important;
}

/* Make sure text always sits above the sidebar gradients */
.sidebar .nav-link,
.sidebar .sub-menu li a {
    position: relative;
    z-index: 3 !important;
}

.sidebar .nav-link:hover::after {
    transform: scaleX(1);
}

.sidebar .nav-link i {
    margin-right: 12px;
    font-size: 1.3rem;
    flex-shrink: 0;
    transition: all var(--transition-speed);
}

.sidebar .nav-link:hover i {
    transform: scale(1.1);
    color: var(--sidebar-active-color);
}

.sidebar .nav-link span {
    flex: 1;
    white-space: normal;
    word-break: break-word;
    font-size: 0.95rem;
}

.sidebar .menu-title {
    font-weight: 600;
    font-size: 1.1rem;
}

/* Category Headers */
.nav-category {
    color: #90EE90;
    font-weight: 600;
    font-size: 0.85rem;
    padding: 18px 20px 10px; /* Increased padding */
    margin-top: 24px; /* Increased top margin */
    margin-bottom: 16px; /* Increased bottom margin */
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    display: flex;
    align-items: center;
    border-radius: 8px; /* Added rounded corners */
    background: rgba(0, 0, 0, 0.2); /* Added subtle background */
}

.nav-category::before {
    content: '';
    position: absolute;
    left: 20px;
    bottom: 0;
    width: 30px;
    height: 2px;
    background: var(--sidebar-active-color);
    border-radius: 1px;
}

.nav-category::after {
    content: '';
    position: absolute;
    left: 55px;
    bottom: 0;
    width: calc(100% - 75px);
    height: 1px;
    background: rgba(255, 255, 255, 0.2);
}

.sidebar .sub-menu {
    background: var(--sidebar-background) !important;
    border-radius: 8px; /* Added rounded corners */
    margin-top: 8px; /* Added space between parent and submenu */
    padding: 8px 0; /* Added padding */
    position: relative;
    z-index: 10 !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.sidebar .sub-menu.show {
    max-height: 1000px;
    transition: max-height var(--transition-speed) ease-in;
}

.sidebar .sub-menu li {
    position: relative;
    margin-bottom: 6px; /* Added spacing between submenu items */
}

/* REMOVED the blinking dot effect */
.sidebar .sub-menu li::before {
    display: none; /* Hide the dot completely */
}

.sidebar .sub-menu li:hover::before {
    display: none; /* Hide the dot on hover */
}

.sidebar .sub-menu li a {
    white-space: normal;
    word-break: break-word;
    line-height: 1.3;
    padding: 10px 20px 10px 35px !important;
    font-size: 0.9rem;
    transition: all var(--transition-speed);
    border-radius: 6px; /* Added rounded corners */
    display: flex;
    align-items: center;
    position: relative;
    z-index: 15 !important;
    color: #fff !important;
    opacity: 1 !important;
    background: transparent !important;
}

/* Disable highlight background overlay for items with submenu */
.sidebar .nav-item.has-submenu::before {
    display: none !important;
}

/* Disable highlight for any nav-item that contains .sub-menu */
.sidebar .nav-item:has(.sub-menu)::before {
    display: none !important;
}

.sidebar .sub-menu li a:hover {
    background: rgba(255, 255, 255, 0.15) !important; /* Increased opacity for better visibility */
    padding-left: 40px !important;
    transform: translateX(3px); /* Subtle movement on hover */
}

/* Submenu Icons with Circular Background */
.sidebar .sub-menu li a i {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    margin-right: 12px;
    font-size: 1.1rem;
    color: #fff;
    transition: all var(--transition-speed);
    flex-shrink: 0;
}

.sidebar .sub-menu li a:hover i {
    background: rgba(255, 255, 255, 0.25);
    transform: scale(1.05);
    color: var(--sidebar-active-color);
}

/* Make submenu text fully white */
.sidebar .sub-menu li a {
    color: #fff !important;
}

/* Menu Arrow */
.menu-arrow {
    margin-left: auto;
    font-size: 0.8rem;
    transition: transform var(--transition-speed);
    color: rgba(255, 255, 255, 0.7);
}

.nav-link.collapsed .menu-arrow {
    transform: rotate(-90deg);
}

.nav-link:hover .menu-arrow {
    color: var(--sidebar-active-color);
}

/* Sidebar Scrollbar */
.sidebar::-webkit-scrollbar {
    width: 8px;
}

.sidebar::-webkit-scrollbar-thumb {
    background: var(--scrollbar-thumb-color);
    border-radius: 10px;
    border: 2px solid var(--sidebar-background);
}

.sidebar::-webkit-scrollbar-track {
    background: var(--scrollbar-track-color);
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: #8BC34A;
}

/* Collapsed Sidebar */
.sidebar.collapsed {
    width: 70px;
}

.sidebar.collapsed .menu-title,
.sidebar.collapsed .sub-menu,
.sidebar.collapsed .nav-link span,
.sidebar.collapsed .nav-category,
.sidebar.collapsed .menu-arrow {
    display: none;
}

.sidebar.collapsed .nav-link {
    justify-content: center;
    padding: 15px 0;
}

.sidebar.collapsed .nav-link i {
    margin: 0;
    font-size: 1.4rem;
}

.sidebar.collapsed + .main-content {
    margin-left: 70px;
}

/* Logout Button */
.logout-item {
    margin-top: 36px; /* Increased top margin for more spacing */
    padding: 0 10px;
    position: relative;
}

.logout-item::before {
    content: '';
    position: absolute;
    top: -18px; /* Adjusted position */
    left: 20px;
    right: 20px;
    height: 1px;
    background: rgba(255, 255, 255, 0.2);
}

/* make dashboard active item black */
.nav-item.black-active,
.nav-item.black-active .nav-link {
    background: #000 !important;
    border-left: 4px solid #C8E6C9 !important;
    color: #fff !important;
    border-radius: 8px; /* Added rounded corners */
}

/* ensure icon and text are white */
.nav-item.black-active i,
.nav-item.black-active .menu-title {
    color: #fff !important;
}

/* hover stays black */
.nav-item.black-active:hover {
    background: #111 !important;
}

.logout-btn {
    background: linear-gradient(135deg, #d9534f, #c9302c);
    color: white;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 14px 15px; /* Increased padding */
    border-radius: var(--border-radius);
    width: 100%;
    border: none;
    cursor: pointer;
    transition: all var(--transition-speed);
    box-shadow: var(--shadow-light);
    position: relative;
    overflow: hidden;
    border-radius: 8px; /* Added rounded corners */
}

/* Add this rule to ensure nav items are always visible */
.sidebar .nav .nav-item {
    opacity: 1 !important;
    animation: none !important;
}

.logout-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left var(--transition-speed);
}

.logout-btn:hover::before {
    left: 100%;
}

.logout-btn:hover {
    background: linear-gradient(135deg, #c9302c, #a02622);
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
}

.logout-btn i {
    margin-right: 8px;
    font-size: 1.2rem;
}

/* New Document Button */
.new-document {
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.1)) !important;
    font-weight: 600;
    border-left: 3px solid #fff !important;
    position: relative;
    overflow: hidden;
    border-radius: 6px; /* Added rounded corners */
}

.new-document::after {
    content: 'NEW';
    position: absolute;
    top: 5px;
    right: 5px;
    background: #ff4757;
    color: white;
    font-size: 0.65rem;
    padding: 2px 5px;
    border-radius: 3px;
    font-weight: bold;
}

.new-document:hover {
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.2)) !important;
    transform: translateX(5px);
}

/* Active State for Current Page */
.sidebar .nav-item.active .nav-link {
    color: var(--sidebar-active-color) !important;
    font-weight: 600;
}

.sidebar .nav-item.active .nav-link i {
    color: var(--sidebar-active-color);
}

/* Mobile Sidebar */
@media (max-width: 992px) {
    .sidebar {
        position: fixed;
        left: -300px;
        z-index: 1000;
        height: 100vh;
        box-shadow: var(--shadow-heavy);
    }
    
    .sidebar.active {
        left: 0;
    }
    
    .main-content {
        margin-left: 0;
    }
    
    .sidebar.collapsed + .main-content {
        margin-left: 0;
    }
    
    /* Mobile Toggle Button */
    .navbar-toggler {
        display: block !important;
        border: none;
        padding: 5px 10px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: var(--border-radius);
    }
    
    .navbar-toggler:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    
    .navbar-toggler i {
        color: white;
        font-size: 1.5rem;
    }
    
    /* Adjust logo for mobile */
    .sidebar-brand-wrapper img {
        width: 180px;
        max-height: 50px;
    }
}

/* Animation for sidebar items */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.sidebar .nav-item {
    animation: slideIn 0.5s ease-out forwards;
    opacity: 0;
}

.sidebar .nav-item:nth-child(1) { animation-delay: 0.1s; }
.sidebar .nav-item:nth-child(2) { animation-delay: 0.2s; }
.sidebar .nav-item:nth-child(3) { animation-delay: 0.3s; }
.sidebar .nav-item:nth-child(4) { animation-delay: 0.4s; }
.sidebar .nav-item:nth-child(5) { animation-delay: 0.5s; }
.sidebar .nav-item:nth-child(6) { animation-delay: 0.6s; }
.sidebar .nav-item:nth-child(7) { animation-delay: 0.7s; }
.sidebar .nav-item:nth-child(8) { animation-delay: 0.8s; }

/* Loading animation for logo */
@keyframes logoLoad {
    0% { opacity: 0; transform: scale(0.8); }
    100% { opacity: 1; transform: scale(1); }
}

/* Accessibility improvements */
.sidebar-brand-wrapper .brand-logo:focus {
    outline: 2px solid var(--sidebar-active-color);
    outline-offset: 2px;
}

/* Keyboard navigation support */
.sidebar .nav-link:focus {
    outline: 2px solid var(--sidebar-active-color);
    outline-offset: -2px;
    background: rgba(255, 255, 255, 0.1);
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .sidebar {
        border-right: 2px solid white;
    }
    
    .sidebar .nav-item.active {
        border-left: 6px solid white;
    }
    
    .sidebar-brand-wrapper {
        border-bottom: 3px solid white;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .sidebar-brand-wrapper img,
    .sidebar .nav-item,
    .sidebar-brand-wrapper,
    .logout-btn,
    .sidebar .nav-link {
        animation: none;
        transition: none;
    }
}
</style>

<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <!-- Sidebar Brand Logo -->
    <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
        <a class="sidebar-brand brand-logo" href="<?= $backend ?>" title="Equalization Fund - Return to Dashboard" aria-label="Equalization Fund Logo - Return to Dashboard">
            <img src="<?= $web ?>/igfr_front/img/eq.png" alt="Equalization Fund Logo">
        </a>
    </div>

    <?php if (!Yii::$app->user->isGuest) { ?>
        <ul class="nav">
           <li class="nav-item menu-items <?= Yii::$app->controller->id == 'default' && Yii::$app->controller->action->id == 'dashboard' ? 'active black-active' : '' ?>">
    <a class="nav-link" href="<?= yii\helpers\Url::to(['/ef/default/dashboard']) ?>" aria-label="Dashboard">
        <i class="mdi mdi-speedometer"></i>
       <span class="menu-title" style="background:#000; color:#fff; padding:6px 10px; border-radius:4px;">
    DASHBOARD
</span>

    </a>
</li>

            
            <!-- Policies & Management -->
        <li class="nav-item nav-category" style="background:#000; color:#fff; border-radius:8px;">
    Policies & Management
</li>


            <!-- 1st Marginalization Policy -->
            <li class="nav-item menu-items has-submenu">
                <a class="nav-link <?= in_array(Yii::$app->controller->id, ['allocation', 'ef-project', 'disbursement']) ? '' : 'collapsed' ?>" 
                   data-toggle="collapse" href="#marginalizationPolicy" 
                   aria-expanded="<?= in_array(Yii::$app->controller->id, ['allocation', 'ef-project', 'disbursement']) ? 'true' : 'false' ?>"
                   aria-controls="marginalizationPolicy">
                    <i class="mdi mdi-calendar"></i>
                    <span class="menu-title">1st Marginalization Policy</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse sub-menu <?= in_array(Yii::$app->controller->id, ['allocation', 'ef-project', 'disbursement']) ? 'show' : '' ?>" id="marginalizationPolicy">
                    <ul class="nav flex-column">
                        <li class="nav-item menu-items <?= Yii::$app->controller->id == 'allocation' ? 'active' : '' ?>">
                            <a class="nav-link" href="<?= \yii\helpers\Url::to(['/ef/allocation/index']) ?>" aria-label="EF Entitlement">
                                <i class="mdi mdi-account-cash-outline"></i> EF Entitlement
                            </a>
                        </li>
                        <li class="nav-item menu-items <?= Yii::$app->controller->id == 'ef-project' ? 'active' : '' ?>">
                            <a class="nav-link" href="<?= $efProjectUrl ?>" aria-label="Equalization Projects">
                                <i class="mdi mdi-lightbulb-on-outline"></i>EF Appropriation 2018
                            </a>
                        </li>
                        <li class="nav-item menu-items <?= Yii::$app->controller->id == 'disbursement' && Yii::$app->controller->action->id == 'index' ? 'active' : '' ?>">
                            <a class="nav-link" href="<?= $web ?>/ef/disbursement/index" aria-label="View Disbursements">
                                <i class="mdi mdi-cash-multiple"></i> View Disbursements
                            </a>
                        </li>
                        <li class="nav-item menu-items <?= Yii::$app->controller->id == 'disbursement' && Yii::$app->controller->action->id == 'summaries' ? 'active' : '' ?>">
                            <a class="nav-link" href="<?= $web ?>/ef/disbursement/summaries" aria-label="Disbursement Summaries">
                                <i class="mdi mdi-file-chart-outline"></i> Disburs. Summaries
                            </a>
                        </li>
                        <li class="nav-item menu-items <?= Yii::$app->controller->id == 'disbursement' && Yii::$app->controller->action->id == 'visualization' ? 'active' : '' ?>">
                            <a class="nav-link" href="<?= $web ?>/ef/disbursement/visualization" aria-label="Allocation vs Disbursement">
                                <i class="mdi mdi-chart-line"></i> Allocation vs Disburs.
                            </a>
                        </li>
                        <li class="nav-item menu-items <?= Yii::$app->controller->id == 'ef-project' && Yii::$app->controller->action->id == 'visualization' ? 'active' : '' ?>">
                            <a class="nav-link" href="<?= \yii\helpers\Url::to(['/ef/ef-project/visualization']) ?>" aria-label="Sector vs Project Analysis">
                                <i class="mdi mdi-chart-areaspline"></i> Sector vs Project Analysis
                            </a>
                        </li>
                        <li class="nav-item menu-items <?= Yii::$app->controller->id == 'disbursement' && Yii::$app->controller->action->id == 'sector-disbursements-per-county' ? 'active' : '' ?>">
                            <a class="nav-link" href="<?= $web ?>/ef/disbursement/sector-disbursements-per-county" aria-label="Sector Disbursements">
                                <i class="mdi mdi-chart-bar"></i> Sector Disbursements
                            </a>
                        </li>
                        <li class="nav-item menu-items <?= Yii::$app->controller->id == 'ef-project' && Yii::$app->controller->action->id == 'county-disbursement' ? 'active' : '' ?>">
                            <a class="nav-link" href="<?= \yii\helpers\Url::to(['/ef/ef-project/county-disbursement']) ?>" aria-label="County vs Project Sector">
                                <i class="mdi mdi-map-outline"></i> County vs Project Sector
                            </a>
                        </li>
                      <li class="nav-item menu-items <?= Yii::$app->controller->id == 'ef-project' && Yii::$app->controller->action->id == 'table-summary' ? 'active' : '' ?>">
    <a class="nav-link" href="<?= \yii\helpers\Url::to(['/ef/ef-project/table-summary']) ?>" aria-label="Projects Allocation vs Disbursement">
        <i class="mdi mdi-compare-horizontal"></i> Projects All. Vs Disbur.
    </a>
</li>
<li class="nav-item menu-items <?= Yii::$app->controller->id == 'marginalized-schedule1' && Yii::$app->controller->action->id == 'index' ? 'active' : '' ?>">
    <a class="nav-link" href="<?= \yii\helpers\Url::to(['/ef/marginalized-schedule1/index']) ?>" aria-label="1st Schedule Reports">
        <i class="mdi mdi-file-chart"></i> 1st Schedule Reports
    </a>
</li>
                    </ul>
                </div>
            </li>
            
            <!-- 2nd Marginalization Policy -->
            <li class="nav-item menu-items has-submenu">
                <a class="nav-link collapsed" data-toggle="collapse" href="#secondMarginalizationPolicy" aria-expanded="false" aria-controls="secondMarginalizationPolicy">
                    <i class="mdi mdi-file-document-outline"></i>
                    <span class="menu-title">2nd Marginalization Policy</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse sub-menu" id="secondMarginalizationPolicy">
                    <ul class="nav flex-column">
                        <li class="nav-item menu-items">
                            <a class="nav-link" href="<?= \yii\helpers\Url::to(['/ef/eq-two-appropriation/index']) ?>" aria-label="Appropriation">
                                <i class="mdi mdi-currency-usd"></i> Appropriation
                            </a>
                        </li>
                        <li class="nav-item menu-items">
                            <a class="nav-link" href="<?= \yii\helpers\Url::to(['/ef/eq-two-appropriation/analytics']) ?>" aria-label="Appropriation Analytics">
                                <i class="mdi mdi-chart-box-outline"></i> Appropriation Analytics
                            </a>
                        </li>
                        <li class="nav-item menu-items">
                            <a class="nav-link" href="<?= \yii\helpers\Url::to(['/ef/eq-two-appropriation/insights']) ?>" aria-label="Appropriation Insights">
                                <i class="mdi mdi-lightbulb-on-outline"></i> Appropriation Insights
                            </a>
                        </li>
                        <li class="nav-item menu-items">
                            <a class="nav-link" href="<?= \yii\helpers\Url::to(['/ef/eq-two-projects/index']) ?>" aria-label="Projects">
                                <i class="mdi mdi-briefcase-outline"></i> Projects
                            </a>
                        </li>
    <li class="nav-item menu-items">
    <a class="nav-link" href="<?= \yii\helpers\Url::to(['/ef/eq-two-disbursement/index']) ?>" aria-label="Disbursement">
        <i class="mdi mdi-cash-multiple"></i> Disbursements
    </a>
</li>

<li class="nav-item menu-items">
    <a class="nav-link" href="<?= \yii\helpers\Url::to(['/ef/eq-two-disbursement/analytics']) ?>" aria-label="Disbursement Analytics">
        <i class="mdi mdi-chart-line"></i> Disbursements Analytics
    </a>
</li>


                        <li class="nav-item menu-items">
                            <a class="nav-link" href="<?= \yii\helpers\Url::to(['/ef/eq-two-projects/analytics']) ?>" aria-label="Projects Analytics">
                                <i class="mdi mdi-chart-bar"></i> Projects Analytics
                            </a>
                        </li>
                        <li class="nav-item menu-items">
    <a class="nav-link" href="<?= \yii\helpers\Url::to(['/ef/eq-two-projects/unified-analytics']) ?>" aria-label="Unified Analytics">
        <i class="mdi mdi-chart-arc"></i> Unified Analytics
    </a>
</li>

<li class="nav-item menu-items">
    <a class="nav-link" href="<?= \yii\helpers\Url::to(['/ef/eq-fund-entitlements/index']) ?>" aria-label="EQ Fund Entitlements">
        <i class="mdi mdi-cash-multiple"></i> EQ Fund Entitlements
    </a>
</li>
<li class="nav-item menu-items">
    <a class="nav-link" href="<?= \yii\helpers\Url::to(['/ef/eq-fund-entitlements/analytics']) ?>" aria-label="EQ Fund Entitlements Analytics">
        <i class="mdi mdi-chart-line"></i> EQ Fund Entitlements Analytics
    </a>
</li>


<li class="nav-item menu-items">
    <a class="nav-link" 
       href="<?= \yii\helpers\Url::to(['/ef/reports/index']) ?>" 
       aria-label="2nd Schedule Reports">
        <i class="mdi mdi-file-chart-outline"></i> 2nd Schedule Reports
    </a>
</li>


                    </ul>
                </div>
            </li>

            <!-- Resources Section -->
            <li class="nav-item menu-items has-submenu">
                <a class="nav-link collapsed" data-toggle="collapse" href="#resourcesSection" aria-expanded="false" aria-controls="resourcesSection">
                    <i class="mdi mdi-folder-outline"></i>
                    <span class="menu-title">Resources</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse sub-menu" id="resourcesSection">
                    <ul class="nav flex-column">
                        <li class="nav-item menu-items">
                            <a class="nav-link" href="<?= $web ?>/ef/default/card" aria-label="Documents Library">
                                <i class="mdi mdi-folder-multiple-outline"></i> Documents Library
                            </a>
                        </li>
                        <li class="nav-item menu-items">
                            <a class="nav-link new-document" href="<?= $web ?>/ef/document-library/create" aria-label="Add New Document">
                                <i class="mdi mdi-plus-circle-outline"></i> Add New Document
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

          <!-- ADMIN TASKS (ONLY FOR ADMINS) -->
<?php if (Yii::$app->user->can('admin')): ?>
    <li class="nav-item nav-category">Admin Tasks</li>

    <li class="nav-item menu-items has-submenu">
        <a class="nav-link collapsed" data-toggle="collapse" href="#adminTasks" aria-expanded="false" aria-controls="adminTasks">
            <i class="mdi mdi-cog-outline"></i>
            <span class="menu-title">System Administration</span>
            <i class="menu-arrow"></i>
        </a>

        <div class="collapse sub-menu" id="adminTasks">
            <ul class="nav flex-column">

                <li class="nav-item menu-items">
                    <a class="nav-link" href="<?= $web ?>/backend/user/index" aria-label="User Management">
                        <i class="mdi mdi-account-group-outline"></i> User Management
                    </a>
                </li>

                <li class="nav-item menu-items">
                    <a class="nav-link" href="<?= $web ?>/backend/user-role/index" aria-label="User Roles">
                        <i class="mdi mdi-shield-account-outline"></i> User Roles
                    </a>
                </li>

                <li class="nav-item menu-items">
                    <a class="nav-link" href="<?= $web ?>/backend/external-entity/index" aria-label="External Entities">
                        <i class="mdi mdi-domain"></i> External Entities
                    </a>
                </li>

                <li class="nav-item menu-items">
                    <a class="nav-link" href="<?= $web ?>/backend/document-type/index" aria-label="Document Types">
                        <i class="mdi mdi-file-document-multiple-outline"></i> Document Types
                    </a>
                </li>

                <li class="nav-item menu-items">
                    <a class="nav-link" href="<?= $web ?>/backend/financial-year/index" aria-label="Financial Years">
                        <i class="mdi mdi-calendar-range-outline"></i> Financial Years
                    </a>
                </li>

            </ul>
        </div>
    </li>
<?php endif; ?>


<!-- ✅ This item is visible to everyone -->
<li class="nav-item menu-items">
    <a class="nav-link" href="<?= $web ?>/backend/user/equalization-users" aria-label="Equalization Users">
        <i class="mdi mdi-account-check-outline"></i>
        <span class="menu-title">Equalization Users</span>
    </a>
</li>


            <!-- LOGOUT -->
            <li class="nav-item menu-items logout-item">
                <?= Html::beginForm(['/backend/default/logout'], 'post', ['class' => 'logout-form'])
                    . Html::submitButton(
                        '<i class="mdi mdi-logout"></i> Logout',
                        ['class' => 'nav-link logout-btn', 'aria-label' => 'Logout']
                    )
                    . Html::endForm(); ?>
            </li>
        </ul>
    <?php } ?>
</nav>

<!-- SIDEBAR TOGGLE SCRIPT -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const toggleButton = document.querySelector(".navbar-toggler");
    
    // Toggle sidebar on mobile
    if (toggleButton) {
        toggleButton.addEventListener("click", function () {
            sidebar.classList.toggle("active");
        });
    }
    
    // Handle collapsible menus
    const collapsibleLinks = document.querySelectorAll('.nav-link[data-toggle="collapse"]');
    collapsibleLinks.forEach(link => {
        link.addEventListener("click", function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            const isExpanded = !this.classList.contains('collapsed');
            
            // Close other open menus
            collapsibleLinks.forEach(otherLink => {
                if (otherLink !== this) {
                    const otherTarget = document.querySelector(otherLink.getAttribute('href'));
                    if (otherTarget && otherTarget.classList.contains('show')) {
                        otherTarget.classList.remove('show');
                        otherLink.classList.add('collapsed');
                        // Remove active class from the parent if it's not the current page
                        const parentItem = otherLink.closest('.nav-item');
                        if (parentItem && !parentItem.querySelector('.sub-menu .nav-link.active')) {
                            parentItem.classList.remove('active');
                        }
                    }
                }
            });
            
            // Toggle current menu
            if (isExpanded) {
                target.classList.remove('show');
                this.classList.add('collapsed');
                // Remove active class from the parent if it's not the current page
                const parentItem = this.closest('.nav-item');
                if (parentItem && !parentItem.querySelector('.sub-menu .nav-link.active')) {
                    parentItem.classList.remove('active');
                }
            } else {
                target.classList.add('show');
                this.classList.remove('collapsed');
                // Add active class to the parent if it's not already active
                const parentItem = this.closest('.nav-item');
                if (parentItem && !parentItem.classList.contains('active')) {
                    parentItem.classList.add('active');
                }
            }
        });
    });
    
    // Add active class to current page link
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.closest('.nav-item').classList.add('active');
            // Open parent menu if in submenu
            const parentMenu = link.closest('.sub-menu');
            if (parentMenu) {
                parentMenu.classList.add('show');
                document.querySelector(`[href="#${parentMenu.id}"]`).classList.remove('collapsed');
                // Also add active class to the parent menu item
                const parentItem = parentMenu.closest('.nav-item');
                if (parentItem) {
                    parentItem.classList.add('active');
                }
            }
        }
    });
    
    // Keyboard navigation for collapsible menus
    collapsibleLinks.forEach(link => {
        link.addEventListener("keydown", function(e) {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                this.click();
            }
        });
    });
    
  
    const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
    sidebarLinks.forEach((link, index) => {
        link.addEventListener("keydown", function(e) {
            if (e.key === "ArrowDown") {
                e.preventDefault();
                const nextLink = sidebarLinks[index + 1];
                if (nextLink) nextLink.focus();
            } else if (e.key === "ArrowUp") {
                e.preventDefault();
                const prevLink = sidebarLinks[index - 1];
                if (prevLink) prevLink.focus();
            }
        });
    });
});
</script>