<!DOCTYPE html>

<?php
	session_start();
	function getCoordinates($address){

	 $address = str_replace(" ", "+", $address); // replace all the white space with "+" sign to match with google search pattern

		$url = "http://maps.google.com/maps/api/geocode/json?sensor=false&region=MY&address=$address";

		$response = file_get_contents($url);

		$json = json_decode($response,TRUE); //generate array object from the response from the web

		return array('latitude'=>$json['results'][0]['geometry']['location']['lat'], 'longitude'=>$json['results'][0]['geometry']['location']['lng']);


	}
	
    date_default_timezone_set('Asia/Kuala_Lumpur');	
	$locationName = "";
    if (isset($_REQUEST['areaName'])) {
        $locationName = trim($_REQUEST['areaName']);
        $locationName = str_replace("-"," ",$locationName);
    }
	$landed = "";
    if (isset($_REQUEST['landed'])) {
        $landed = trim($_REQUEST['landed']);
    }
	$highrise = "";
	if (isset($_REQUEST['highrise'])) {
         $highrise= trim($_REQUEST['highrise']);
    }
	$room = "";
	if (isset($_REQUEST['bedroom'])) {
         $room = trim($_REQUEST['bedroom']);
    }
	$carpark = "";
	if (isset($_REQUEST['carpark'])) {
         $carpark = trim($_REQUEST['carpark']);
    }
	$page = "0";
	if (isset($_REQUEST['page'])) {
         $page = trim($_REQUEST['page']);
    }
	$offset = "0";
	if (isset($_REQUEST['offset'])) {
         $offset = trim($_REQUEST['offset']);
    }
	$furnishType = "";
	if (isset($_REQUEST['furnishing'])) {
         $furnishType = trim($_REQUEST['furnishing']);
    }
	$minPrice = "";
	if (isset($_REQUEST['minPrice'])) {
         $minPrice = trim($_REQUEST['minPrice']);
    }
	$isFilter = "";
	if (isset($_REQUEST['isFilter'])) {
         $isFilter = trim($_REQUEST['isFilter']);
    }
    if ($isFilter && $isFilter != "1") {
        $isFilter = "0";
    }
	$maxPrice = "";
	if (isset($_REQUEST['maxPrice'])) {
         $maxPrice = trim($_REQUEST['maxPrice']);
    }
	
	$furnish = array('FULL' => "Fully Furnished", 'PARTIAL'=>"Partial Furnished",'NONE'=>"Basic Furnished");
	$postFurnish = array("Basic Furnishing" => "NONE", "Partial Furnishing" => "PARTIAL", "Fully Furnishing" => "FULL");
    if (!array_key_exists($furnishType ,$postFurnish)) {
        $furnishType = "";
    }
    $selected = "selected='selected'";
 

    $mysqli = new mysqli('localhost', 'root', 'noobie', 'speedrent');
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    if ($locationName && $locationName != '') {
    }
    $sql = "SELECT p.mobile_user_id,p.ref,p.price,p.id,p.name,p.address,p.sqft,p.type,p.furnish_type,p.bedroom,p.bathroom,p.carpark,DATE(p.availability) as availability,p.discount FROM property as p WHERE p.is_active = 1";
    if (($highrise || $landed)  && $isFilter == "1") {
        if ($highrise != "" && $landed != "") {
        }
        else {
            $pType = ($highrise != "") ? $highrise : $landed;
            $sql = $sql." AND type ='".$mysqli->real_escape_string($pType)."'";
        }
    }

    if ($room && $room != '' && $isFilter == "1" && $room != "Any") {
        $sql = $sql." AND bedroom =".$mysqli->real_escape_string($room);
    }

    if ($carpark && $carpark != '' && $isFilter == "1" && $carpark != "Any") {
        $sql = $sql." AND carpark=".$mysqli->real_escape_string($carpark);
    }

    if ($furnishType && $furnishType != '' && $isFilter == "1" && $furnishType != "Any") {
        $sql = $sql." AND furnish_type='".$mysqli->real_escape_string($postFurnish[$furnishType])."'";
    }
    if ($room == "Any") $room = "";
    if ($carpark == "Any") $carpark = "";
    if ($furnishType == "Any") $furnishType = "";

    if ($minPrice && $minPrice != '' && $isFilter == "1") {
        $sql = $sql." AND price >=".$mysqli->real_escape_string($minPrice);
    }
    if ($maxPrice && $maxPrice != '' && $isFilter == "1") {
        $sql = $sql." AND price <=".$mysqli->real_escape_string($maxPrice);
    }
    $backSql = $sql;
    if ($locationName && $locationName != '') {
        $sql = $sql." AND (p.name like '%".$mysqli->real_escape_string($locationName) . "%')";
    }
 
    $sql = $sql ." ORDER BY p.date_created DESC LIMIT $offset, 20";
    $res = $mysqli->query($sql);
    $total = $res->num_rows;
    $showNearBy = "false";
    $_SESSION['distance_page'] = 1;
    if ($total < 20) {
        $showNearBy = "true";
        if ($total > 0 ) {
            $newOffset = 20 - $total;
            $_SESSION['distance_page'] = 1;
            $fromset = 0;
        }
        else {
            $prevPage = 0;
            if ($_SESSION['distance_page']) {
                $prevPage = $_SESSION['distance_page'];
                $_SESSION['distance_page'] = $_SESSION['distance_page'] + 1;
                $newOffset = 20;
            }
            else{
                $_SESSION['distance_page'] = 1;
                $fromset = 0 ;
                $newOffset = 20;
            }

            $fromset = 20 * ($prevPage);
        }
        $pc = $mysqli->query("SELECT * FROM postcode WHERE area = '".$mysqli->real_escape_string($locationName)."' AND active = 1 LIMIT 1");
        $postcode = $pc->fetch_assoc();

        if (!$postcode) {

            $pcs = $mysqli->query("SELECT * FROM postcode WHERE area LIKE '%".$mysqli->real_escape_string($locationName)."%' AND active = 1");
            $allpcs = $pcs->fetch_assoc();

            $totalPcs = $pcs->num_rows;
            if ($totalPcs > 1) {
                $preferred = array('KUL','SGR');
                while($pc = $allpcs):
                    if ($in_array($pc['state_code'],$preferred)) {
                        $postcode = $pc;
                    }
                endwhile;
            } else {
                $postcode = $allpcs;
            }
            if (!$postcode) {
                $postcode = getCoordinates($locationName);
            }
        }

        if ($postcode) {
            $newSql = substr($backSql,7,strlen($backSql));
            $resDist = $mysqli->query("SELECT ( 6371 * acos( cos( radians(".$postcode['latitude'].") ) * cos( radians( p.latitude ) ) * cos( radians( p.longitude ) - radians(".$postcode['longitude'].") ) + sin( radians(".$postcode['latitude'].") ) * sin( radians( p.latitude ) ) ) ) AS distance, ".$newSql." AND (p.name not like '%".$mysqli->real_escape_string($locationName) . "%' OR p.address not like '%".$mysqli->real_escape_string($locationName) . "%')  ORDER BY distance ASC LIMIT $fromset,$newOffset");
        }
    }
    else {
        $_SESSION['distance_page'] = 0;
    }
    $page = 1;



  //          while ($p = $res->fetch_assoc()) :*/

?>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search result for <?php echo $locationName; ?></title>

    <!-- Material Design Icons -->
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Material Design Bootstrap -->
    <link href="css/mdb.css" rel="stylesheet">
    <!-- Main.css -->
    <link href="stylesheets/main.css" rel="stylesheet">
    <link href="css/jquery-ui.min.css" rel="stylesheet">
    <link href="css/jquery.steps.css" rel="stylesheet">
    <!-- fonts -->
    <link href='http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <!-- sidr -->
    <link rel="stylesheet" href="http://cdn.jsdelivr.net/jquery.sidr/2.2.1/stylesheets/jquery.sidr.light.min.css">
	
	<?php include('footer.php'); ?>
  
</head>

<body>
    <div class="listing-page">
        <!--Navigation-->
        <nav class="navbar navbar-fixed-top" role="navigation">
            <a id="simple-menu" class="simple-menu hidden-lg" href="#sidr"><i class="fa fa-bars"></i></a>
            <div id="sidr" class="sidr-mobile-menu">
                <div class="logo hidden-lg">
                    <a class="clearfix" class="logo-mobile-menu" href="index.php">
                        <img class="logo-mobile-menu-img" src="img/speedrent-logo.png" alt="Logo">
                    </a>
                    <a id="sidr-close" class="simple-menu hidden-lg" href="/"><i class="fa fa-arrow-left"></i></a>
                </div>
                <ul>
                    <li class="speedrent__footer-sub-title">
                        <a href="http://speedrent.com/web/about-us/">About Us</a>
                    </li>
                    <li class="speedrent__footer-sub-title">
                        <a href="http://speedrent.com/web/press-release/">Press</a>
                    </li>
                    <li class="speedrent__footer-sub-title">
                        <a href="http://speedrent.com/web/blog/">Blog</a>
                    </li>
                    <li class="speedrent__footer-sub-title">
                        <a href="http://speedrent.com/web/contact-us/">Contact Us</a>
                    </li>
                    <li class="speedrent__footer-sub-title">
                        <a href="speedrent.html">Speedrent</a>
                    </li>
                    <li class="speedrent__footer-sub-title">
                        <a href="speedsign.html">Speedsign</a>
                    </li>
                    <li class="speedrent__footer-sub-title">
                        <a href="http://speedrent.com/web/terms/">Terms</a>
                    </li>
                </ul>
                <div class="speedrent__social-box hidden-lg">
                    <a href="https://www.facebook.com/speedrent">
                        <img class="img-responsive" src="img/fb.png">
                    </a>
                    <a href="https://twitter.com/speedrentapp">
                        <img class="img-responsive" src="img/tw.png">
                    </a>
                </div>
            </div>
            <div class="logo">
                <a href="index.php">
                    <img class="img-responsive" src="img/logo-house-green.png" alt="Logo">
                </a>
            </div>
            <div class="search-box visible-lg">
                <div class="row">
                    <form class="col-md-10">
                        <div class="row">
                            <div class="input-field col-lg-10">
                                <i class="material-icons prefix search-icon">search</i>
                                <input id="icon_prefix" type="text" class="validate">
                                <label for="icon_prefix">Search</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- search-box -->
            <div id="sb-search" class="sb-search hidden-lg">
                <form>
                    <input class="sb-search-input" placeholder="Search" type="search" value="" name="search" id="search">
                    <input class="sb-search-submit" type="submit" value="">
                    <span class="sb-icon-search"><i class="material-icons prefix search-icon">search</i></span>
                    <span class="sb-icon-close"><i class="material-icons prefix close-icon">close</i></span>
                </form>
            </div>
            <a href="post.html" class="visible-lg pull-right btn btn-white waves-effect waves-light">Post a Property</a>
            <!-- <a href="post.html" class="hidden-lg pull-right btn btn-white waves-effect waves-light">Post</a> -->
        </nav><!--/.Navigation-->
        <div class="row reset-all-padding">
            <div class="col-md-12 col-lg-3 border-r-custom reset-padding-r">
                <section class="visible-lg">
                    <div class="logo visible-lg">
                        <a class="clearfix" href="home.html">
                            <img class="img-responsive" src="img/speedrent-logo.png" alt="Logo">
                        </a>
                    </div>
                    <div id="side-block" class="side-block equal-height border-r-custom border-top-bottom-custom clearfix">
                        <form class="clearfix fixed-form" action="listing.php" method="get" name="filters" id="filters">
                            <p class="side__filter-title">Housing Type</p>
                            <div class="checkbox-wrap top-margin-small">
                                <!-- <img class="high-rise" src="img/left-high-rise.png"> -->

                                <input type="checkbox" name="highrise" value="HIGHRISE" <?php echo (($highrise != '' || $landed == '') ? 'checked' : '');?> class="filled-in" id="high-rise-listing" onchange="onFilterChange()"/>

                                <label for="high-rise-listing">
                                    <span>High-Rise</span>
                                </label>
                                <div class="high-rise"></div>
                            </div><!-- checkbox-wrap -->
                            <div class="checkbox-wrap top-margin-small">
                                <!-- <img src="img/left-landed.png"> -->
								<input type="checkbox" id="landed-listing" name="landed" value="LANDED" class="filled-in" value="LANDED" <?php echo ($landed != '' ? 'checked' : '');?> onchange="onFilterChange()"/>
                                <label for="landed-listing">
                                    <span>Landed</span>
                                </label>
                                <div class="landed"></div>
                            </div><!-- checkbox-wrap -->
                            <p class="side__filter-title">Property Size</p>
                            <div class="filter-selects">
                                <div class="filter-selects-item-box">
                                    <a href="#" class="arr-left"></a>
                                    <div class="filter-select-item">
                                        <div class="select-item-text bedroom-icon">
                                            <span class="option"></span> Bedroom
                                            <select class="hidden" name="bedroom" onchange="onFilterChange()">
                                                 <option value="Any" <?php echo ($room == "" ? $selected : "");?>></option>
													<option value="1" <?php echo ($room == 1 ? $selected : "");?>></option>
													<option value="2" <?php echo ($room == 2 ? $selected : "");?>></option>
													<option value="3" <?php echo ($room == 3 ? $selected : "");?>></option>
													<option value="4" <?php echo ($room == 4 ? $selected : "");?>></option>
													<option value="5" <?php echo ($room == 5 ? $selected : "");?>></option>
													<option value="6" <?php echo ($room == 6 ? $selected : "");?>></option>
                                            </select>
                                        </div><!-- select-item-text -->
                                    </div><!-- filter-select-item -->
                                    <a href="#" class="arr-right"></a>
                                </div><!-- class="filter-selects-item-box -->
                                <div class="filter-selects-item-box">
                                    <a href="#" class="arr-left"></a>
                                    <div class="filter-select-item">
                                        <div class="select-item-text parking-icon">
                                            <span class="option"></span> Parking Space
                                            <select class="hidden" name="carpark" onchange="onFilterChange()">
                                               <option value="Any" <?php echo ($carpark == "" ? $selected : "");?>></option>
												<option value="1" <?php echo ($carpark == 1 ? $selected : "");?>></option>
												<option value="2" <?php echo ($carpark == 2 ? $selected : "");?>></option>
												<option value="3" <?php echo ($carpark == 3 ? $selected : "");?>></option>
                                            </select>
                                        </div><!-- select-item-text -->
                                    </div><!-- filter-selects-item-box -->
                                    <a href="#" class="arr-right"></a>
                                </div>
                                <div class="filter-selects-item-box">
                                    <a href="#" class="arr-left"></a>
                                    <div class="filter-select-item">
                                        <div class="select-item-text furnishing-icon">
                                            <span class="option">Basic Furnishing</span>
                                            <select class="hidden" name="furnishing" onchange="onFilterChange()">
                                                 <option value="Any" <?php echo ($furnishType == "" ? "selected" : "");?>></option>
												<option value="Basic Furnishing" <?php echo ($furnishType == "Basic Furnishing" ? "selected" : "");?>></option>
												<option value="Partial Furnishing" <?php echo ($furnishType == "Partial Furnishing" ? "selected" : "");?>></option>
												<option value="Fully Furnishing" <?php echo ($furnishType == "Fully Furnishing" ? "selected" : "");?>></option>
                                            </select>
                                        </div><!-- select-item-text -->
                                    </div><!-- filter-select-item -->
                                    <a href="#" class="arr-right"></a>
                                </div><!-- filter-selects-item-box -->
                                <p class="side__filter-title">Price Range</p>
                                <div class="filter-price-range">
                                    <div class="min-price">
                                        <div class="amount"></div>
                                    </div>
                                    <input type="hidden" id="maxPrice" name="maxPrice" value="<?php echo ($maxPrice  ? $maxPrice : 3500); ?>">
                                    <input type="hidden" id="minPrice" name="minPrice" value="<?php echo ($minPrice ? $minPrice : 600); ?>">
                                    <div class="max-price pull-right">
                                        <div class="amount"></div>
                                    </div>
                                    <div class="range-box">
                                        <div class="filter-price-range--ui"></div>
                                    </div>
                                </div>
                                <a class="btn reset-filters waves-effect waves-dark" onClick="resetFilter()">Reset Filters</a>
                            </div><!-- filter-selects -->
                        </form>
                    </div><!-- side-block -->
                </section>
            </div><!-- col-md-12 col-lg-3 -->
            <div class="col-md-12 col-lg-9 reset-padding-l">
                <section>
                    <div class="search-box visible-lg">
                        <div class="row">
                            <form class="col-md-10">
                                <div class="row">
                                    <div class="input-field col-lg-10">
                                        <i class="material-icons prefix search-icon">search</i>
                                        <input id="icon_prefix" type="text" class="validate">
                                        <label for="icon_prefix">Search</label>
                                    </div>
                                </div>
                            </form>
                            <a href="post.html" class="visible-lg pull-right btn btn-white waves-effect waves-light">Post a Property</a>
                        </div>
                    </div><!-- search-box -->
                </section>
                <section>
                    <div class="listing-content-block equal-height border-top-bottom-custom">
                        <!-- Button trigger modal -->
                        <a type="button" class="btn-floating btn-large filter hidden-lg waves-effect waves-light" data-toggle="modal" data-target="#modal-filter">
                            <i class="fa fa-filter"></i>
                        </a>
                        <!-- Modal -->
                       <h6 class="h5-responsive">Search Results for <span id="search-area"><?php if(isset($_REQUEST['areaName'])){echo $_REQUEST['areaName'];}else{echo "";}?></span></h6>
                        <div class="row">
                            <?php 
								$i = 0; 
								while ($p = $res->fetch_assoc()){
									$res2 = $mysqli->query("select pi.code from property_property_image as ppi join property_image as pi on (ppi.property_image_id = pi.id) where ppi.property_images_id = " . $mysqli->real_escape_string($p['id']) . " ORDER BY id ASC LIMIT 1");
									$img = $res2->fetch_assoc();
									$imgUrl = "http://prod.speedrent.com/images/property/".$p['ref']."/".$img['code']."-medium.jpg";
									//echo $p;
									//echo $img;
									
									$name = $p["name"];
									$price = 'RM'.round($p["price"], 0);
									$sqft = $p["sqft"];
									$hotdeal = '<span class="label hot-deal">Hot Deal</span>';
									
									if($p["discount"] != 0){
										$discount = '<span class="card-price-info small"> RM '.number_format($p['price']/2).' for the first month!</span>';	
									}else{
										$discount = "";
									}
									
									if($p["type"] == 'HIGHRISE'){
										$type = 'High-Rise';
									}else{
										$type = 'Landed';
									}
									$furnishType = $furnish[$p["furnish_type"]];
									
									$bedroom = $p["bedroom"];
									$bathroom = $p["bathroom"];
									$carpark = $p["carpark"];
									
									
									//$url = "/rent/<?php echo str_replace(" ","-",preg_replace("/[^A-Za-z0-9 ]/","-",$p['name']).'-for-rent-'.$p['ref'])";
									$url = "listing-single.php?name=".str_replace(" ","-",preg_replace("/[^A-Za-z0-9 ]/","-",$p['name']).'-for-rent-'.$p['ref'].'&code='.$p['ref']);
									
									echo '<div class="col-lg-5 col-md-12"><div class="card"><a href="'.$url.'" class="card-wrap"><div class="card-image"><div class="view overlay hm-white-slight">';
									echo '<img src="'.$imgUrl.'" class="img-responsive" alt=""></div>';
									echo '<div class="card-title">'.$hotdeal.'<h5>'.$name.'</h5></div></div><div class="card-content">';
									echo '<p><span class="card-price">'.$price.'</span>'.$discount.'</p>';
									echo '<ul class="card-price-description"><li><span>'.$sqft.' sqft &#10072;</span></li><li><span>'.$type.' &#10072;</span></li><li><span>'.$furnishType.'</span></li></ul>';
									echo '<ul class="card-price-description card-included-content"><li><span>'.$bedroom.'<img src="img/left-bedroom.png"></span></li><li><span>'.$bathroom.'<img src="img/bathroom.png"></span></li><li><span>'.$carpark.'<img src="img/left-parking-space.png"></span></li>';
									echo '</ul></div></a></div></div>';
									$i++;
								}
							  ?>
							
							
                        </div>
                        <!--<h6 class="h5-responsive">The following results are properties nearby <span id="search-area-nearby"><?php if(isset($_REQUEST['areaName'])){echo $_REQUEST['areaName'];}else{echo "";}?></span></h6>
                        <div class="row">
                           
						</div>-->
                    </div><!-- listing-content-block -->
                </section>
            </div>
        </div>
        <footer class="padding-block-medium listing-footer visible-lg">
            <div class="row">
                <div class="col-lg-3">
                    <h5 class="speedrent__footer-title">Speedrent</h5>
                    <p class="speedrent__footer-text">support@speedrent.com<br> +60 1234 5678</p>
                </div>
                <div class="col-lg-4">
                    <h5 class="speedrent__footer-title">Company</h5>
                    <ul class="clearfix">
                        <li class="speedrent__footer-sub-title">
                            <a href="http://speedrent.com/web/about-us/">About Us</a>
                        </li>
                        <li class="speedrent__footer-sub-title">
                            <a href="http://speedrent.com/web/press-release/">Press</a>
                        </li>
                        <li class="speedrent__footer-sub-title">
                            <a href="http://speedrent.com/web/blog/">Blog</a>
                        </li>
                        <li class="speedrent__footer-sub-title">
                            <a href="http://speedrent.com/web/contact-us/">Contact Us</a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-5">
                    <h5 class="speedrent__footer-title">Product</h5>
                    <ul class="clearfix">
                        <li class="speedrent__footer-sub-title">
                            <a href="speedrent.html">Speedrent</a>
                        </li>
                        <li class="speedrent__footer-sub-title">
                            <a href="speedsign.html">Speedsign</a>
                        </li>
                        <li class="speedrent__footer-sub-title">
                            <a href="http://speedrent.com/web/terms/">Terms</a>
                        </li>
                    </ul>
                </div>
                <div class="speedrent__social-box visible-lg">
                    <a href="https://www.facebook.com/speedrent">
                        <img class="img-responsive" src="img/fb.png">
                    </a>
                    <a href="https://twitter.com/speedrentapp">
                        <img class="img-responsive" src="img/tw.png">
                    </a>
                </div>
            </div>
            <div class="padding-block-sides-midium center-block">
                <span class="copyright">&copy; Speedrent 2016. All images are the property of their respective owners. &shy; &#124 &shy; &shy;<a class="speedrent__footer-sub-title" href="http://speedrent.com/web/terms/">Privacy Policy</a></span>
            </div>
        </footer>
    </div><!-- listing-page -->
    <!-- SCRIPTS -->

    <!-- JQuery -->
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/jquery.equalheights.min.js"></script>
    <script type="text/javascript" src="js/jquery.sticky-kit.min.js"></script>
    <script type="text/javascript" src="js/uisearch.js"></script>
    <script type="text/javascript" src="js/classie.js"></script>
    <script src="http://cdn.jsdelivr.net/jquery.sidr/2.2.1/jquery.sidr.min.js"></script>

    <script type="text/javascript" src="js/modernizr.custom.js"></script>

    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="js/bootstrap.min.js"></script>

    <!-- Material Design Bootstrap -->
    <script type="text/javascript" src="js/mdb.js"></script>
    <!-- jquery.easyWizard -->
    <script type="text/javascript" src="js/jquery.easyWizard.js"></script>
    <!-- jquery.steps -->
    <script type="text/javascript" src="js/jquery.steps.min.js"></script>
    <!-- main.js -->
    <script type="text/javascript" src="js/listing.js"></script>
	
	<script type="text/javascript">
  var hasLoadMore = false;
  var showNearBy = <?php echo $showNearBy;?>;
  function loadMore(last) {
    hasLoadMore = true;
    $.post({
        url: "/load/more",
        cache: true,
        data: $('#filters').serialize(),
        success: function(data) {
            if ($('.list-page__content-ajax').length == 0) {
                $("#content-box").append(data);
            }
            else {
                $("#content-box-ajax").append(data);
            }
            if ($('.list-page__content-ajax').length > 0) {
                showNearBy = true;
            }
            var page = $('#page').val();
            $.cookie('page', $("#page").val(), { path:'/'});
            $.cookie('hasLoadMore', "1", { path:'/'});
            $("#page").val(parseInt(page)+1);
            $(".preview-picture").click(function() {
                window.location = $(this).find("a").attr("href"); 
            });

            $.cookie('filters', $('#filters').serialize(), { path:'/'});
            if (last) {
                $('html, body, div').animate({
                    scrollTop: $("#"+$.cookie('ref')).offset().top
               }, 1000);
            }
            if ($('.list-page__content-ajax').length > 0 && showNearBy) {
                $('.list-page__content-ajax').insertAfter('.list-page__content');
                showNearBy = false;
            }


        },
        error: function() {
            alert("Couldnt get listing");
        }
    });
}

function goToUrl() {
    if (!hasLoadMore) {
        $.cookie('page', $("#page").val(), { path:'/'});
        $.removeCookie('hasLoadMore', { path:'/'});
        $.cookie('filters', $('#filters').serialize(), { path:'/'});
    }
    return true;
}

function populateForm() {

    var pageData = $.cookie('filters');
    $.each(pageData.split('&'), function (index, elem) {
        var vals = elem.split('=');
        $("[name='" + vals[0] + "']").val(decodeURIComponent(vals[1].replace(/\+/g, ' ')));
        if (vals[0] == "bedroom" || vals[0] == "carpark" || vals[0] == "furnishing") {
            $("[name='" + vals[0] + "']").change();
        }
        if (vals[0] == "landed" || vals[0] == "highrise") {
            $("[name='" + vals[0] + "']").prop('checked', true);
        }
        if (vals[0] == "highrise") {
            hasHighrise = true;
        }
        if (vals[0] == "minPrice") {
            $('.filter-price-range .min-price .amount').html(vals[1]);
        }
        if (vals[0] == "maxPrice") {
            $('.filter-price-range .max-price .amount').html(vals[1]);
        }

    });
    if (!hasHighrise) $("[name='highrise']").prop('checked', false);
    $("#page").val("1");
}

(function($) {
    $(function () {
			$(window).scroll(function() {
				if($(window).scrollTop() == $(document).height() - $(window).height()) {
					loadMore();
				}
			});


           $(".list-item").click(function() {
                goToUrl();
           });
           $(".preview-picture").click(function() {
                window.location = $(this).find("a").attr("href"); 
           });
           if ($.cookie('back') && $.cookie('hasLoadMore') && $.cookie('filters')) {
                var pageData = $.cookie('filters');
                var pageUntil = parseInt($.cookie('page'));
                var hasHighrise = false;
                populateForm();
                for (i=1;i<=pageUntil;i++) {

                    
                    if (i==pageUntil) {
                        loadMore(true);
                        $("#page").val(pageUntil);
                    }
                    else {
                        loadMore();
        
                    }
                    $("#page").val(i);

                }
           }
           else if ($.cookie('back')) {
                //populateForm();
                $('html, body, div').animate({
                    scrollTop: $("#"+$.cookie('ref')).offset().top
                }, 1000);
 
           }
           $.removeCookie('back',{ path:'/'});

      var openChat = function() {
             $('.left-side').addClass('open');
             $('.mobile-menu').addClass('mobile-modal-open');
         };

         var closeChat = function() {
             $('.left-side').removeClass('open');
             $('.mobile-menu').removeClass('mobile-modal-open');
         };

         if (location.hash == '#chat') {
             openChat();
         }

         $('.chat-mobile-button__link').click(function() {
             openChat();
             return false;
         });

         $('.mobile-close').click(function() {
             closeChat();
             return false;
         });

        if (location.hash == '#chat') {
          $('.left-side').addClass('open');
          $('.mobile-menu').addClass('mobile-modal-open');
        }
        $("#movingDate").datepicker({ 
            dateFormat: 'dd M yy'
        });


    })
})(jQuery);
  </script>
    

</body>

</html>