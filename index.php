<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Population Density Observation Dashboard — Johor</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f9fafb; color: #111827; }

    /* HEADER */
    header {
      background: #fff;
      border-bottom: 1px solid #d1d5db;
      padding: 16px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    header h1 { font-size: 22px; font-weight: 700; color: #111827; }
    header p  { font-size: 13px; color: #6b7280; margin-top: 2px; }
    .header-btns { display: flex; gap: 8px; align-items: center; }
    .header-btns select, .header-btns button {
      padding: 6px 14px;
      border: 1.5px solid #d1d5db;
      border-radius: 6px;
      font-size: 13px;
      background: #fff;
      cursor: pointer;
      color: #374151;
    }
    .header-btns button {
      background: #1d4ed8;
      color: #fff;
      border-color: #1d4ed8;
    }
    .header-btns button:hover { background: #1e40af; }

    /* LAYOUT */
    .main { padding: 24px; display: flex; flex-direction: column; gap: 24px; }

    /* KPI CARDS */
    .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
    .kpi-card {
      background: #fff;
      border: 1.5px solid #e5e7eb;
      border-radius: 10px;
      padding: 20px;
    }
    .kpi-top { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
    .kpi-icon {
      width: 36px; height: 36px; border-radius: 8px;
      background: #eff6ff;
      display: flex; align-items: center; justify-content: center; font-size: 18px;
    }
    .kpi-label { font-size: 13px; color: #6b7280; font-weight: 500; }
    .kpi-value { font-size: 26px; font-weight: 700; color: #111827; }
    .kpi-sub   { font-size: 12px; color: #10b981; margin-top: 3px; }
    .kpi-sub.negative { color: #ef4444; }

    /* MAP + DISTRICT LIST ROW */
    .map-row { display: grid; grid-template-columns: 2fr 1fr; gap: 16px; }
    .card {
      background: #fff;
      border: 1.5px solid #e5e7eb;
      border-radius: 10px;
      padding: 20px;
    }
    .card h2 { font-size: 15px; font-weight: 600; color: #374151; margin-bottom: 14px; }

    /* LEAFLET MAP */
    #map { height: 380px; border-radius: 8px; border: 1px solid #e5e7eb; }

    .map-legend {
      display: flex; align-items: center; gap: 12px;
      margin-top: 12px; flex-wrap: wrap;
    }
    .map-legend span { font-size: 12px; color: #6b7280; }
    .legend-item { display: flex; align-items: center; gap: 4px; font-size: 12px; color: #6b7280; }
    .legend-box { width: 20px; height: 14px; border-radius: 3px; border: 1px solid #d1d5db; }

    /* DISTRICT LIST */
    .district-list { display: flex; flex-direction: column; gap: 8px; max-height: 420px; overflow-y: auto; }
    .district-item {
      padding: 10px 12px;
      border: 1px solid #e5e7eb;
      border-radius: 7px;
      background: #f9fafb;
      display: flex; justify-content: space-between; align-items: center;
      cursor: pointer; transition: background 0.15s;
    }
    .district-item:hover { background: #eff6ff; border-color: #bfdbfe; }
    .district-item.active { background: #dbeafe; border-color: #3b82f6; border-width: 2px; }
    .district-item { cursor: pointer; }
    .district-pin { font-size: 14px; }
    .district-name { font-size: 13px; font-weight: 500; color: #374151; }
    .district-density {
      font-size: 12px; font-weight: 600; color: #1d4ed8;
      background: #eff6ff; padding: 2px 8px; border-radius: 999px;
    }

    /* CHARTS */
    .charts-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .chart-wrap { height: 280px; position: relative; }

    /* TABLE */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    thead tr { border-bottom: 2px solid #e5e7eb; }
    thead th {
      text-align: left; padding: 10px 14px;
      font-size: 12px; font-weight: 600; color: #6b7280;
      text-transform: uppercase; letter-spacing: 0.04em; background: #f9fafb;
    }
    tbody tr { border-bottom: 1px solid #f3f4f6; transition: background 0.1s; }
    tbody tr:hover { background: #f0f9ff; }
    tbody td { padding: 10px 14px; color: #374151; }
    .badge {
      display: inline-block; font-size: 11px; font-weight: 600;
      padding: 2px 8px; border-radius: 999px;
    }
    .badge-green  { background: #d1fae5; color: #065f46; }
    .badge-red    { background: #fee2e2; color: #991b1b; }
    .badge-blue   { background: #dbeafe; color: #1e40af; }
    .prelim-note  { font-size: 11px; color: #9ca3af; margin-top: 10px; text-align: right; }
    .note-box {
      background: #fefce8; border: 1px solid #fde68a;
      border-radius: 8px; padding: 8px 12px;
      font-size: 12px; color: #92400e; margin-top: 10px;
    }

    @media (max-width: 900px) {
      .kpi-grid { grid-template-columns: 1fr 1fr; }
      .map-row, .charts-row { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<?php
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : 2024;

// KPI — state level
$kpi = $conn->query("SELECT population_000, density_per_km2, growth_rate_pct
                     FROM population_data
                     WHERE district = 'Johor (State)' AND year = $selected_year")->fetch_assoc();

// District data for selected year
$dist_result = $conn->query("SELECT district, population_000, density_per_km2, growth_rate_pct,
                                    male_000, female_000, citizens_000, non_citizens_000
                             FROM population_data
                             WHERE year = $selected_year AND district != 'Johor (State)'
                             ORDER BY density_per_km2 DESC");
$districts = [];
while ($row = $dist_result->fetch_assoc()) $districts[] = $row;

// Trend data
$trend_result = $conn->query("SELECT year, population_000, density_per_km2
                              FROM population_data
                              WHERE district = 'Johor (State)'
                              ORDER BY year");
$trend_years = []; $trend_pop = []; $trend_dens = [];
while ($row = $trend_result->fetch_assoc()) {
  $trend_years[] = $row['year'];
  $trend_pop[]   = $row['population_000'];
  $trend_dens[]  = $row['density_per_km2'];
}

// Chart arrays
$dist_names   = array_column($districts, 'district');
$dist_density = array_column($districts, 'density_per_km2');
$dist_pop     = array_column($districts, 'population_000');

// Density lookup for JS map colouring
$density_lookup = [];
foreach ($districts as $d) {
    $density_lookup[$d['district']] = (float)$d['density_per_km2'];
}

$total_pop   = number_format($kpi['population_000'] * 1000);
$avg_density = $kpi['density_per_km2'];
$growth      = $kpi['growth_rate_pct'];
?>

<!-- HEADER -->
<header>
  <div>
    <h1>🗺️ Population Density Observation Dashboard</h1>
    <p>Johor State, Malaysia &nbsp;|&nbsp; Source: DOSM Malaysia &nbsp;|&nbsp; Unit: persons/km²</p>
  </div>
  <div class="header-btns">
    <form method="GET" style="display:flex; gap:8px; align-items:center;">
      <label style="font-size:13px; color:#6b7280;">Year:</label>
      <select name="year" onchange="this.form.submit()">
        <?php foreach ([2020,2021,2022,2023,2024] as $yr): ?>
          <option value="<?= $yr ?>" <?= $yr == $selected_year ? 'selected' : '' ?>>
            <?= $yr ?><?= $yr == 2024 ? ' (p)' : '' ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="button" onclick="window.print()">⬇ Export</button>
    </form>
  </div>
</header>

<div class="main">

  <!-- KPI CARDS -->
  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-top"><div class="kpi-icon">👥</div><span class="kpi-label">Total Population</span></div>
      <div class="kpi-value"><?= $total_pop ?></div>
      <div class="kpi-sub"><?= $selected_year == 2024 ? '(p) Preliminary' : 'DOSM ' . $selected_year ?></div>
    </div>
    <div class="kpi-card">
      <div class="kpi-top"><div class="kpi-icon">📍</div><span class="kpi-label">Total Area</span></div>
      <div class="kpi-value">19,166 km²</div>
      <div class="kpi-sub">Johor State</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-top"><div class="kpi-icon">📊</div><span class="kpi-label">Avg Density</span></div>
      <div class="kpi-value"><?= $avg_density ?> /km²</div>
      <div class="kpi-sub">State average <?= $selected_year ?></div>
    </div>
    <div class="kpi-card">
      <div class="kpi-top"><div class="kpi-icon">📈</div><span class="kpi-label">Growth Rate</span></div>
      <div class="kpi-value"><?= $growth ? '+' . $growth . '%' : 'N/A' ?></div>
      <div class="kpi-sub <?= $growth < 0 ? 'negative' : '' ?>">
        <?= $growth ? ($growth >= 0 ? '▲ Annual growth' : '▼ Annual decline') : 'Base year 2020' ?>
      </div>
    </div>
  </div>

  <!-- MAP + DISTRICT LIST -->
  <div class="map-row">
    <div class="card">
      <h2>Johor Districts Map — Population Density (<?= $selected_year ?>)</h2>
      <div id="map"></div>
      <div class="map-legend">
        <span>Density (per km²):</span>
        <div class="legend-item"><div class="legend-box" style="background:#bbf7d0;"></div> &lt;100</div>
        <div class="legend-item"><div class="legend-box" style="background:#facc15;"></div> 100–250</div>
        <div class="legend-item"><div class="legend-box" style="background:#fb923c;"></div> 250–500</div>
        <div class="legend-item"><div class="legend-box" style="background:#ef4444;"></div> 500–1000</div>
        <div class="legend-item"><div class="legend-box" style="background:#991b1b;"></div> &gt;1000</div>
      </div>
      <div class="note-box">
        ⚠️ Kulai &amp; Tangkak boundaries not available in this shapefile (districts created post-2000).
        Data for both districts is included in the table and charts below.
      </div>
    </div>

    <div class="card">
      <h2>Districts — ranked by density</h2>
      <div class="district-list">
        <?php foreach ($districts as $d): ?>
        <div class="district-item" onclick="focusDistrict('<?= htmlspecialchars($d['district']) ?>')" id="item-<?= preg_replace('/\s+/', '-', strtolower(htmlspecialchars($d['district']))) ?>">
          <div style="display:flex; align-items:center; gap:6px;">
            <span class="district-pin">📍</span>
            <span class="district-name"><?= htmlspecialchars($d['district']) ?></span>
          </div>
          <span class="district-density"><?= number_format($d['density_per_km2']) ?> /km²</span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- CHARTS -->
  <div class="charts-row">
    <div class="card">
      <h2>Population Trend — Johor State (2020–2024)</h2>
      <div class="chart-wrap"><canvas id="trendChart"></canvas></div>
    </div>
    <div class="card">
      <h2>District Density Comparison (<?= $selected_year ?>)</h2>
      <div class="chart-wrap"><canvas id="barChart"></canvas></div>
    </div>
  </div>

  <!-- DATA TABLE -->
  <div class="card">
    <h2>Population Data Table — <?= $selected_year ?></h2>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>District</th>
            <th>Population ('000)</th>
            <th>Density (/km²)</th>
            <th>Growth Rate (%)</th>
            <th>Male ('000)</th>
            <th>Female ('000)</th>
            <th>Citizens ('000)</th>
            <th>Non-Citizens ('000)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($districts as $d): ?>
          <tr>
            <td><strong><?= htmlspecialchars($d['district']) ?></strong></td>
            <td><?= number_format($d['population_000'], 1) ?></td>
            <td><span class="badge badge-blue"><?= number_format($d['density_per_km2']) ?></span></td>
            <td>
              <?php if ($d['growth_rate_pct'] === null): ?>
                <span style="color:#9ca3af;">—</span>
              <?php elseif ($d['growth_rate_pct'] >= 0): ?>
                <span class="badge badge-green">▲ <?= number_format($d['growth_rate_pct'], 2) ?>%</span>
              <?php else: ?>
                <span class="badge badge-red">▼ <?= number_format($d['growth_rate_pct'], 2) ?>%</span>
              <?php endif; ?>
            </td>
            <td><?= number_format($d['male_000'], 1) ?></td>
            <td><?= number_format($d['female_000'], 1) ?></td>
            <td><?= number_format($d['citizens_000'], 1) ?></td>
            <td><?= number_format($d['non_citizens_000'], 1) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if ($selected_year == 2024): ?>
      <p class="prelim-note">* (p) = Preliminary data, subject to revision by DOSM Malaysia</p>
    <?php endif; ?>
  </div>

</div><!-- end main -->

<script>
// ── Chart.js data from PHP
const trendYears  = <?= json_encode($trend_years) ?>;
const trendPop    = <?= json_encode($trend_pop) ?>;
const distNames   = <?= json_encode($dist_names) ?>;
const distDensity = <?= json_encode($dist_density) ?>;
const densityLookup = <?= json_encode($density_lookup) ?>;

// ── Colour function by density
function densityColor(d) {
  return d > 1000 ? '#991b1b' :  // dark red — very high
         d > 500  ? '#ef4444' :  // red — high
         d > 250  ? '#fb923c' :  // orange — medium
         d > 100  ? '#facc15' :  // yellow — low
                     '#bbf7d0';  // green — very low
}

// ── Line chart — population trend
new Chart(document.getElementById('trendChart'), {
  type: 'line',
  data: {
    labels: trendYears,
    datasets: [{
      label: "Population ('000)",
      data: trendPop,
      borderColor: '#2563eb',
      backgroundColor: 'rgba(37,99,235,0.08)',
      borderWidth: 2.5,
      pointBackgroundColor: '#2563eb',
      pointRadius: 5,
      fill: true,
      tension: 0.3
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      y: { ticks: { font: { size: 11 } }, grid: { color: '#f3f4f6' } },
      x: { ticks: { font: { size: 11 } }, grid: { display: false } }
    }
  }
});

// ── Bar chart — district density
new Chart(document.getElementById('barChart'), {
  type: 'bar',
  data: {
    labels: distNames,
    datasets: [{
      label: 'Density (per km²)',
      data: distDensity,
      backgroundColor: distDensity.map(v => densityColor(v)),
      borderRadius: 5,
      borderSkipped: false
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      y: { ticks: { font: { size: 11 } }, grid: { color: '#f3f4f6' } },
      x: { ticks: { font: { size: 10 }, maxRotation: 30 }, grid: { display: false } }
    }
  }
});

// ── Leaflet map
const map = L.map('map').setView([2.1, 103.5], 8);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Store layers by district name
const districtLayers = {};
let activeLayer = null;

function resetStyle(layer, name) {
  const d = densityLookup[name] || 0;
  layer.setStyle({
    fillColor: densityColor(d),
    fillOpacity: 0.7,
    color: '#fff',
    weight: 1.5
  });
}

function highlightLayer(layer, name) {
  layer.setStyle({
    fillColor: densityColor(densityLookup[name] || 0),
    fillOpacity: 0.95,
    color: '#1d4ed8',
    weight: 3
  });
  layer.bringToFront();
}

// Click district list item → zoom + highlight on map
function focusDistrict(name) {
  // Remove active class from all items
  document.querySelectorAll('.district-item').forEach(el => el.classList.remove('active'));

  // Add active class to clicked item
  const slug = name.toLowerCase().replace(/\s+/g, '-');
  const item = document.getElementById('item-' + slug);
  if (item) {
    item.classList.add('active');
    item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  // Highlight on map
  if (districtLayers[name]) {
    // Reset previous active layer
    if (activeLayer && activeLayer !== districtLayers[name]) {
      const prevName = activeLayer.feature.properties.district;
      resetStyle(activeLayer, prevName);
    }
    activeLayer = districtLayers[name];
    highlightLayer(activeLayer, name);

    // Zoom to district bounds
    map.fitBounds(activeLayer.getBounds(), { padding: [30, 30], maxZoom: 11 });

    // Open popup
    const dens = densityLookup[name] || 'N/A';
    activeLayer.openPopup();
  }
}

// Load GeoJSON
fetch('johor_10districts.geojson')
  .then(r => r.json())
  .then(data => {
    L.geoJSON(data, {
      style: feature => {
        const d = densityLookup[feature.properties.district] || 0;
        return {
          fillColor: densityColor(d),
          fillOpacity: 0.7,
          color: '#fff',
          weight: 1.5
        };
      },
      onEachFeature: (feature, layer) => {
        const name = feature.properties.district;
        const dens = densityLookup[name] || 'N/A';

        // Store layer reference
        districtLayers[name] = layer;

        layer.bindPopup(
          `<strong>${name}</strong><br>Density: <b>${dens} /km²</b>`
        );

        // Click on map → highlight list item too
        layer.on('click', function() {
          focusDistrict(name);
        });

        layer.on('mouseover', function() {
          if (this !== activeLayer) {
            this.setStyle({ fillOpacity: 0.9, weight: 2.5 });
          }
        });
        layer.on('mouseout', function() {
          if (this !== activeLayer) {
            resetStyle(this, name);
          }
        });
      }
    }).addTo(map);
  });
</script>

</body>
</html>
