<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Restaurant-Lists</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400..700&family=Red+Hat+Display:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <link href="dashboard.css" rel="stylesheet">
    <link href="site.css" rel="stylesheet">
    <style>
        @media (max-width: 993px) {
            .col-lg-10 {
                flex: 0 0 auto;
                width: 83.33333333%;

            }

            .col-md-9 {
                flex: 0 0 auto;
                width: 75%;
            }

            .col-md-3 {
                flex: 0 0 auto;
                width: 25%;
            }
        }
    </style>
</head>

<body>

    <header class="navbar navbar-dark sticky-top flex-md-nowrap p-0 shadow header-bg">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 header-font" href="/">Find My Restaurant</a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation" style="padding: 15px;">
            <span class="navbar-toggler-icon"></span>
        </button>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse sidebar-bg">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item nav-item-cus">
                            <form id="search-form">
                                <div class="wrapper m-auto">
                                    <input type="text" id="search-input" value="<?php echo $search; ?>" placeholder="Enter location" />
                                    <button type="submit">
                                        <i class="fa fa-search" aria-hidden="true" style="font-size: large;"></i>
                                    </button>
                                </div>
                            </form>
                        </li>
                        <br>
                        <li class="nav-item nav-item-cus">
                            <div class="card mb-3">
                                <div class="row g-0">
                                    <div class="col-md-4 card-cus">
                                        <div><i class="fa fa-map-marker" aria-hidden="true" style="font-size: xxx-large;margin-top: 20%;"></i></div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <h5 class="card-title" id="search-result">12 Restaurant</h5>
                                            <p class="card-text">In Your Search.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content-bg">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div id="map"></div>
                </div>
                <br>
                <h2>Restaurant List</h2>
                <div class="table-responsive">
                    <table id="restaurant-table" class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($restaurants as $restaurant) : ?>
                                <tr>
                                    <td><?php echo $restaurant['name']; ?></td>
                                    <td><?php echo $restaurant['formatted_address']; ?></td>
                                    <td><?php echo isset($restaurant['rating']) ? $restaurant['rating'] : 'N/A'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo env('GOOGLE_MAPS_API_KEY') ?>&callback=initMap" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script>

    <script>
        let map;
        let markers = [];
        let infoWindow;

        function initMap() {
            const initialLocation = {
                lat: 13.8203,
                lng: 100.5144
            }; // Default Bang Sue

            map = new google.maps.Map(document.getElementById('map'), {
                center: initialLocation,
                zoom: 15
            });

            infoWindow = new google.maps.InfoWindow();

            loadRestaurants(<?php echo json_encode($restaurants, JSON_UNESCAPED_UNICODE); ?>);
        }

        function loadRestaurants(restaurants) {
            // Clear existing markers
            markers.forEach(marker => marker.setMap(null));
            markers = [];

            // Clear DataTable and add new data
            const table = $('#restaurant-table').DataTable();
            table.clear();

            // Get the search result element
            const searchResultElement = document.getElementById("search-result");

            // Update search result message
            if (restaurants.length > 0) {
                searchResultElement.innerHTML = restaurants.length + " Restaurant(s)";
            } else {
                searchResultElement.innerHTML = "No Restaurants Found";
            }


            restaurants.forEach((restaurant, index) => {
                const location = {
                    lat: restaurant.geometry.location.lat,
                    lng: restaurant.geometry.location.lng
                };

                const marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    title: restaurant.name
                });

                // Add listener to Show detail on click the table row
                marker.addListener('click', () => {
                    const photoUrl = restaurant.photos && restaurant.photos.length > 0 ?
                        `https://maps.googleapis.com/maps/api/place/photo?maxwidth=200&photoreference=${restaurant.photos[0].photo_reference}&key=<?php echo env('GOOGLE_MAPS_API_KEY'); ?>` :
                        '';

                    infoWindow.setContent(`
                    <div>
                        <h3>${restaurant.name}</h3>
                        <p>${restaurant.formatted_address}</p>
                        ${photoUrl ? `<img src="${photoUrl}" alt="${restaurant.name}" style="width: 100%; height: auto;">` : ''}
                    </div>
                `);
                    infoWindow.open(map, marker);
                });

                markers.push(marker);

                // Add restaurant details to the DataTable
                const rating = restaurant.rating || 0; // Default to 0 if no rating
                let ratingHtml = '';
                let starColorClass = '';

                if (rating < 3) {
                    starColorClass = 'text-danger';
                } else if (rating >= 3) {
                    starColorClass = 'text-warning';
                }

                for (let i = 0; i < Math.floor(rating); i++) {
                    ratingHtml += `<i class="fa fa-star ${starColorClass}"></i>`;
                }

                if (rating % 1 >= 0.5) {
                    ratingHtml += `<i class="fa fa-star-half-o ${starColorClass}"></i>`;
                }

                // Optional: Add empty stars to make it 5
                for (let i = Math.floor(rating) + (rating % 1 >= 0.5 ? 1 : 0); i < 5; i++) {
                    ratingHtml += `<i class="fa fa-star-o"></i>`;
                }
                ratingHtml += restaurant.rating > 0 ? ' ' + restaurant.rating : ' N/A';
                table.row.add([
                    restaurant.name,
                    restaurant.formatted_address,
                    ratingHtml
                ]).draw(false); // Draw without resetting page
            });

            // Attach click event to table rows
            $('#restaurant-table tbody').on('click', 'tr', function() {
                const rowIndex = table.row(this).index(); // Get the index of the clicked row
                const selectedMarker = markers[rowIndex];

                if (selectedMarker) {
                    map.setCenter(selectedMarker.getPosition());
                    map.setZoom(17); // Zoom in for better visibility
                    const photoUrl = restaurants[rowIndex].photos && restaurants[rowIndex].photos.length > 0 ?
                        `https://maps.googleapis.com/maps/api/place/photo?maxwidth=200&photoreference=${restaurants[rowIndex].photos[0].photo_reference}&key=<?php echo env('GOOGLE_MAPS_API_KEY'); ?>` :
                        '';

                    infoWindow.setContent(`
                    <div>
                        <h3>${restaurants[rowIndex].name}</h3>
                        <p>${restaurants[rowIndex].formatted_address}</p>
                        ${photoUrl ? `<img src="${photoUrl}" alt="${restaurants[rowIndex].name}" style="width: 100%; height: auto;">` : ''}
                    </div>
                `);
                    infoWindow.open(map, selectedMarker);

                    // Scroll to the top of the page
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });

            if (restaurants.length > 0) {
                map.setCenter({
                    lat: restaurants[0].geometry.location.lat,
                    lng: restaurants[0].geometry.location.lng
                });
            }
        }

        document.getElementById('search-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const searchInput = document.getElementById('search-input').value;
            fetch(`/restaurant?search=${searchInput}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    loadRestaurants(data.restaurants);
                })
                .catch(error => {
                    console.error('Error fetching restaurant data:', error);
                });
        });

        $(document).ready(function() {
            $('#restaurant-table').DataTable();
        });
    </script>




</body>

</html>