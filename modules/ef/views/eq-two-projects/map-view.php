<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Json;

$this->title = 'Projects Map View';
$this->params['breadcrumbs'][] = ['label' => 'Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Register Leaflet.js
$this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
$this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
?>

<style>
    :root {
        --primary-color: #008a8a;
        --primary-dark: #006666;
        --primary-light: #e0f7fa;
        --text-dark: #333;
        --text-light: #666;
        --bg-light: #f8f9fa;
        --border-color: #dee2e6;
        --shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    body {
        font-family: 'Poppins', sans-serif !important;
        background: var(--bg-light);
    }

    .map-view-wrapper {
        background: #fff;
        border-radius: 12px;
        box-shadow: var(--shadow);
        padding: 20px;
        margin-bottom: 20px;
    }

    .page-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: #fff;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .page-header h1 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0 0 8px 0;
        position: relative;
        z-index: 1;
    }

    .page-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 0.95rem;
        position: relative;
        z-index: 1;
    }

    /* Search Form */
    .search-form-container {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: var(--shadow);
        margin-bottom: 20px;
        border-left: 4px solid var(--primary-color);
    }

    .search-form-container h3 {
        color: var(--primary-color);
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .search-form-container .form-group {
        margin-bottom: 15px;
    }

    .search-form-container label {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .search-form-container .form-control,
    .search-form-container .select2-container {
        border: 2px solid var(--border-color);
        border-radius: 8px;
    }

    .search-form-container .form-control:focus,
    .search-form-container .select2-container--focus .select2-selection {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0, 138, 138, 0.1);
    }

    .search-buttons {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .btn-search {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    .btn-reset {
        background: #6c757d;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-reset:hover {
        background: #5a6268;
    }

    /* Map Container */
    .map-container {
        width: 100%;
        height: 600px;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 20px;
        border: 3px solid var(--primary-color);
        box-shadow: var(--shadow);
        position: relative;
        background: #e0e0e0;
    }

    #map {
        width: 100%;
        height: 100%;
        min-height: 600px;
    }
    
    /* Loading indicator */
    .map-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--text-light);
        font-size: 1.1rem;
    }

    /* Stats Bar */
    .stats-bar {
        background: var(--primary-light);
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
        gap: 15px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-item .stat-label {
        font-size: 0.85rem;
        color: var(--text-light);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .stat-item .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    /* Project Info Panel */
    .project-info-panel {
        background: #fff;
        border-radius: 12px;
        padding: 0;
        box-shadow: var(--shadow);
        max-height: 600px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    .project-info-panel-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: #fff;
        padding: 20px;
        border-radius: 12px 12px 0 0;
        position: sticky;
        top: 0;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .project-info-panel-header h3 {
        color: #fff;
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .project-info-panel-header .project-count {
        background: rgba(255,255,255,0.2);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-left: auto;
    }

    .projects-list-container {
        padding: 15px;
        flex: 1;
        overflow-y: auto;
    }

    .project-item {
        background: #fff;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .project-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--primary-color);
        transition: width 0.3s ease;
    }

    .project-item:hover {
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 138, 138, 0.15);
    }

    .project-item:hover::before {
        width: 6px;
    }

    .project-item.active {
        background: linear-gradient(135deg, var(--primary-light), #f0f9f9);
        border-color: var(--primary-color);
        box-shadow: 0 4px 15px rgba(0, 138, 138, 0.25);
    }

    .project-item.active::before {
        width: 6px;
        background: var(--primary-dark);
    }

    .project-item-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .project-item-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        line-height: 1.4;
        flex: 1;
        padding-right: 10px;
    }

    .project-item.active .project-item-title {
        color: var(--primary-dark);
    }

    .project-item-icon {
        background: var(--primary-light);
        color: var(--primary-color);
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .project-item.active .project-item-icon {
        background: var(--primary-color);
        color: #fff;
        transform: scale(1.1);
    }

    .project-item-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-top: 10px;
    }

    .project-detail-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        color: var(--text-light);
    }

    .project-item.active .project-detail-item {
        color: var(--text-dark);
    }

    .project-detail-item i {
        color: var(--primary-color);
        font-size: 0.75rem;
        width: 16px;
    }

    .project-item.active .project-detail-item i {
        color: var(--primary-dark);
    }

    .project-budget {
        grid-column: 1 / -1;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .project-item.active .project-budget {
        border-top-color: var(--primary-color);
    }

    .budget-label {
        font-size: 0.75rem;
        color: var(--text-light);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .project-item.active .budget-label {
        color: var(--text-dark);
    }

    .budget-value {
        font-size: 1rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .project-item.active .budget-value {
        color: var(--primary-dark);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-light);
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--primary-light);
        margin-bottom: 15px;
    }

    .empty-state p {
        margin: 0;
        font-size: 0.95rem;
    }

    /* Custom Scrollbar */
    .projects-list-container::-webkit-scrollbar {
        width: 6px;
    }

    .projects-list-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .projects-list-container::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 10px;
    }

    .projects-list-container::-webkit-scrollbar-thumb:hover {
        background: var(--primary-dark);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .map-container {
            height: 400px;
        }
        .stats-bar {
            flex-direction: column;
        }
        .search-buttons {
            flex-direction: column;
        }
        .btn-search, .btn-reset {
            width: 100%;
        }
    }
</style>

<div class="map-view-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-map-marked-alt"></i> <?= Html::encode($this->title) ?></h1>
        <p>View all projects with coordinates on an interactive map. Use the search filters to find specific projects.</p>
    </div>

    <!-- Stats Bar -->
    <div class="stats-bar">
        <div class="stat-item">
            <div class="stat-label">Total Projects</div>
            <div class="stat-value"><?= count($projects) ?></div>
        </div>
        <div class="stat-item">
            <div class="stat-label">With Coordinates</div>
            <div class="stat-value"><?= count($projects) ?></div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Total Budget</div>
            <div class="stat-value">KES <?= number_format(array_sum(array_map(function($p) { return $p->project_budget ?: 0; }, $projects)), 2) ?></div>
        </div>
    </div>

    <!-- Search Form -->
    <div class="search-form-container">
        <h3><i class="fas fa-search"></i> Search & Filter Projects</h3>
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => ['map-view'],
            'options' => ['class' => 'search-form']
        ]); ?>
        
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($searchModel, 'project_description')->textInput([
                    'placeholder' => 'Project name...',
                    'class' => 'form-control'
                ])->label('Project Name') ?>
            </div>
            
            <div class="col-md-3">
                <?= $form->field($searchModel, 'county')->widget(Select2::classname(), [
                    'data' => $counties,
                    'options' => ['placeholder' => 'Select county...'],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('County') ?>
            </div>
            
            <div class="col-md-3">
                <?= $form->field($searchModel, 'constituency')->widget(Select2::classname(), [
                    'data' => $constituencies,
                    'options' => ['placeholder' => 'Select constituency...'],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Constituency') ?>
            </div>
            
            <div class="col-md-3">
                <?= $form->field($searchModel, 'ward')->widget(Select2::classname(), [
                    'data' => $wards,
                    'options' => ['placeholder' => 'Select ward...'],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Ward') ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($searchModel, 'marginalised_area')->widget(Select2::classname(), [
                    'data' => $marginalisedAreas,
                    'options' => ['placeholder' => 'Select marginalised area...'],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Marginalised Area') ?>
            </div>
            
            <div class="col-md-4">
                <?= $form->field($searchModel, 'sector')->widget(Select2::classname(), [
                    'data' => $sectors,
                    'options' => ['placeholder' => 'Select sector...'],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Sector') ?>
            </div>
        </div>
        
        <div class="search-buttons">
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i> Search
            </button>
            <?= Html::a('<i class="fas fa-redo"></i> Reset', ['map-view'], ['class' => 'btn-reset']) ?>
        </div>
        
        <?php ActiveForm::end(); ?>
    </div>

    <!-- Map and Project List -->
    <div class="row">
        <div class="col-md-8">
            <div class="map-container">
                <div id="map">
                    <div class="map-loading">
                        <i class="fas fa-spinner fa-spin"></i> Loading map...
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="project-info-panel">
                <div class="project-info-panel-header">
                    <h3>
                        <i class="fas fa-map-pin"></i> Projects on Map
                        <span class="project-count"><?= count($projects) ?> Projects</span>
                    </h3>
                </div>
                <div class="projects-list-container" id="projects-list">
                    <?php if (empty($projects)): ?>
                        <div class="empty-state">
                            <i class="fas fa-map-marked-alt"></i>
                            <p>No projects with coordinates found.<br>Please add coordinates to projects first.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($projects as $index => $project): ?>
                            <div class="project-item" data-project-id="<?= $project->id ?>" data-lat="<?= $project->latitude ?>" data-lng="<?= $project->longitude ?>">
                                <div class="project-item-header">
                                    <h4 class="project-item-title">
                                        <?= Html::encode($project->project_description ?: 'Unnamed Project') ?>
                                    </h4>
                                    <div class="project-item-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                </div>
                                <div class="project-item-details">
                                    <div class="project-detail-item">
                                        <i class="fas fa-building"></i>
                                        <span><?= Html::encode($project->county ?: 'N/A') ?></span>
                                    </div>
                                    <div class="project-detail-item">
                                        <i class="fas fa-landmark"></i>
                                        <span><?= Html::encode($project->constituency ?: 'N/A') ?></span>
                                    </div>
                                    <div class="project-detail-item">
                                        <i class="fas fa-map"></i>
                                        <span><?= Html::encode($project->ward ?: 'N/A') ?></span>
                                    </div>
                                    <div class="project-detail-item">
                                        <i class="fas fa-tag"></i>
                                        <span><?= Html::encode($project->sector ?: 'N/A') ?></span>
                                    </div>
                                    <div class="project-budget">
                                        <span class="budget-label">Budget</span>
                                        <span class="budget-value">KES <?= number_format($project->project_budget ?: 0, 2) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Wait for DOM and Leaflet to be ready
    document.addEventListener('DOMContentLoaded', function() {
        // Wait a bit more to ensure Leaflet is loaded
        setTimeout(function() {
            if (typeof L === 'undefined') {
                console.error('Leaflet library not loaded!');
                document.getElementById('map').innerHTML = '<div style="padding: 20px; text-align: center; color: #dc3545;"><i class="fas fa-exclamation-triangle"></i> Map library failed to load. Please refresh the page.</div>';
                return;
            }
            
            try {
                // Remove loading indicator
                var mapDiv = document.getElementById('map');
                mapDiv.innerHTML = '';
                
                // Initialize map
                var map = L.map('map').setView([-0.0236, 37.9062], 6); // Center on Kenya
                
                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);
                
                // Projects data from PHP
                var projectsData = <?= Json::encode($projectsData) ?>;
                
                // Store markers for later reference
                var markers = [];
                var currentMarker = null;
                
                // Create markers for each project
                if (projectsData && projectsData.length > 0) {
                    projectsData.forEach(function(project, index) {
                        if (project.latitude && project.longitude) {
                            var marker = L.marker([project.latitude, project.longitude]).addTo(map);
                            
                            // Create popup content
                            var popupContent = `
                                <div style="min-width: 200px;">
                                    <h4 style="margin: 0 0 10px 0; color: #008a8a; font-weight: 600;">${project.name}</h4>
                                    <p style="margin: 5px 0;"><strong>County:</strong> ${project.county}</p>
                                    <p style="margin: 5px 0;"><strong>Constituency:</strong> ${project.constituency}</p>
                                    <p style="margin: 5px 0;"><strong>Ward:</strong> ${project.ward}</p>
                                    <p style="margin: 5px 0;"><strong>Area:</strong> ${project.marginalised_area}</p>
                                    <p style="margin: 5px 0;"><strong>Sector:</strong> ${project.sector}</p>
                                    <p style="margin: 5px 0;"><strong>Budget:</strong> KES ${project.budget}</p>
                                    <a href="/ef/eq-two-projects/view?id=${project.id}" style="display: inline-block; margin-top: 10px; padding: 5px 10px; background: #008a8a; color: white; text-decoration: none; border-radius: 4px;">View Details</a>
                                </div>
                            `;
                            
                            marker.bindPopup(popupContent, {
                                closeOnClick: false,
                                autoClose: false,
                                closeOnEscapeKey: true
                            });
                            marker.projectId = project.id;
                            
                            // Show popup on hover
                            marker.on('mouseover', function(e) {
                                this.openPopup();
                                highlightProjectInList(project.id);
                            });
                            
                            // Keep popup open when hovering over it
                            marker.on('popupopen', function(e) {
                                var popup = e.popup;
                                popup.getElement().addEventListener('mouseenter', function() {
                                    clearTimeout(marker._closeTimeout);
                                });
                                popup.getElement().addEventListener('mouseleave', function() {
                                    marker._closeTimeout = setTimeout(function() {
                                        marker.closePopup();
                                    }, 200);
                                });
                            });
                            
                            // Close popup when mouse leaves marker
                            marker.on('mouseout', function(e) {
                                var self = this;
                                marker._closeTimeout = setTimeout(function() {
                                    self.closePopup();
                                }, 200);
                            });
                            
                            // Add click event to highlight in list and keep popup open
                            marker.on('click', function() {
                                highlightProjectInList(project.id);
                                this.openPopup();
                            });
                            
                            markers.push(marker);
                        }
                    });
                    
                    // Fit map to show all markers
                    if (markers.length > 0) {
                        var group = new L.featureGroup(markers);
                        map.fitBounds(group.getBounds().pad(0.1));
                    } else {
                        console.warn('No valid markers to display');
                    }
                } else {
                    console.warn('No projects data available');
                }
                
                // Highlight project in list when marker is clicked
                function highlightProjectInList(projectId) {
                    // Remove active class from all items
                    document.querySelectorAll('.project-item').forEach(function(item) {
                        item.classList.remove('active');
                    });
                    
                    // Add active class to clicked item
                    var projectItem = document.querySelector('.project-item[data-project-id="' + projectId + '"]');
                    if (projectItem) {
                        projectItem.classList.add('active');
                        projectItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
                
                // Click on project item to focus map on that marker
                document.querySelectorAll('.project-item').forEach(function(item) {
                    item.addEventListener('click', function() {
                        var lat = parseFloat(this.getAttribute('data-lat'));
                        var lng = parseFloat(this.getAttribute('data-lng'));
                        var projectId = this.getAttribute('data-project-id');
                        
                        // Find and open marker popup
                        markers.forEach(function(marker) {
                            if (marker.projectId == projectId) {
                                map.setView([lat, lng], 12);
                                marker.openPopup();
                                highlightProjectInList(projectId);
                            }
                        });
                    });
                });
            } catch (error) {
                console.error('Error initializing map:', error);
                document.getElementById('map').innerHTML = '<div style="padding: 20px; text-align: center; color: #dc3545;"><i class="fas fa-exclamation-triangle"></i> Error loading map: ' + error.message + '</div>';
            }
        }, 500); // Wait 500ms for Leaflet to load
    });
</script>

