<?php
$web = Yii::getAlias('@web');
$backend = yii\helpers\Url::to(['/backend/default']);
$eq = yii\helpers\Url::to(['/ef/default']);
?>

<style>
    
    :root {
  --sidebar-width: 300px;
  --sidebar-background: #8B4513;
  --sidebar-text-color: #FFFF; 
  --sidebar-hover-background: #8B4513;
  --sidebar-active-color: #FFD700; 
  --scrollbar-thumb-color: #FFD700;
  --scrollbar-track-color: #FFF8DC; 
}

.sidebar {
  background: var(--sidebar-background);
  color: var(--sidebar-text-color);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
}


.sidebar .nav-link,
.sidebar .nav-item {
  transition: all .3s ease;
}

.sidebar .nav-item:hover,
.sidebar .nav-item.active {
  background: var(--sidebar-hover-background);
  border-left: 4px solid var(--sidebar-active-color);
}


.sidebar .nav-link {
  color: var(--sidebar-text-color) !important; 
  font-size: .9rem;
  text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.1); /* Subtle text shadow for better legibility */
}

.sidebar .nav-item:hover .nav-link i {
  transform: scale(1.1);
}

.sidebar .menu-title {
  font-weight: 800;
}


.sidebar::-webkit-scrollbar {
  width: 5px;
}

.sidebar::-webkit-scrollbar-thumb {
  background: var(--scrollbar-thumb-color);
  border-radius: 7px;
}

.sidebar::-webkit-scrollbar-track {
  background: var(--scrollbar-track-color);
}

/* Responsive design using Bootstrap's media query mixins */
@media (max-width: 992px) {
  .sidebar {
    position: fixed;
    left: calc(-1 * var(--sidebar-width));
    width: var(--sidebar-width);
    transition: left 0.3s ease;
  }

  .sidebar.active {
    left: 0;
  }

  /* Adjusting margins using Bootstrap's margin utility classes */
  .sidebar-offcanvas.active ~ .navbar {
    margin-left: var(--sidebar-width);
  }
}

/* Enhancements for profile images and responsiveness */
.sidebar .nav-item.profile img {
  box-shadow: 0 0 8px rgba(255, 215, 0, 0.6); /* Glowing effect for profile image */
}

@media (max-width: 576px) {
  .sidebar .nav-link,
  .sidebar .menu-title {
    font-size: .8rem;
  }
}
    </style>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
        <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
          <a class="sidebar-brand brand-logo" href="<?= $backend ?>"><img src="<?= $web ?>/igfr_template/assets/images/tnt.png" alt="logo" width="80%"/></a>
        </div>
        <?php if(!Yii::$app->user->isGuest){ ?>
        <ul class="nav">
          <li class="nav-item menu-items">
            <a class="nav-link" href="<?= $backend ?>">
              <span class="menu-icon">
                <i class="mdi mdi-speedometer"></i>
              </span>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          
          <li class="nav-item menu-items" id="doc_library_menu">
            <a class="nav-link" data-toggle="collapse" href="#library-docs" aria-expanded="false" aria-controls="ui-basic">
              <span class="menu-icon">
                <i class="mdi mdi-laptop"></i>
              </span>
              <span class="menu-title">Document Library</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="library-docs">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="<?=$web ?>/backend/document-library/card">IGFR Documents</a></li>                
                <li class="nav-item"> <a class="nav-link" href="<?=$web ?>/backend/letter/index">Correspondences</a></li>
              </ul>
            </div>
          </li>
          
          <li class="nav-item menu-items" id="aigfr_data_menu">
            <a class="nav-link" data-toggle="collapse" href="#igfr-data" aria-expanded="true" aria-controls="ui-basic">
              <span class="menu-icon">
                <i class="mdi mdi-laptop"></i>
              </span>
              <span class="menu-title">IGFR Data</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="igfr-data">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="<?=$web ?>/backend/equitable-revenue/index" title="Equitable Revenue Share">Equitable Revenue</a></li>
                <li class="nav-item"> <a class="nav-link" href="<?=$web ?>/backend/additional-revenue/index" title="Additional Revenue Share">Additional Revenue</a></li>
                <li class="nav-item"> <a class="nav-link" href="<?=$web ?>/backend/projects/index" title="Projects List">Projects</a></li>
                <li class="nav-item"> <a class="nav-link" href="<?=$web ?>/backend/financier/index" title="Financiers">Financiers</a></li>
                <li class="nav-item"> <a class="nav-link" href="<?=$web ?>/backend/fiscal/index" title="Fiscal">Fiscal Responsibility</a></li>
                <li class="nav-item"> <a class="nav-link" href="<?=$web ?>/backend/obligation/index">Obligations</a></li>
                <li class="nav-item"> <a class="nav-link" href="<?=$web ?>/backend/default/charts">Data Visualization </a></li>
                <li class="nav-item"> <a class="nav-link" href="<?=$web ?>/backend/county/index">Counties</a></li>
              </ul>
            </div>
          </li>
          
          <li class="nav-item menu-items">
            <a class="nav-link" href="<?= $eq ?>">
              <span class="menu-icon">
                <i class="mdi mdi-speedometer"></i>
              </span>
              <span class="menu-title">Equalization Fund</span>
            </a>
          </li>
          
          <li class="nav-item menu-items" id="administrative_menu">
            <a class="nav-link" data-toggle="collapse" href="#admin-tasks" aria-expanded="false" aria-controls="ui-basic">
              <span class="menu-icon">
                <i class="mdi mdi-laptop"></i>
              </span>
              <span class="menu-title">Admin Tasks</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="admin-tasks">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="<?=$web ?>/backend/user/index">User Management</a></li>
                <li class="nav-item"> <a class="nav-link" href="<?=$web ?>/backend/user-role/index">User Roles</a></li>
                <li class="nav-item"> <a class="nav-link" href="<?=$web ?>/backend/external-entity/index">External Entities</a></li>
                <li class="nav-item"> <a class="nav-link" href="<?=$web ?>/backend/document-type/index">Document Types</a></li>
                <li class="nav-item"> <a class="nav-link" href="<?=$web ?>/backend/financial-year/index">Financial Years</a></li>
              </ul>
            </div>
          </li>          
        </ul>
        <?php } ?>
      </nav>
