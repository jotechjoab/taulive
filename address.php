<!DOCTYPE html>
<html lang='en'>

<head>
  <meta charset='utf-8' />
  <title>Tree Adoption Uganda Track Your Trees On a Live Map</title>
  <meta name='viewport' content='width=device-width, initial-scale=1' />
  <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
 <!--  <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.js'></script>
  <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.css' rel='stylesheet' /> -->
  <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <style>
    body {
      margin: 0;
      padding: 0;
    }

    #map {
      position: absolute;
      top: 0;
      bottom: 0;
      width: 100%;
    }

    .marker {
      background-image: url('icon.png');
      background-size: cover;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      cursor: pointer;
    }

    .mapboxgl-popup {
      max-width: 300px;
    }

    .mapboxgl-popup-content {
      text-align: center;
      font-family: 'Open Sans', sans-serif;
    }

    .element-with-timer {
      /*height: 100%;*/
      padding: 50px;
      position: absolute;
      z-index: 10;
      margin-top: 400px;
      margin-left: 50px;
      background: #020202cf;
      color: #FFF;
      border-radius: 20px;
    }
  </style>
</head>

<body>
  <div class="element-with-timer">
    <h3>Track Your Trees On a Live Map </h3>
    <form method="GET" action="">
      <input type="text" name="tree_no" style="height: 30px; width: 200px;" placeholder="Enter Your Tree Code Here "><button style="height: 36px; width: 36px; margin-left: 10px;" type="submit">Go</button>
      <p> <small><i>Eg. TAU_500</i></small></p>
      <span id="msg"></span>
    </form>
  </div>
  <div id='map'>

  </div>

<script>
  mapboxgl.accessToken = 'pk.eyJ1Ijoiam90ZWNoam9hYiIsImEiOiJjbGo2dGV6aGQwcG12M2RxeXl4bHd5Z3o3In0.sOak1G_g2cu35O_MmLZd2A';

  const map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/streets-v12',
    center: [32.4950852685138, 0.6376670430058137],
    zoom: <?php if (isset($_GET['tree_no'])) { echo 14; } else { echo 2; } ?>
  });

  map.addControl(
    new mapboxgl.GeolocateControl({
      positionOptions: {
        enableHighAccuracy: true
      },
      trackUserLocation: true,
      showUserHeading: true
    })
  );

  <?php
  require 'conn.php';

  if (isset($_GET['tree_no'])) {
    $treeNo = $_GET['tree_no'];
    $sql1 = "SELECT * FROM trees WHERE tracking_code='$treeNo' LIMIT 1";
    $result1 = $mysqli->query($sql1);

    if ($result1->num_rows > 0) {
      $row1 = mysqli_fetch_assoc($result1);
      $parts1 = explode(',', $row1['coodinates']);
      $latitude1 = $parts1[0];
      $longitude1 = $parts1[1];
  ?>
      var msg = document.getElementById("msg");
      msg.style.color = "#FFF";
      msg.innerHTML = "<h3>Tree Details</h3> <div style='text-align:left;'><p>Tree Name: <b><?php echo $row1['name_of_tree']; ?></b></p><p>Tree Planter: <b><?php echo $row1['planter']; ?></b></p><p>Date Of Planting : <b><?php echo $row1['date_of_planting']; ?></b></p><p>Coordinates : <b><?php echo  $row1['coodinates']; ?></b></p>";

      map.setCenter([<?php echo $longitude1; ?>, <?php echo $latitude1; ?>]);
  <?php
    }
    $result1->close();
  }
  ?>
    <?php



  if ($mysqli->connect_errno) {
    // printf("Connect failed: %s<br />", $mysqli->connect_error);
    exit();
  }
  // printf('Connected successfully.<br />');
  $sql = "";
  if (isset($_GET['tree_no'])) {
    $t_no = $_GET['tree_no'];
    $sql = "SELECT * FROM trees  WHERE tracking_code='$treeNo'";
  } else {
    $sql = "SELECT * FROM trees  ORDER BY created_at DESC LIMIT 100";
  }

  $result = $mysqli->query($sql);

  if ($result->num_rows > 0) {
  } else {
    echo '<script> var msg=document.getElementById("msg");
          msg.innerHTML="Sorry No Records found";
          msg.style.color = "magenta";</script>';  // printf('Unexpected Error. DB did not return enough values for a successful export.<br />');
  }

  ?>

  function reverseGeocode(coordinates, callback) {
    const reverseGeocodingEndpoint = `https://nominatim.openstreetmap.org/reverse?lat=${coordinates.lat}&lon=${coordinates.lng}&format=jsonv2`;

    $.getJSON(reverseGeocodingEndpoint, function(data) {
      if (data.address) {
        const address = `${data.address.road}, ${data.address.city}, ${data.address.state}, ${data.address.country}`;
        callback(address);
        console.log("Address=" + address);
      } else {
        callback(null);
      }
    });
  }

  const geojson = {
    type: 'FeatureCollection',
    features: [
      <?php
      while ($row = mysqli_fetch_assoc($result)) {
        $displayName = $row['name_of_tree'];
        $displayDescription = $row['planter'];
        $date_of_planting = $row['date_of_planting'];
        $image_url = $row['image_url'];
        $parts = explode(',', $row['coodinates']);
        $coodinates = $row['coodinates'];

        // Retrieve the separated coordinates
        $latitude = $parts[0];
        $longitude = $parts[1];
      ?>
        {
          type: "Feature",
          geometry: {
            type: "Point",
            coordinates: [<?php echo $longitude . ',' . $latitude; ?>]
          },
          properties: {
            title: "<?php echo $displayName; ?>",
            description: "<?php echo $displayDescription; ?>",
            date_of_planting: "<?php echo $date_of_planting; ?>",
            image_url: "<?php echo $image_url; ?>",
            cordinates: "<?php echo $coodinates; ?>",
            address: ""
          }
        },
      <?php
      }
      mysqli_free_result($result);
      $mysqli->close();
      ?>
    ]
  };

  for (const feature of geojson.features) {
    const el = document.createElement('div');
    el.className = 'marker';

    new mapboxgl.Marker(el)
      .setLngLat(feature.geometry.coordinates)
      .setPopup(
        new mapboxgl.Popup({ offset: 25 })
          .setHTML(
            `<h3>Tree Details</h3>
            <img src="../app/${feature.properties.image_url}" style="width:200px; height:200px;">
            <div style="text-align:left;">
              <p>Tree Name: <b>${feature.properties.title}</b></p>
              <p>Tree Planter: <b>${feature.properties.description}</b></p>
              <p>Date Of Planting: <b>${feature.properties.date_of_planting}</b></p>
              <p>Coordinates: <b>${feature.properties.cordinates}</b></p>
              <p>Address: <b id="id_span">Loading...</b></p>
            </div>`
          )
      )
      .addTo(map);

    reverseGeocode({ lat: feature.geometry.coordinates[1], lng: feature.geometry.coordinates[0] }, function(address) {
      const popupContent = el.getElementsByClassName('mapboxgl-popup-content')[0];
      const addressElement = $("#id_span");
      addressElement.textContent = address;
      console.log(address);
    });
  }
</script>

</body>

</html>