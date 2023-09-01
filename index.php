<!DOCTYPE html>
<html lang='en'>

<head>
  <meta charset='utf-8' />
  <title>Tree Adoption Uganda Track Your Trees On a Live Map</title>
  <meta name='viewport' content='width=device-width, initial-scale=1' />
  <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <!--  <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.js'></script>
  <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.css' rel='stylesheet' /> -->
  <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">
  <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
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
      max-width: 500px;
    }

    .mapboxgl-popup-content {
      text-align: center;
      font-family: 'Open Sans', sans-serif;
    }

    .header {

      padding: 50px;
      position: absolute;
      z-index: 10;
      margin-top: 100px;
      margin-left: 50px;
      background: #020202cf;
      color: #FFF;
      border-radius: 20px;
      max-width: 568px;
    }



    @media (max-width:568px) {
      .header {
        padding: 5px;
        /* Reduce padding for smaller screens */
      }

         .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #020202cf;
      color: #FFF;
      padding: 10px;
      border-radius: 20px;
      margin: 10px;
    }


      .search-container {
        display: flex;
        justify-content: center;
        align-items: center;
        /*background-color: #020202cf;*/
        color: #FFF;
        border-radius: 20px;
        padding: 5px;
        /* Add padding for better visual appearance */
      }



      input[type="text"] {
        /*width: 100%;*/
        /* Make the input field fill the available space */
        /*max-width: 200px;*/
        /* Limit the maximum width of the input field */
        /*height: 30px;*/
      }

  /*    button {
        height: 30px;
        width: 30px;
      }*/
    }
  </style>
</head>

<body>
    <div class="header">
      <div class="row">
    <h5 style="text-align: center;"><b>Track Your Trees On a Live Map</b></h5>
    <div class="search-container col-md-12">
      <form method="GET" action="">
        <div class="row">
        <div class="form-group col-md-6">
        <input type="text" name="tree_no" placeholder="Enter Your Tree Code Here" class="form-control">
      </div>
      <div class="form-group col-md-4">
        <button type="submit" class="btn btn-primary">Go</button>
      </div>
    </div>
      </form>
      
    </div>
    <div class="form-group col-md-12"><small><i>Eg. TAU_500</i></small></div>
    <div class="col-md-12"><button onclick="share()" class="btn btn-success btn-sm">Share</button></div>
    <span id="notice"></span>
  </div>
  </div>
  <!-- <div class="element-with-timer">
    <h3>Track Your Trees On a Live Map </h3>
    <form method="GET" action="">
      <input type="text" name="tree_no" style="height: 30px; width: 200px;" placeholder="Enter Your Tree Code Here "><button style="height: 36px; width: 36px; margin-left: 10px;" type="submit">Go</button>
      <p> <small><i>Eg. TAU_500</i></small></p>
      <span id="msg"></span>
      <span id="notice"></span>
     
     
    </form>
     <button onclick="share()">Share</button>
  </div> -->
  <div id='map'>

  </div>

  <script>
    mapboxgl.accessToken = 'pk.eyJ1Ijoiam90ZWNoam9hYiIsImEiOiJjbGo2dGV6aGQwcG12M2RxeXl4bHd5Z3o3In0.sOak1G_g2cu35O_MmLZd2A';

    const map = new mapboxgl.Map({
      container: 'map',
      projection: 'globe',
      // style: 'mapbox://styles/mapbox/streets-v11',
      // mapbox://styles/mapbox/satellite-v9
      style: 'mapbox://styles/mapbox/streets-v12',
      center: [32.4950852685138, 0.6376670430058137],
      zoom: <?php if (isset($_GET['tree_no'])) {
              echo 14;
            } else {
              echo 2;
            } ?>
    });
    // Add geolocate control to the map.
    map.addControl(
      new mapboxgl.GeolocateControl({
        positionOptions: {
          enableHighAccuracy: true
        },
        // When active the map will receive updates to the device's location as it changes.
        trackUserLocation: true,
        // Draw an arrow next to the location dot to indicate which direction the device is heading.
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
        // var msg = document.getElementById("msg");
        // msg.style.color = "#FFF";
        // msg.innerHTML = "<h3>Tree Details</h3> <div style='text-align:left;'><p>Tree Name: <b> <?php echo $row1['name_of_tree']; ?></b></p><p>Tree Planter: <b><?php echo $row1['planter']; ?></b></p><p>Date Of Planting : <b><?php echo $row1['date_of_planting']; ?></b></p><p>Coordinates : <b><?php echo  $row1['coodinates']; ?></b></p>";

        // Set the center of the map to the specific tree coordinates
        map.setCenter([<?php echo $longitude1; ?>, <?php echo $latitude1; ?>]);
        // map.setZoom(14);
    <?php
      }
      $result1->close();
    }
    ?>

    // Adding markers below >>>
  </script>
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
    $sql = "SELECT * FROM trees  ORDER BY created_at DESC LIMIT 1";
  }

  $result = $mysqli->query($sql);

  if ($result->num_rows > 0) {
  } else {
  //   echo '<script> var msg=document.getElementById("msg");
  //         msg.innerHTML="Sorry No Records found";
  //         msg.style.color = "magenta";</script>';  // printf('Unexpected Error. DB did not return enough values for a successful export.<br />');
  }

  ?>

  <script>
    function reverseGeocode(coordinates, callback) {
      const geocodingEndpoint = `https://api.mapbox.com/geocoding/v5/mapbox.places/${coordinates.lng},${coordinates.lat}.json`;

      const params = {
        access_token: mapboxgl.accessToken,
        types: 'address',
        limit: 1,
      };

      $.getJSON(geocodingEndpoint, params, function(data) {
        if (data.features.length > 0) {
          const place = data.features[0];
          const addressComponents = place.context.map(component => component.text);
          const formattedAddress = addressComponents.join(',');


          callback(formattedAddress);
          console.log("Address=" + formattedAddress);
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

        ?> {
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
              address: "<?php echo $coodinates; ?>",
            }
          },
        <?php

        }

        mysqli_free_result($result);
        $mysqli->close();
        ?>
      ]
    };

    // add markers to map
    // Popups and Display Details
    for (const feature of geojson.features) {
      // create a HTML element for each feature
      const el = document.createElement('div');
      el.className = 'marker';

      // make a marker for each feature and add it to the map
      new mapboxgl.Marker(el)
        .setLngLat(feature.geometry.coordinates)
        .setPopup(
          new mapboxgl.Popup({
            offset: 25
          }) // add popups
          .setHTML(
            `<h3>Tree Details</h3>
<img src="../app/${feature.properties.image_url}" style="width:200px; height:200px;">
<div style="text-align:left; style="width:200px;">
<p>Tree Name: <b> ${feature.properties.title}</b></p>
<p>Tree Planter: <b>${feature.properties.description}</b></p>
<p>Date Of Planting : <b>${feature.properties.date_of_planting}</b></p>
<p>Coordinates : <b>${feature.properties.cordinates}</b></p>
<p>Address : <b>${feature.properties.address}</b></p>
</div>
`
          )
        )
        .addTo(map);
    }

    map.on('style.load', () => {
      map.setFog({}); // Set the default atmosphere style
    });

    // The following values can be changed to control rotation speed:

    // At low zooms, complete a revolution every two minutes.
    const secondsPerRevolution = 120;
    // Above zoom level 5, do not rotate.
    const maxSpinZoom = 5;
    // Rotate at intermediate speeds between zoom levels 3 and 5.
    const slowSpinZoom = 3;

    let userInteracting = false;
    let spinEnabled = true;

    function spinGlobe() {
      const zoom = map.getZoom();
      if (spinEnabled && !userInteracting && zoom < maxSpinZoom) {
        let distancePerSecond = 360 / secondsPerRevolution;
        if (zoom > slowSpinZoom) {
          // Slow spinning at higher zooms
          const zoomDif =
            (maxSpinZoom - zoom) / (maxSpinZoom - slowSpinZoom);
          distancePerSecond *= zoomDif;
        }
        const center = map.getCenter();
        center.lng -= distancePerSecond;
        // Smoothly animate the map over one second.
        // When this animation is complete, it calls a 'moveend' event.
        map.easeTo({
          center,
          duration: 1000,
          easing: (n) => n
        });
      }
    }

    // Pause spinning on interaction
    map.on('mousedown', () => {
      userInteracting = true;
    });

    // Restart spinning the globe when interaction is complete
    map.on('mouseup', () => {
      userInteracting = false;
      spinGlobe();
    });

    // These events account for cases where the mouse has moved
    // off the map, so 'mouseup' will not be fired.
    map.on('dragend', () => {
      userInteracting = false;
      spinGlobe();
    });
    map.on('pitchend', () => {
      userInteracting = false;
      spinGlobe();
    });
    map.on('rotateend', () => {
      userInteracting = false;
      spinGlobe();
    });

    // When animation is complete, start spinning if there is no ongoing interaction
    map.on('moveend', () => {
      spinGlobe();
    });

    function openPopupOnLoad(feature) {
  const el = document.createElement('div');
  el.className = 'marker';

  new mapboxgl.Marker(el)
    .setLngLat(feature.geometry.coordinates)
    .setPopup(
      new mapboxgl.Popup({
        offset: 25
      })
      .setHTML(
        `<h3>Tree Details</h3>
<img src="../app/${feature.properties.image_url}" style="width:200px; height:200px;">
<div style="text-align:left;">
<p>Tree Name: <b> ${feature.properties.title}</b></p>
<p>Tree Planter: <b>${feature.properties.description}</b></p>
<p>Date Of Planting : <b>${feature.properties.date_of_planting}</b></p>
<p>Coordinates : <b>${feature.properties.cordinates}</b></p>
<p>Address : <b>${feature.properties.address}</b></p>
`
      )
    )
    .addTo(map)
    .togglePopup(); // Automatically open the popup

  map.off('style.load', openPopupOnLoad); // Remove the event listener after the first trigger
}

// Attach the 'style.load' event listener
map.on('style.load', () => {
  map.setFog({}); // Set the default atmosphere style
  for (const feature of geojson.features) {
    openPopupOnLoad(feature);
  }
});

function share(){
    var dummy = document.createElement('input'),
    text = window.location.href;

document.body.appendChild(dummy);
dummy.value = text;
dummy.select();
document.execCommand('copy');
document.body.removeChild(dummy);

 var msg=document.getElementById("notice");
 msg.innerHTML="A sharable link has been copied to your clipboard";

}
  </script>

</body>

</html>