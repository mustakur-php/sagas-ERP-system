@extends('layouts.app')

@section('content')
<style>
.map-container {
    width: 960px;
    height: 860px;
    position: fixed;
    left: 30px;
    top: 260px;
    z-index: 10;
}

#map {
    width: 100%;
    height: 100%;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

.work-location-layout {
    margin-left: 980px;
}

.work-location-content {
    width: 100%;
}

.work-location-form {
    display: grid;
    grid-template-columns: 1.4fr 1fr 1fr .8fr .7fr auto;
    gap: 12px;
    align-items: end;
}

.work-location-form .form-group {
    margin-bottom: 0;
}

.work-location-form input,
.work-location-form select {
    height: 44px;
}

@media (max-width: 1200px) {
    .work-location-layout {
        margin-left: 0;
    }

    .work-location-form {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    
}
/* تحسين صندوق اختيار طبقات الخريطة */
.leaflet-control-layers {
    border: none !important;
    border-radius: 14px !important;
    box-shadow: 0 10px 25px rgba(0,0,0,.18) !important;
    overflow: hidden;
    background: #ffffff !important;
    font-family: Tahoma, Arial, sans-serif;
}

.leaflet-control-layers-toggle {
    width: 42px !important;
    height: 42px !important;
    background-size: 22px 22px !important;
    border-radius: 14px !important;
    background-color: #ffffff !important;
}

.leaflet-control-layers-expanded {
    padding: 12px 14px !important;
    min-width: 150px;
}

.leaflet-control-layers-list {
    margin: 0 !important;
}

.leaflet-control-layers-base label {
    display: flex !important;
    align-items: center;
    gap: 8px;
    padding: 8px 6px;
    margin: 0;
    cursor: pointer;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    color: #1f2937;
}

.leaflet-control-layers-base label:hover {
    background: #f3f4f6;
}

.leaflet-control-layers-base input {
    width: auto !important;
    margin: 0 !important;
    accent-color: #2563eb;
}

.leaflet-control-custom {
    border-radius: 14px;
    box-shadow: 0 6px 15px rgba(0,0,0,.15);
}
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<div class="page">

    <div class="page-actions">
        <div>
            <h2 class="fw-bold mb-1">مواقع العمل الجغرافية</h2>
            <p class="text-muted">تحديد مواقع ونطاقات السماح لتسجيل الحضور والانصراف</p>
        </div>
    </div>

    <div class="map-container">
        <div id="map"></div>
    </div>
    <div id="locationResult" style="
        margin-top:10px;
        font-weight:bold;
        font-size:14px;
    "></div>
    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-success" style="background:#fee2e2;color:#991b1b;border-color:#fecaca;">
            <ul style="margin:0;padding-right:20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="work-location-layout">
        <div class="work-location-content">
            <div class="card">
                <h4 style="margin-top:0;">إضافة موقع عمل</h4>

                <form method="POST" action="{{ route('hr.work-locations.store') }}" class="work-location-form">
                    @csrf

                    <div class="form-group">
                        <label>اسم الموقع</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="مثال: محطة التحلية" required>
                    </div>

                    <div class="form-group">
                        <label>Latitude</label>
                        <input type="text" id="latitude" name="latitude" value="{{ old('latitude') }}" readonly required>
                    </div>

                    <div class="form-group">
                        <label>Longitude</label>
                        <input type="text" id="longitude" name="longitude" value="{{ old('longitude') }}" readonly required>
                    </div>

                    <div class="form-group">
                        <label>النطاق بالمتر</label>
                        <input type="number" id="radius_meters" name="radius_meters" value="{{ old('radius_meters', 100) }}" min="5" max="5000" required>
                    </div>

                    <div class="form-group">
                        <label>الحالة</label>
                        <label style="display:flex;align-items:center;gap:8px;height:44px;">
                            <input type="checkbox" name="is_active" value="1" checked style="width:auto;">
                            نشط
                        </label>
                    </div>

                    <div class="form-actions" style="margin-top:24px;">
                        <button type="submit" class="btn btn-primary">حفظ الموقع</button>
                    </div>
                </form>
            </div>
            
            <div class="card">
                <h4 style="margin-top:0;">المواقع المسجلة</h4>

                @if($locations->count())
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>الموقع</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>النطاق</th>
                                    <th>الحالة</th>
                                    <th>إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($locations as $location)
                                    <tr>
                                        <td>{{ $location->name }}</td>
                                        <td>{{ $location->latitude }}</td>
                                        <td>{{ $location->longitude }}</td>
                                        <td>{{ $location->radius_meters }} متر</td>
                                        <td>
                                            @if($location->is_active)
                                                <span class="status-badge status-approved">نشط</span>
                                            @else
                                                <span class="status-badge status-rejected">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form method="POST"
                                                action="{{ route('hr.work-locations.destroy', $location->id) }}"
                                                onsubmit="return confirm('هل أنت متأكد من حذف الموقع؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger mini-btn">حذف</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="muted">لا توجد مواقع عمل حتى الآن.</p>
                @endif
            </div>
        </div>
    </div>

</div>
<script>
function getDistance(lat1, lon1, lat2, lon2) {
    var R = 6371000; // متر
    var dLat = (lat2 - lat1) * Math.PI / 180;
    var dLon = (lon2 - lon1) * Math.PI / 180;

    var a =
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon/2) * Math.sin(dLon/2);

    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    return R * c;
}
    var map = L.map('map', {
    maxZoom: 22
}).setView([21.543333, 39.172777], 18);

// الخريطة العادية
var streetMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap',
    maxNativeZoom: 19,
    maxZoom: 22
});

// خريطة الستالايت
var satelliteMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: 'Tiles © Esri',
    maxNativeZoom: 19,
    maxZoom: 22
});

// الطبقة الافتراضية
satelliteMap.addTo(map);

// زر اختيار الطبقات
var baseMaps = {
    "صور فضائية": satelliteMap,
    "خريطة الشوارع": streetMap
};

// زر موقعي الحالي
var locateControl = L.control({ position: 'topright' });

locateControl.onAdd = function(map) {
    var div = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');

    div.innerHTML = '📍';
    div.style.backgroundColor = '#fff';
    div.style.width = '42px';
    div.style.height = '42px';
    div.style.display = 'flex';
    div.style.alignItems = 'center';
    div.style.justifyContent = 'center';
    div.style.fontSize = '18px';
    div.style.cursor = 'pointer';

    div.onclick = function() {
        if (!navigator.geolocation) {
            alert('المتصفح لا يدعم تحديد الموقع');
            return;
        }

        navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;

            map.setView([lat, lng], 19);

            // تحديث الحقول
            document.getElementById('latitude').value = lat.toFixed(7);
            document.getElementById('longitude').value = lng.toFixed(7);

            // حذف القديم
            if (marker) map.removeLayer(marker);
            if (circle) map.removeLayer(circle);

            // رسم جديد
            marker = L.marker([lat, lng]).addTo(map);

            var radius = parseInt(document.getElementById('radius_meters').value || 100);

            circle = L.circle([lat, lng], {
                radius: radius,
                color: '#16a34a',
                fillColor: '#16a34a',
                fillOpacity: 0.2
            }).addTo(map);

        }, function() {
            alert('تعذر تحديد الموقع');
        }, {
            enableHighAccuracy: true
        });
    };

    return div;
};

locateControl.addTo(map);

L.control.layers(baseMaps).addTo(map);

    var marker;
    var circle;

    map.on('click', function(e) {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;

        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        var radius = parseInt(document.getElementById('radius_meters').value || 100);

        // حذف القديم
        if (marker) {
            map.removeLayer(marker);
        }
        if (circle) {
            map.removeLayer(circle);
        }

        // إضافة ماركر
        marker = L.marker([lat, lng]).addTo(map);

        // إضافة دائرة
        circle = L.circle([lat, lng], {
            radius: radius,
            color: '#2563eb',
            fillColor: '#2563eb',
            fillOpacity: 0.15
        }).addTo(map);
    });

    // تحديث الدائرة عند تغيير النطاق
    document.getElementById('radius_meters').addEventListener('input', function () {
        if (!marker) return;

        var lat = marker.getLatLng().lat;
        var lng = marker.getLatLng().lng;
        var radius = parseInt(this.value || 100);

        if (circle) {
            map.removeLayer(circle);
        }

        circle = L.circle([lat, lng], {
            radius: radius,
            color: '#2563eb',
            fillColor: '#2563eb',
            fillOpacity: 0.15
        }).addTo(map);
    });
</script>
@endsection