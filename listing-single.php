<!DOCTYPE html>
<html lang="en">
<?php
	date_default_timezone_set('Asia/Kuala_Lumpur');
    $locationName = trim($_GET['name']);
    $locationName = str_replace("-"," ",$locationName);
    $code = trim($_GET['code']);
    $code = explode("-", $code);
    $code = end($code);
    $furnish = array('FULL' => "Fully Furnished", 'PARTIAL'=>"Partial Furnished",'NONE'=>"Basic Furnished");

    $mysqli = new mysqli('localhost', 'root', 'noobie', 'speedrent');
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    if ($locationName && $locationName != '') {

    }
    $res = $mysqli->query("SELECT p.*,mu.fullname FROM property as p JOIN mobile_user as mu ON (mu.id = p.mobile_user_id) WHERE ref ='" . $mysqli->real_escape_string($code) . "'");
    $rowset = $res->fetch_assoc();
    $imageCode = ""; 
    $resImage = $mysqli->query("select pi.code from property_property_image as ppi join property_image as pi on (ppi.property_image_id = pi.id) where ppi.property_images_id = " . $mysqli->real_escape_string($rowset['id']) . " ORDER BY id ASC LIMIT 1");
    

    $res2 = $mysqli->query("select code,name from property_property_facility as ppf join property_facility as pf on (pf.id = ppf.property_facility_id) where ppf.property_facilities_id = ".$mysqli->real_escape_string($rowset['id']));
    $res3 = $mysqli->query("select code,name from property_property_furnish as ppf join property_furnish as pf on (pf.id = ppf.property_furnish_id) where ppf.property_furnishes_id = ".$mysqli->real_escape_string($rowset['id']));
         
		 
	$p = $rowset; 
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
	
	$address = $p["address"];
	
	$bedroom = $p["bedroom"];
	$bathroom = $p["bathroom"];
	$carpark = $p["carpark"];
	
	$description = $p["description"];
	$availability = $p["availability"];
	
	$imgCount = 0;
	while ($img = $resImage->fetch_assoc()){
		$images[$imgCount] = $img["code"];
		$imgCount++;
	}

?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $name;?></title>

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
</head>

<body>
    <div class="listing-page listing-page-single">
        <!--Navigation-->
        <nav class="navbar navbar-fixed-top" role="navigation">
            <a id="simple-menu" class="simple-menu hidden-lg" href="#sidr"><i class="fa fa-bars"></i></a>
            <div id="sidr" class="sidr-mobile-menu">
                <div class="logo hidden-lg">
                    <a class="clearfix" class="logo-mobile-menu" href="home.html">
                        <img class="img-responsive logo-mobile-menu-img" src="img/speedrent-logo.png" alt="Logo">
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
                <div class="speedrent__social-box hidden-lg pull-left">
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
            <div id="sb-search" class="sb-search hidden-lg">
                <form>
                    <input class="sb-search-input" placeholder="Search" type="search" value="" name="search" id="search">
                    <input class="sb-search-submit" type="submit" value="">
                    <span class="sb-icon-search"><i class="material-icons prefix search-icon">search</i></span>
                    <span class="sb-icon-close"><i class="material-icons prefix close-icon">close</i></span>
                </form>
            </div>
            <a href="post.php" class="visible-lg pull-right btn btn-white waves-effect waves-light">Post a Property</a>
        </nav><!--/.Navigation-->
        <div class="row reset-all-padding">
            <div class="col-md-12 col-lg-3 border-r-custom reset-padding-r">
                <section class="visible-lg">
                    <div class="logo visible-lg">
                        <a class="clearfix" href="index.php">
                            <img class="img-responsive" src="img/speedrent-logo.png" alt="Logo">
                        </a>
                    </div>
                    <div id="side-block" class="side-block equal-height border-r-custom border-top-bottom-custom clearfix">
                        <div class="row">
                            <form id="submitChatForm" action="https://prod.speedrent.com/web/chatRequest" method="post" class="col-md-12 clearfix fixed-form form-horizontal listing-single-side-form">
								<div class="input-field">
                                    <input placeholder="Alicia" id="fullname" name="fullname" type="text" class="validate">
                                    <label for="budget">What is your name?</label>
                                </div>
								
								<div class="input-field">
                                    <input placeholder="+60 1234 5678" id="tenantNumber" name="tenantNumber" type="text" class="validate">
                                    <label for="tenantNumber">We need your mobile number for the landlord to be able to contact you</label>
                                </div>
								
                                <p class="bold-text">To request a chat with the landlord, please give them a better understanding of your offer.</p>
                                <div class="input-field">
                                    <input placeholder="E.g. RM 1900" id="budget" name="budget" type="text" class="validate">
                                    <label for="budget">What is your budget for this property?</label>
                                </div>
                                <label for="nationality">Which nationality are you?</label>
                                <select class="form-control">
                                  <option value="Afganistan">Afghanistan</option>
								  <option value="Albania">Albania</option>
								  <option value="Algeria">Algeria</option>
								  <option value="American Samoa">American Samoa</option>
								  <option value="Andorra">Andorra</option>
								  <option value="Angola">Angola</option>
								  <option value="Anguilla">Anguilla</option>
								  <option value="Antigua &amp; Barbuda">Antigua &amp; Barbuda</option>
								  <option value="Argentina">Argentina</option>
								  <option value="Armenia">Armenia</option>
								  <option value="Aruba">Aruba</option>
								  <option value="Australia">Australia</option>
								  <option value="Austria">Austria</option>
								  <option value="Azerbaijan">Azerbaijan</option>
								  <option value="Bahamas">Bahamas</option>
								  <option value="Bahrain">Bahrain</option>
								  <option value="Bangladesh">Bangladesh</option>
								  <option value="Barbados">Barbados</option>
								  <option value="Belarus">Belarus</option>
								  <option value="Belgium">Belgium</option>
								  <option value="Belize">Belize</option>
								  <option value="Benin">Benin</option>
								  <option value="Bermuda">Bermuda</option>
								  <option value="Bhutan">Bhutan</option>
								  <option value="Bolivia">Bolivia</option>
								  <option value="Bonaire">Bonaire</option>
								  <option value="Bosnia &amp; Herzegovina">Bosnia &amp; Herzegovina</option>
								  <option value="Botswana">Botswana</option>
								  <option value="Brazil">Brazil</option>
								  <option value="British Indian Ocean Ter">British Indian Ocean Ter</option>
								  <option value="Brunei">Brunei</option>
								  <option value="Bulgaria">Bulgaria</option>
								  <option value="Burkina Faso">Burkina Faso</option>
								  <option value="Burundi">Burundi</option>
								  <option value="Cambodia">Cambodia</option>
								  <option value="Cameroon">Cameroon</option>
								  <option value="Canada">Canada</option>
								  <option value="Canary Islands">Canary Islands</option>
								  <option value="Cape Verde">Cape Verde</option>
								  <option value="Cayman Islands">Cayman Islands</option>
								  <option value="Central African Republic">Central African Republic</option>
								  <option value="Chad">Chad</option>
								  <option value="Channel Islands">Channel Islands</option>
								  <option value="Chile">Chile</option>
								  <option value="China">China</option>
								  <option value="Christmas Island">Christmas Island</option>
								  <option value="Cocos Island">Cocos Island</option>
								  <option value="Colombia">Colombia</option>
								  <option value="Comoros">Comoros</option>
								  <option value="Congo">Congo</option>
								  <option value="Cook Islands">Cook Islands</option>
								  <option value="Costa Rica">Costa Rica</option>
								  <option value="Cote DIvoire">Cote D'Ivoire</option>
								  <option value="Croatia">Croatia</option>
								  <option value="Cuba">Cuba</option>
								  <option value="Curaco">Curacao</option>
								  <option value="Cyprus">Cyprus</option>
								  <option value="Czech Republic">Czech Republic</option>
								  <option value="Denmark">Denmark</option>
								  <option value="Djibouti">Djibouti</option>
								  <option value="Dominica">Dominica</option>
								  <option value="Dominican Republic">Dominican Republic</option>
								  <option value="East Timor">East Timor</option>
								  <option value="Ecuador">Ecuador</option>
								  <option value="Egypt">Egypt</option>
								  <option value="El Salvador">El Salvador</option>
								  <option value="Equatorial Guinea">Equatorial Guinea</option>
								  <option value="Eritrea">Eritrea</option>
								  <option value="Estonia">Estonia</option>
								  <option value="Ethiopia">Ethiopia</option>
								  <option value="Falkland Islands">Falkland Islands</option>
								  <option value="Faroe Islands">Faroe Islands</option>
								  <option value="Fiji">Fiji</option>
								  <option value="Finland">Finland</option>
								  <option value="France">France</option>
								  <option value="French Guiana">French Guiana</option>
								  <option value="French Polynesia">French Polynesia</option>
								  <option value="French Southern Ter">French Southern Ter</option>
								  <option value="Gabon">Gabon</option>
								  <option value="Gambia">Gambia</option>
								  <option value="Georgia">Georgia</option>
								  <option value="Germany">Germany</option>
								  <option value="Ghana">Ghana</option>
								  <option value="Gibraltar">Gibraltar</option>
								  <option value="Great Britain">Great Britain</option>
								  <option value="Greece">Greece</option>
								  <option value="Greenland">Greenland</option>
								  <option value="Grenada">Grenada</option>
								  <option value="Guadeloupe">Guadeloupe</option>
								  <option value="Guam">Guam</option>
								  <option value="Guatemala">Guatemala</option>
								  <option value="Guinea">Guinea</option>
								  <option value="Guyana">Guyana</option>
								  <option value="Haiti">Haiti</option>
								  <option value="Hawaii">Hawaii</option>
								  <option value="Honduras">Honduras</option>
								  <option value="Hong Kong">Hong Kong</option>
								  <option value="Hungary">Hungary</option>
								  <option value="Iceland">Iceland</option>
								  <option value="India">India</option>
								  <option value="Indonesia">Indonesia</option>
								  <option value="Iran">Iran</option>
								  <option value="Iraq">Iraq</option>
								  <option value="Ireland">Ireland</option>
								  <option value="Isle of Man">Isle of Man</option>
								  <option value="Israel">Israel</option>
								  <option value="Italy">Italy</option>
								  <option value="Jamaica">Jamaica</option>
								  <option value="Japan">Japan</option>
								  <option value="Jordan">Jordan</option>
								  <option value="Kazakhstan">Kazakhstan</option>
								  <option value="Kenya">Kenya</option>
								  <option value="Kiribati">Kiribati</option>
								  <option value="Korea North">Korea North</option>
								  <option value="Korea Sout">Korea South</option>
								  <option value="Kuwait">Kuwait</option>
								  <option value="Kyrgyzstan">Kyrgyzstan</option>
								  <option value="Laos">Laos</option>
								  <option value="Latvia">Latvia</option>
								  <option value="Lebanon">Lebanon</option>
								  <option value="Lesotho">Lesotho</option>
								  <option value="Liberia">Liberia</option>
								  <option value="Libya">Libya</option>
								  <option value="Liechtenstein">Liechtenstein</option>
								  <option value="Lithuania">Lithuania</option>
								  <option value="Luxembourg">Luxembourg</option>
								  <option value="Macau">Macau</option>
								  <option value="Macedonia">Macedonia</option>
								  <option value="Madagascar">Madagascar</option>
								  <option value="Malaysia" selected>Malaysia</option>
								  <option value="Malawi">Malawi</option>
								  <option value="Maldives">Maldives</option>
								  <option value="Mali">Mali</option>
								  <option value="Malta">Malta</option>
								  <option value="Marshall Islands">Marshall Islands</option>
								  <option value="Martinique">Martinique</option>
								  <option value="Mauritania">Mauritania</option>
								  <option value="Mauritius">Mauritius</option>
								  <option value="Mayotte">Mayotte</option>
								  <option value="Mexico">Mexico</option>
								  <option value="Midway Islands">Midway Islands</option>
								  <option value="Moldova">Moldova</option>
								  <option value="Monaco">Monaco</option>
								  <option value="Mongolia">Mongolia</option>
								  <option value="Montserrat">Montserrat</option>
								  <option value="Morocco">Morocco</option>
								  <option value="Mozambique">Mozambique</option>
								  <option value="Myanmar">Myanmar</option>
								  <option value="Nambia">Nambia</option>
								  <option value="Nauru">Nauru</option>
								  <option value="Nepal">Nepal</option>
								  <option value="Netherland Antilles">Netherland Antilles</option>
								  <option value="Netherlands">Netherlands (Holland, Europe)</option>
								  <option value="Nevis">Nevis</option>
								  <option value="New Caledonia">New Caledonia</option>
								  <option value="New Zealand">New Zealand</option>
								  <option value="Nicaragua">Nicaragua</option>
								  <option value="Niger">Niger</option>
								  <option value="Nigeria">Nigeria</option>
								  <option value="Niue">Niue</option>
								  <option value="Norfolk Island">Norfolk Island</option>
								  <option value="Norway">Norway</option>
								  <option value="Oman">Oman</option>
								  <option value="Pakistan">Pakistan</option>
								  <option value="Palau Island">Palau Island</option>
								  <option value="Palestine">Palestine</option>
								  <option value="Panama">Panama</option>
								  <option value="Papua New Guinea">Papua New Guinea</option>
								  <option value="Paraguay">Paraguay</option>
								  <option value="Peru">Peru</option>
								  <option value="Phillipines">Philippines</option>
								  <option value="Pitcairn Island">Pitcairn Island</option>
								  <option value="Poland">Poland</option>
								  <option value="Portugal">Portugal</option>
								  <option value="Puerto Rico">Puerto Rico</option>
								  <option value="Qatar">Qatar</option>
								  <option value="Republic of Montenegro">Republic of Montenegro</option>
								  <option value="Republic of Serbia">Republic of Serbia</option>
								  <option value="Reunion">Reunion</option>
								  <option value="Romania">Romania</option>
								  <option value="Russia">Russia</option>
								  <option value="Rwanda">Rwanda</option>
								  <option value="St Barthelemy">St Barthelemy</option>
								  <option value="St Eustatius">St Eustatius</option>
								  <option value="St Helena">St Helena</option>
								  <option value="St Kitts-Nevis">St Kitts-Nevis</option>
								  <option value="St Lucia">St Lucia</option>
								  <option value="St Maarten">St Maarten</option>
								  <option value="St Pierre &amp; Miquelon">St Pierre &amp; Miquelon</option>
								  <option value="St Vincent &amp; Grenadines">St Vincent &amp; Grenadines</option>
								  <option value="Saipan">Saipan</option>
								  <option value="Samoa">Samoa</option>
								  <option value="Samoa American">Samoa American</option>
								  <option value="San Marino">San Marino</option>
								  <option value="Sao Tome &amp; Principe">Sao Tome &amp; Principe</option>
								  <option value="Saudi Arabia">Saudi Arabia</option>
								  <option value="Senegal">Senegal</option>
								  <option value="Serbia">Serbia</option>
								  <option value="Seychelles">Seychelles</option>
								  <option value="Sierra Leone">Sierra Leone</option>
								  <option value="Singapore">Singapore</option>
								  <option value="Slovakia">Slovakia</option>
								  <option value="Slovenia">Slovenia</option>
								  <option value="Solomon Islands">Solomon Islands</option>
								  <option value="Somalia">Somalia</option>
								  <option value="South Africa">South Africa</option>
								  <option value="Spain">Spain</option>
								  <option value="Sri Lanka">Sri Lanka</option>
								  <option value="Sudan">Sudan</option>
								  <option value="Suriname">Suriname</option>
								  <option value="Swaziland">Swaziland</option>
								  <option value="Sweden">Sweden</option>
								  <option value="Switzerland">Switzerland</option>
								  <option value="Syria">Syria</option>
								  <option value="Tahiti">Tahiti</option>
								  <option value="Taiwan">Taiwan</option>
								  <option value="Tajikistan">Tajikistan</option>
								  <option value="Tanzania">Tanzania</option>
								  <option value="Thailand">Thailand</option>
								  <option value="Togo">Togo</option>
								  <option value="Tokelau">Tokelau</option>
								  <option value="Tonga">Tonga</option>
								  <option value="Trinidad &amp; Tobago">Trinidad &amp; Tobago</option>
								  <option value="Tunisia">Tunisia</option>
								  <option value="Turkey">Turkey</option>
								  <option value="Turkmenistan">Turkmenistan</option>
								  <option value="Turks &amp; Caicos Is">Turks &amp; Caicos Is</option>
								  <option value="Tuvalu">Tuvalu</option>
								  <option value="Uganda">Uganda</option>
								  <option value="Ukraine">Ukraine</option>
								  <option value="United Arab Erimates">United Arab Emirates</option>
								  <option value="United Kingdom">United Kingdom</option>
								  <option value="United States of America">United States of America</option>
								  <option value="Uraguay">Uruguay</option>
								  <option value="Uzbekistan">Uzbekistan</option>
								  <option value="Vanuatu">Vanuatu</option>
								  <option value="Vatican City State">Vatican City State</option>
								  <option value="Venezuela">Venezuela</option>
								  <option value="Vietnam">Vietnam</option>
								  <option value="Virgin Islands (Brit)">Virgin Islands (Brit)</option>
								  <option value="Virgin Islands (USA)">Virgin Islands (USA)</option>
								  <option value="Wake Island">Wake Island</option>
								  <option value="Wallis &amp; Futana Is">Wallis &amp; Futana Is</option>
								  <option value="Yemen">Yemen</option>
								  <option value="Zaire">Zaire</option>
								  <option value="Zambia">Zambia</option>
								  <option value="Zimbabwe">Zimbabwe</option>		
                                </select>
                                <div class="input-field">
                                    <input placeholder="E.g. Software Developer / Student" id="occupation" name="occupation" type="text" class="validate">
                                    <label for="occupation">What is your occupation?</label>
                                </div>
                                <!--<div class="input-field">
                                    <input placeholder="Speedrent" id="company" name="" type="text" class="validate">
                                    <label for="company">Which Company / University?</label>
                                </div>-->
								<div class="input-field">
                                    <input placeholder="E.g. Family / Friends" id="relationship" name="relationship" type="text" class="validate">
                                    <label for="relationship">What is your relationship with other tenants?</label>
                                </div>
								
                                <div class="input-field col-md-7 reset-padding-l">
                                    <input placeholder="31 Jan 2015" id="date" name="movingDate" type="text" class="validate">
                                    <label for="date">Move in Date</label>
                                </div>
                                <div class="input-field col-md-5 reset-all-padding">
                                    <!-- <p>No. of Tenants</p> -->
                                    <label for="tenants" class="tenant-label">No. of Tenants</label>
                                    <select id = "amountPerson" name = "amountPerson" class="form-control tenant-select col-md-5">
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                        <option>4</option>
                                        <option>5</option>
										<option>6</option>
                                        <option>7</option>
                                        <option>8</option>
                                        <option>9</option>
                                    </select>
                                </div>

                                <div class="choice-offer">
                                    <input type="checkbox" class="filled-in" id="serious">
                                    <label for="serious">
                                        <span>Are you serious about this offer?</span>
                                    </label>
                                </div>
                               
                                <div class="col-md-7 reset-padding-l">
                                    <label for="verification">Mobile Verification</label>
                                    <input placeholder="- - - -" id="verification" type="text" class="validate">
                                </div>
                                <div class="col-md-5 reset-padding-l">
                                    <a class="btn btn-yellow waves-effect waves-light pull-right submit" onclick="requestPin()" href = "javascript:void(0)">Request Code</a>
                                </div>
								<a class="btn btn-yellow full-width waves-effect waves-light" onclick="submitChat()">Chat with Landlord</a>
                            </form>
                        </div>
                    </div><!-- side-block -->
                </section>
            </div><!-- col-md-12 col-lg-3 -->
            <div class="col-lg-9 col-md-12 reset-padding-l">
                <section>
                    <div class="search-box search-box-list-single visible-lg">
                        <a class="back-btn" href="javascript:void(0)" onclick="goBack()">
                            < Back to Listings </h6>
                        </a>
                        <a href="post.php" class="visible-lg pull-right btn btn-white waves-effect waves-light">Post a Property</a>
                    </div><!-- search-box -->
                </section>
                <section>
                    <div class="listing-content-block equal-height border-top-bottom-custom">
                        <div class="search-box-list-single hidden-lg">
                            <a class="back-btn" href="javascript:void(0)" onclick="goBack()">
                                < Back to Listings </h6>
                            </a>
                        </div>
                        <div class="row">
                            <div class="col-lg-9 col-md-12">
                                <!--Image Card-->
                                <div class="card">
                                    <div class="card-image card-image-single">
                                        <!-- <div class="view overlay hm-white-slight">
                                            <img src="img/list-prevew-picture.png" class="img-responsive" alt="">
                                            <a href="#">
                                                <div class="mask waves-effect"></div>
                                            </a>
                                        </div> -->
                                        <!-- Carousel -->
                                        <div id="carousel1" class="carousel slide carousel-fade">
                                            <!-- Indicators -->
                                            <ol class="carousel-indicators">
												<?php
													for($i = 0; $i < $imgCount; $i++){
														if($i == 0){
															echo '<li data-target="#carousel1" data-slide-to="'.$i.'" class="active"></li>';
														}else{
															echo '<li data-target="#carousel1" data-slide-to="'.$i.'"></li>';
														}
													}
												
												?>
                                            </ol>

                                            <!-- Wrapper for slides -->
                                            <div class="carousel-inner" role="listbox">
											
												<?php
													for($i = 0; $i < $imgCount; $i++){
														$url = "http://prod.speedrent.com/images/property/".$p['ref']."/".$images[$i]."-medium.jpg";
														if($i == 0){
															echo '<div class="item active">';
														}else{
															echo '<div class="item">';
														}
														echo '<div class="item active">
																<div class="view overlay hm-blue-slight">
																	<a>
																		<img src="'.$url.'" class="img-responsive"alt="slide'.($i+1).'">
																		<div class="mask waves-effect waves-light"></div>
																	</a>
																	<div class="carousel-caption">
																		<div class="card-title animated fadeInDown">
																			'.$hotdeal.'
																			<h5>'.$name.'</h5>
																		</div>
																	</div>
																</div>
															</div>';
													}
													//<img src="'.$url.'" class="img-responsive" alt="slide1">
												
												?>

                                            </div>
                                            <!-- /.carousel-inner -->

                                            <!-- Controls -->
                                            <a class="left carousel-control new-control" href="#carousel1" role="button" data-slide="prev">
                                                <span class="fa fa fa-angle-left waves-effect waves-light"></span>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                            <a class="right carousel-control new-control" href="#carousel1" role="button" data-slide="next">
                                                <span class="fa fa fa-angle-right waves-effect waves-light"></span>
                                                <span class="sr-only">Previous</span>
                                            </a>

                                        </div>
                                        <!-- /.carousel -->

                                    </div><!-- card-image -->
                                    <div class="card-content">
                                        <p>
                                            <span class="card-price"><?php echo $price;?></span>
                                            <span class="card-price-info small">RM 675 for the first month!</span>
                                        </p>
                                        <ul class="card-price-description">
                                            <li>
                                                <span><?php echo $sqft; ?> sqft &#10072;</span>
                                            </li>
                                            <li>
                                                <span><?php echo $type; ?> &#10072;</span>
                                            </li>
                                            <li>
                                                <span><?php echo $furnishType; ?></span>
                                            </li>
                                        </ul><!-- card-price-description -->
                                        <ul class="card-price-description card-included-content">
                                            <li>
                                                <span><img src="img/left-bedroom.png"><?php echo $bedroom;?> Bedroom</span>
                                            </li>
											<li>
                                                <span><img src="img/left-bathroom-space.png"><?php echo $bathroom;?> Bathroom</span>
                                            </li>
                                            <li>
                                                <span><img src="img/left-parking-space.png"><?php echo $carpark;?> Parking Space</span>
                                            </li>
                                            <li>
                                                <span><img src="img/left-furnishing.png">Partial Furnishing</span>
                                            </li>
                                        </ul><!-- card-price-description -->
                                    </div><!-- card-content -->
                                </div>
                                <!--Image Card-->
                                <div class="card-details">
                                    <h6>Address & Key Information</h6>
                                    <p><?php echo $address; ?></p>
                                    <p><?php echo $sqft;?> sqft &#124 <?php echo $type;?> &#124 <?php echo $furnishType;?></p>
                                    <div class="divider"></div>
                                    <h6>Availability</h6>
                                    <p><?php echo $availability;?></p>
                                    <div class="divider"></div>
                                    <h6>Description</h6>
                                    <p><?php echo $description;?></p>
                                    <div class="furnishing-block">
                                        <h6>Furnishing</h6>
                                        <div class="row">
                                            <form action="#" class="clearfix">
												<?php 
														$i = 0;
														 while ($row = $res2->fetch_assoc()) {
															if ($i % 3 == 0){ 
																echo  "<div class='col-lg-4 col-md-4 col-xs-6 reset-padding-l'><ul>"; 
															}
															
															$id = "furnishing-".strval($i);
															$code = $row["code"];
															$name = $row["name"];
															echo "<li><input name='furnishes[]'  onclick='return false' type='checkbox' class='filled-in' id='$id' checked='checked' value='$code'/><label for='$id'>$name</label></li>";

															if ($i % 3 == 2){
																echo "</ul></div>";
															}
															$i++;
														}
														$i--;
														if($i % 3 != 2){
															echo "</ul></div>";
														}													
													?>
                                            </form>
                                        </div>
                                    </div><!-- furnishing-block -->
                                    <div class="furnishing-block">
                                        <h6>Facilities</h6>
                                        <div class="row">
                                            <form action="#" class="clearfix form-horisontal">
													<?php 
														$i = 0;
														 while ($row = $res3->fetch_assoc()) {
															if ($i % 3 == 0){ 
																echo  "<div class='col-lg-4 col-md-4 col-xs-6 reset-padding-l'><ul>"; 
															}
															
															$id = "facilities-".strval($i);
															$code = $row["code"];
															$name = $row["name"];
															echo "<li><input name='facilities[]'  onclick='return false' type='checkbox' class='filled-in' id='$id' checked='checked' value='$code'/><label for='$id'>$name</label></li>";

															if ($i % 3 == 2){
																echo "</ul></div>";
															}
															$i++;
														}
														$i--;
														if($i % 3 != 2){
															echo "</ul></div>";
														}													
													?>
											</form>
                                        </div>
                                    </div><!-- furnishing-block -->
                                </div><!-- card-details -->
                                <div class="map">
                                    <h5>See on Map</h5>
                                    <iframe src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDmquiY0MlSJH6uka9vbpSALHCaOP2QjHc&q=<?php echo $rowset['latitude'].",",$rowset['longitude'];?>&zoom=18" width="100%" height="400px" frameborder="0" style="border:0" allowfullscreen></iframe>
                                    <a href="/" class="report">Report this Listing</a>
                                </div><!-- map -->

                            </div>
                        </div>
                        <!-- Button trigger modal -->
                        <a type="button" class="btn-floating btn-large chat-btn filter hidden-lg waves-effect waves-light" data-toggle="modal" data-target="#modal-filter">
                            <img src="img/chat-mobile-icon.png">
                        </a>
                        
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
    <script type="text/javascript" src="js/listing-single.js"></script>


</body>

</html>
