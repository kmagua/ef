<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Json;

$this->title = 'Assign Coordinates - ' . Html::encode($model->project_description);
$this->params['breadcrumbs'][] = ['label' => 'Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Register Leaflet.js - Free, open-source, no API key needed!
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
        --success-color: #28a745;
        --text-dark: #333;
        --text-light: #666;
        --bg-light: #f8f9fa;
        --border-color: #dee2e6;
        --shadow: 0 2px 10px rgba(0,0,0,0.08);
        --shadow-hover: 0 4px 20px rgba(0,0,0,0.12);
    }

    body {
        font-family: 'Poppins', sans-serif !important;
        background: var(--bg-light);
    }

    .coordinates-wrapper {
        background: #fff;
        border-radius: 12px;
        box-shadow: var(--shadow);
        padding: 0;
        margin-bottom: 20px;
        overflow: hidden;
    }

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: #fff;
        padding: 30px;
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
        margin: 0 0 10px 0;
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

    .content-container {
        padding: 25px;
    }

    /* Flash Messages */
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success {
        background: #d4edda;
        border-color: var(--success-color);
        color: #155724;
    }

    .alert-danger {
        background: #f8d7da;
        border-color: #dc3545;
        color: #721c24;
    }

    .alert i {
        font-size: 1.2rem;
    }

    /* Project Info Card */
    .project-info-card {
        background: linear-gradient(135deg, var(--primary-light), #f0f9f9);
        border-left: 5px solid var(--primary-color);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        box-shadow: var(--shadow);
    }

    .project-info-card h3 {
        color: var(--primary-dark);
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0 0 15px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .project-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-item i {
        color: var(--primary-color);
        font-size: 1.1rem;
        width: 20px;
    }

    .info-item strong {
        color: var(--text-dark);
        font-weight: 600;
        margin-right: 5px;
    }

    .info-item span {
        color: var(--text-light);
    }

    /* Map Container */
    .map-container {
        width: 100%;
        height: 550px;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 25px;
        border: 3px solid var(--primary-color);
        box-shadow: var(--shadow);
        position: relative;
        background: #e0e0e0;
    }

    #map {
        width: 100%;
        height: 100%;
        min-height: 550px;
    }

    .map-instructions {
        background: var(--primary-light);
        border-left: 4px solid var(--primary-color);
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .map-instructions i {
        color: var(--primary-color);
        font-size: 1.3rem;
        margin-top: 2px;
    }

    .map-instructions p {
        margin: 0;
        color: var(--text-dark);
        font-size: 0.9rem;
        line-height: 1.6;
    }

    .map-instructions strong {
        color: var(--primary-dark);
    }

    /* Coordinates Form */
    .coordinates-form {
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        border: 2px solid var(--border-color);
        box-shadow: var(--shadow);
    }

    .form-header {
        color: var(--primary-color);
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--primary-light);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-weight: 600;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .form-group label i {
        color: var(--primary-color);
        font-size: 1rem;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0, 138, 138, 0.1);
        outline: none;
        background: #fff;
    }

    .form-control[readonly] {
        background: var(--primary-light);
        cursor: not-allowed;
        font-weight: 600;
        color: var(--primary-dark);
    }

    .coordinate-display {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 2px solid var(--border-color);
        flex-wrap: wrap;
    }

    .btn-modern {
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: var(--shadow);
        font-size: 0.95rem;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: #fff;
    }

    .btn-primary-modern:hover {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        color: #fff;
    }

    .btn-secondary-modern {
        background: #6c757d;
        color: #fff;
    }

    .btn-secondary-modern:hover {
        background: #5a6268;
        color: #fff;
    }

    .form-text {
        color: var(--text-light);
        font-size: 0.85rem;
    }

    #updateMapBtn:hover {
        background: linear-gradient(135deg, #ff4500, #ff6b35) !important;
        transform: translateY(-2px);
    }

    #clearCoordinatesBtn:hover {
        background: #5a6268 !important;
        transform: translateY(-2px);
    }

    /* Leaflet map container */
    .leaflet-container {
        height: 100%;
        width: 100%;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .map-container {
            height: 400px;
        }
        .coordinate-display {
            grid-template-columns: 1fr;
        }
        .form-actions {
            flex-direction: column;
        }
        .btn-modern {
            width: 100%;
            justify-content: center;
        }
        .project-info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="coordinates-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-map-marker-alt"></i> <?= Html::encode($this->title) ?></h1>
        <p>Click on the map or drag the marker to set the project location coordinates</p>
    </div>

    <div class="content-container">
        <!-- Flash Messages -->
        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>
        
        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?= Yii::$app->session->getFlash('error') ?>
            </div>
        <?php endif; ?>
        
        <!-- Project Info Card -->
        <div class="project-info-card">
            <h3><i class="fas fa-info-circle"></i> Project Details</h3>
            <div class="project-info-grid">
                <div class="info-item">
                    <i class="fas fa-building"></i>
                    <div>
                        <strong>County:</strong>
                        <span><?= Html::encode($model->county ?? 'N/A') ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-landmark"></i>
                    <div>
                        <strong>Constituency:</strong>
                        <span><?= Html::encode($model->constituency ?? 'N/A') ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-map"></i>
                    <div>
                        <strong>Ward:</strong>
                        <span><?= Html::encode($model->ward ?? 'N/A') ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-tag"></i>
                    <div>
                        <strong>Sector:</strong>
                        <span><?= Html::encode($model->sector ?? 'N/A') ?></span>
                    </div>
                </div>
            </div>
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(0,138,138,0.2);">
                <div class="info-item">
                    <i class="fas fa-file-alt"></i>
                    <div>
                        <strong>Description:</strong>
                        <span><?= Html::encode($model->project_description ?? 'N/A') ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Map Instructions -->
        <div class="map-instructions">
            <i class="fas fa-lightbulb"></i>
            <p>
                <strong>Instructions:</strong> You can assign coordinates in two ways:
                <br><strong>1. Click on Map:</strong> Click anywhere on the map to place a marker, or drag the existing marker to a new location. The coordinates will automatically update in the form below.
                <br><strong>2. Manual Entry:</strong> Type the latitude and longitude values directly in the input fields below, then click "Update Map Location" to update the marker position on the map.
                <br><br>
                Once satisfied with the location, click "Save Coordinates" to save.
                <br><br>
                <strong>Note:</strong> This page shows only the current project. To view all projects on a map, use the <a href="<?= \yii\helpers\Url::to(['map-view']) ?>" style="color: #ff6b35; font-weight: 700; text-decoration: none; border-bottom: 2px solid #ff6b35; transition: all 0.3s ease;" onmouseover="this.style.color='#ff4500'; this.style.borderBottomColor='#ff4500';" onmouseout="this.style.color='#ff6b35'; this.style.borderBottomColor='#ff6b35';">View Projects on Map</a> button from the projects page.
            </p>
        </div>
        
        <!-- Map Container -->
        <div class="map-container">
            <div id="map">
                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--text-light);">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem;"></i> Loading map...
                </div>
            </div>
        </div>
        
        <!-- Coordinates Form -->
        <div class="coordinates-form">
            <div class="form-header">
                <i class="fas fa-crosshairs"></i> Coordinates
            </div>
            
            <?php $form = ActiveForm::begin([
                'id' => 'coordinates-form',
                'action' => ['save-coordinates'],
                'method' => 'post',
                'options' => ['class' => 'modern-form']
            ]); ?>
            
            <?= Html::hiddenInput('id', $model->id) ?>
            
            <div class="coordinate-display">
                <div class="form-group">
                    <label>
                        <i class="fas fa-globe"></i> Latitude
                    </label>
                    <?= Html::textInput('latitude', $model->latitude ?? '', [
                        'id' => 'latitude',
                        'class' => 'form-control',
                        'type' => 'number',
                        'step' => 'any',
                        'placeholder' => 'Enter latitude (e.g., -1.2921)',
                        'min' => '-90',
                        'max' => '90'
                    ]) ?>
                    <small class="form-text text-muted" style="font-size: 0.85rem; margin-top: 5px; display: block;">
                        Valid range: -90 to 90
                    </small>
                </div>
                
                <div class="form-group">
                    <label>
                        <i class="fas fa-globe"></i> Longitude
                    </label>
                    <?= Html::textInput('longitude', $model->longitude ?? '', [
                        'id' => 'longitude',
                        'class' => 'form-control',
                        'type' => 'number',
                        'step' => 'any',
                        'placeholder' => 'Enter longitude (e.g., 36.8219)',
                        'min' => '-180',
                        'max' => '180'
                    ]) ?>
                    <small class="form-text text-muted" style="font-size: 0.85rem; margin-top: 5px; display: block;">
                        Valid range: -180 to 180
                    </small>
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 15px;">
                <button type="button" id="updateMapBtn" class="btn-modern" style="background: linear-gradient(135deg, #ff6b35, #ff4500); color: #fff; padding: 10px 20px; font-size: 0.9rem;">
                    <i class="fas fa-map-marker-alt"></i> Update Map Location
                </button>
                <button type="button" id="clearCoordinatesBtn" class="btn-modern" style="background: #6c757d; color: #fff; padding: 10px 20px; font-size: 0.9rem; margin-left: 10px;">
                    <i class="fas fa-eraser"></i> Clear
                </button>
            </div>
            
            <div class="form-actions">
                <?= Html::submitButton('<i class="fas fa-save"></i> Save Coordinates', [
                    'class' => 'btn-modern btn-primary-modern'
                ]) ?>
                <?= Html::a('<i class="fas fa-arrow-left"></i> Back to Projects', ['index'], [
                    'class' => 'btn-modern btn-secondary-modern'
                ]) ?>
            </div>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
let map;
let marker;
let currentLat = <?= $model->latitude ?? '-1.2921' ?>;
let currentLng = <?= $model->longitude ?? '36.8219' ?>;

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Wait for Leaflet to load
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
            
            // Default to Kenya center (Nairobi) if no coordinates
            if (!currentLat || !currentLng || currentLat == 0 || currentLng == 0) {
                currentLat = -1.2921; // Nairobi
                currentLng = 36.8219;
            }
            
            // Initialize Leaflet map
            map = L.map('map').setView([parseFloat(currentLat), parseFloat(currentLng)], 10);
    
    // Add OpenStreetMap tiles (free, no API key needed!)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Add existing marker if coordinates exist
    if (<?= $model->latitude ? 'true' : 'false' ?>) {
        marker = L.marker([parseFloat(currentLat), parseFloat(currentLng)], {
            draggable: true,
            title: '<?= Html::encode($model->project_description ?? "Project") ?>'
        }).addTo(map);
        
        // Create detailed popup content for current project
        var currentProjectPopup = `
            <div style="min-width: 220px;">
                <h4 style="margin: 0 0 10px 0; color: #008a8a; font-weight: 600; font-size: 1rem;"><?= Html::encode($model->project_description ?? "Project") ?></h4>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>County:</strong> <?= Html::encode($model->county ?? 'N/A') ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Constituency:</strong> <?= Html::encode($model->constituency ?? 'N/A') ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Ward:</strong> <?= Html::encode($model->ward ?? 'N/A') ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Area:</strong> <?= Html::encode($model->marginalised_area ?? 'N/A') ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Sector:</strong> <?= Html::encode($model->sector ?? 'N/A') ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Budget:</strong> KES <?= number_format($model->project_budget ?? 0, 2) ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem; color: #28a745; font-weight: 600;"><i class="fas fa-info-circle"></i> This is the current project - drag to reposition</p>
            </div>
        `;
        
        marker.bindPopup(currentProjectPopup, {
            closeOnClick: false,
            autoClose: false,
            closeOnEscapeKey: true
        });
        
        // Update form fields when marker is dragged
        marker.on('dragend', function() {
            const position = marker.getLatLng();
            updateCoordinates(position);
        });
        
        // Show popup on hover
        marker.on('mouseover', function(e) {
            this.openPopup();
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
    }
    
    // Note: Other projects are not shown on this page to keep focus on the current project
    // Use "View on Map" button to see all projects together
    
    // Add click listener to map
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        if (!marker) {
            marker = L.marker([lat, lng], {
                draggable: true,
                title: '<?= Html::encode($model->project_description ?? "Project") ?>'
            }).addTo(map);
            
            // Create detailed popup content for current project
            var currentProjectPopup = `
                <div style="min-width: 220px;">
                    <h4 style="margin: 0 0 10px 0; color: #008a8a; font-weight: 600; font-size: 1rem;"><?= Html::encode($model->project_description ?? "Project") ?></h4>
                    <p style="margin: 5px 0; font-size: 0.85rem;"><strong>County:</strong> <?= Html::encode($model->county ?? 'N/A') ?></p>
                    <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Constituency:</strong> <?= Html::encode($model->constituency ?? 'N/A') ?></p>
                    <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Ward:</strong> <?= Html::encode($model->ward ?? 'N/A') ?></p>
                    <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Area:</strong> <?= Html::encode($model->marginalised_area ?? 'N/A') ?></p>
                    <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Sector:</strong> <?= Html::encode($model->sector ?? 'N/A') ?></p>
                    <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Budget:</strong> KES <?= number_format($model->project_budget ?? 0, 2) ?></p>
                    <p style="margin: 5px 0; font-size: 0.85rem; color: #28a745; font-weight: 600;"><i class="fas fa-info-circle"></i> This is the current project - drag to reposition</p>
                </div>
            `;
            
            marker.bindPopup(currentProjectPopup, {
                closeOnClick: false,
                autoClose: false,
                closeOnEscapeKey: true
            });
            
            marker.on('dragend', function() {
                const position = marker.getLatLng();
                updateCoordinates(position);
            });
            
            // Show popup on hover
            marker.on('mouseover', function(e) {
                this.openPopup();
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
        } else {
            marker.setLatLng([lat, lng]);
        }
        
        updateCoordinates({lat: lat, lng: lng});
        });
        } catch (error) {
            console.error('Error initializing map:', error);
            document.getElementById('map').innerHTML = '<div style="padding: 20px; text-align: center; color: #dc3545;"><i class="fas fa-exclamation-triangle"></i> Error loading map: ' + error.message + '</div>';
        }
    }, 500); // Wait 500ms for Leaflet to load
});

function updateCoordinates(position) {
    document.getElementById('latitude').value = position.lat.toFixed(6);
    document.getElementById('longitude').value = position.lng.toFixed(6);
}

// Function to update map marker from manual coordinate input
function updateMapFromInput() {
    // Check if map is initialized
    if (typeof map === 'undefined' || !map) {
        alert('Map is still loading. Please wait a moment and try again.');
        return false;
    }
    
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const lat = parseFloat(latInput.value);
    const lng = parseFloat(lngInput.value);
    
    // Validate coordinates
    if (isNaN(lat) || isNaN(lng)) {
        alert('Please enter valid numeric values for both latitude and longitude.');
        return false;
    }
    
    if (lat < -90 || lat > 90) {
        alert('Latitude must be between -90 and 90 degrees.');
        latInput.focus();
        return false;
    }
    
    if (lng < -180 || lng > 180) {
        alert('Longitude must be between -180 and 180 degrees.');
        lngInput.focus();
        return false;
    }
    
    // Update marker position
    if (marker) {
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], map.getZoom());
        
        // Update popup with new coordinates
        const currentProjectPopup = `
            <div style="min-width: 220px;">
                <h4 style="margin: 0 0 10px 0; color: #008a8a; font-weight: 600; font-size: 1rem;"><?= Html::encode($model->project_description ?? "Project") ?></h4>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>County:</strong> <?= Html::encode($model->county ?? 'N/A') ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Constituency:</strong> <?= Html::encode($model->constituency ?? 'N/A') ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Ward:</strong> <?= Html::encode($model->ward ?? 'N/A') ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Area:</strong> <?= Html::encode($model->marginalised_area ?? 'N/A') ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Sector:</strong> <?= Html::encode($model->sector ?? 'N/A') ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Budget:</strong> KES <?= number_format($model->project_budget ?? 0, 2) ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem; color: #28a745; font-weight: 600;"><i class="fas fa-info-circle"></i> Coordinates updated manually</p>
            </div>
        `;
        marker.setPopupContent(currentProjectPopup);
        marker.openPopup();
    } else {
        // Create new marker if it doesn't exist
        marker = L.marker([lat, lng], {
            draggable: true,
            title: '<?= Html::encode($model->project_description ?? "Project") ?>'
        }).addTo(map);
        
        const currentProjectPopup = `
            <div style="min-width: 220px;">
                <h4 style="margin: 0 0 10px 0; color: #008a8a; font-weight: 600; font-size: 1rem;"><?= Html::encode($model->project_description ?? "Project") ?></h4>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>County:</strong> <?= Html::encode($model->county ?? 'N/A') ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Constituency:</strong> <?= Html::encode($model->constituency ?? 'N/A') ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Ward:</strong> <?= Html::encode($model->ward ?? 'N/A') ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Area:</strong> <?= Html::encode($model->marginalised_area ?? 'N/A') ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Sector:</strong> <?= Html::encode($model->sector ?? 'N/A') ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem;"><strong>Budget:</strong> KES <?= number_format($model->project_budget ?? 0, 2) ?></p>
                <p style="margin: 5px 0; font-size: 0.85rem; color: #28a745; font-weight: 600;"><i class="fas fa-info-circle"></i> This is the current project - drag to reposition</p>
            </div>
        `;
        
        marker.bindPopup(currentProjectPopup, {
            closeOnClick: false,
            autoClose: false,
            closeOnEscapeKey: true
        });
        
        marker.on('dragend', function() {
            const position = marker.getLatLng();
            updateCoordinates(position);
        });
        
        // Show popup on hover
        marker.on('mouseover', function(e) {
            this.openPopup();
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
        
        map.setView([lat, lng], 12);
    }
    
    return true;
}

// Function to clear coordinates
function clearCoordinates() {
    if (confirm('Are you sure you want to clear the coordinates?')) {
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
        if (marker && typeof map !== 'undefined' && map) {
            map.removeLayer(marker);
            marker = null;
        }
    }
}

// Add event listeners for manual coordinate input
document.addEventListener('DOMContentLoaded', function() {
    // Wait for the map to be initialized
    setTimeout(function() {
        const updateMapBtn = document.getElementById('updateMapBtn');
        const clearBtn = document.getElementById('clearCoordinatesBtn');
        
        if (updateMapBtn) {
            updateMapBtn.addEventListener('click', function() {
                updateMapFromInput();
            });
        }
        
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                clearCoordinates();
            });
        }
        
        // Allow Enter key to update map
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        
        if (latInput) {
            latInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    updateMapFromInput();
                }
            });
        }
        
        if (lngInput) {
            lngInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    updateMapFromInput();
                }
            });
        }
    }, 1000); // Wait 1 second for map initialization
});
</script>

