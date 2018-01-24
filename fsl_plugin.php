<?php
    /*
    Plugin Name: Find State Legislators
    Description: Find state legislators by ZIP code 
    Version: 1.1
    Author: Glantz Design
    Author URI: https://glantz.net
    License: GPL2
    */

include('fsl_settings.php');

//Setup shortcode
function load_page() { 
    //load page stylesheet when using shortcode.
    wp_enqueue_style( 'fsl_page_style' ); 
    // wp_enqueue_script( 'fsl_page_script' ); 
    wp_enqueue_script('fsl_script');
    ?>
   <div class="fsl-container">
        <form id="zipcode" method="post" action="">
        <label for="user-zipcode">Enter a ZIP Code</label>
        <input id="user-zipcode" type="text" name="user-zipcode">
        <input type="submit">
        </form>
        <ul class="search-result"><?php getLegislator(getZip()); ?></ul>
    </div>

    <?php  
}
add_shortcode( 'fsl', 'load_page' );

//Cache control
header("Cache-Control: private, max-age=10800, pre-check=10800");
header("Pragma: private");
header("Expires: " . date(DATE_RFC822,strtotime("+2 day")));

//Get zipcode from form
function getZip(){
    if(isset($_POST["user-zipcode"])){
        $user_zip = $_POST["user-zipcode"];
        return $user_zip;
    }
}

//Get legislator function
function getLegislator($zip){ 
    //Get APIs.
    $api_google = get_option('google_geo_api');
    $api_openstates = get_option('openstates_api');

    if($zip){
        //Get lat and long by zip
        $location_json = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . $zip . '&key=' . $api_google);
        $location_obj = json_decode($location_json);
        if( $location_obj->status == 'ZERO_RESULTS' ){
            //If zip code does not return any lat and lng info:
            echo '<div class="error">The ZIP code you entered <strong>"' . $zip . '"</strong> is not a valid ZIP code. Please try again. </div>';
        }else{
            //If zip code returns lat and lng info:
            $location_lat = $location_obj->results[0]->geometry->location->lat;
            $location_lng = $location_obj->results[0]->geometry->location->lng;
            $lat_lng_url = 'lat=' . $location_lat . '&long=' . $location_lng;

            //Get legislator
            $legislator_json = file_get_contents('https://openstates.org/api/v1/legislators/geo/?' . $lat_lng_url . '&apikey=' . $api_openstates);
            $legislators = json_decode($legislator_json);

            if(empty($legislators)){
                //If zip code returns legislators info:
                echo '<div class="error">The ZIP code you entered <strong>"' . $zip . '"</strong> is not a valid US ZIP code. Please try again. </div>';
            }else{
                //If zip code returns legislator info:
                echo '<div class="current-zip">The ZIP code you entered is: <strong>' . $zip . '</strong></div>';
                
                //Function to out put one piece of information with label and value.
                function getInfo($label, $value){
                    if($value != ""){
                        $class = strtolower( str_replace(':', '', $label) );
                        //for emails
                        if(strpos($value, '@')){
                            echo '<p class="' . $class .  '"><strong>' . $label . ' </strong><a href="mailto:' . $value . '">' . $value . '</a></p>';
                        }else if($class == 'phone'){
                            echo '<p class="' . $class .  '"><strong>' . $label . ' </strong><a href="tel:' . $value . '">' . $value . '</a></p>';
                        }else{
                            echo '<p class="' . $class .  '"><strong>' . $label . ' </strong>' . $value . '</p>';
                        }
                    }
                }
                
                //Loop out all information
                foreach($legislators as $legislator){
                    $full_name = $legislator->full_name;
                    $photo = $legislator->photo_url;
                    if (strpos($photo, 'http://') !== false) {
                        $photoClass = " image-stay-http";
                    }else{
                        $photoClass = "";
                    }
                    if($legislator->chamber == "lower"){
                        $chamber = "House";
                    }elseif($legislator->chamber == "upper"){
                        $chamber = "Senate";
                    }
                    $party = $legislator->party;
                    $district = $legislator->district;
                    $state = $legislator->state;
                    $term = $legislator->roles[0]->term;
                    $email = $legislator->email;

                    $offices = $legislator->offices;

                    if(count($offices)>0){
                        $office1_type = $legislator->offices[0]->type;
                        $office1_address = $legislator->offices[0]->address;
                        $office1_phone = $legislator->offices[0]->phone;
                        $office1_fax = $legislator->offices[0]->fax;
                        $office1_email = $legislator->offices[0]->email;
                    }

                    if(count($offices)>1){
                        $office2_type = $legislator->offices[1]->type;
                        $office2_address = $legislator->offices[1]->address;
                        $office2_phone = $legislator->offices[1]->phone;
                        $office2_fax = $legislator->offices[1]->fax;
                        $office2_email = $legislator->offices[1]->email;
                    }
                    
                    $url = $legislator->url;
                    if (strpos($url, 'http://') !== false) {
                        $urlClass = " url-stay-http";
                    }else{
                        $photoClass = "";
                    }

                    ?>
                    <div class="legislator">
                        <div class="photo-container">
                            <img class="<?php echo $photoClass; ?>" src="<?php echo $photo; ?>">
                        
                        </div>
                        <div class="info-container">
                            <div class="basic-info">
                                <div class="col-left">
                                    <?php getInfo('Name:', $full_name); ?>
                                    <?php getInfo('Party:', $party); ?>
                                    <?php getInfo('District:', $district); ?>
                                    <?php 
                                    if(count($offices)>1){
                                        if($email != $office1_email & $email != $office2_email){
                                            getInfo('Email:', $email); 
                                        }
                                    }else{
                                        if($email != $office1_email){
                                            getInfo('Email:', $email); 
                                        }
                                    }
                                    ?>
                                </div> 
                                <div class="col-right">
                                    <?php getInfo('State:', $state); ?>
                                    <?php getInfo('Chamber:', $chamber); ?>
                                    <?php getInfo('Term:', $term); ?>
                                </div> 
                            </div>
                            <div class="office-info">
                                <div class="separator"><hr></div>
                                <div class="col-left">
                                    <h4 class="office"><?php echo $office1_type ?> Office:</h4>
                                    <?php getInfo('Address:', $office1_address); ?>
                                    <?php getInfo('Phone:', $office1_phone); ?>
                                    <?php getInfo('Fax:', $office1_fax); ?>
                                    <?php getInfo('Email:', $office1_email); ?>
                                </div>
                                <?php if(count($offices)>1): ?>
                                <div class="col-right">
                                    <h4 class="office"><?php echo $office2_type ?> Office:</h4>
                                    <?php getInfo('Address:', $office2_address); ?>
                                    <?php getInfo('Phone:', $office2_phone); ?>
                                    <?php getInfo('Fax:', $office2_fax); ?>
                                    <?php getInfo('Email:', $office2_email); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <a class="learn-more <?php echo $urlClass; ?>" href="<?php echo $url ?>" target="_blank">Learn More</a>
                        </div>
                    </div>
                <?php 
                }//End foreach loop
            }//End checking legislator empty
        }//End checking zip code validation
    }else{
        echo '<div class="error">Please enter your zipcode.</div>';
    }//End check zip code empty
}//End get legislator function


//Enqueue stylesheet for plugin admin
function fsl_admin_styles() {
    wp_enqueue_style( 'fsl_admin_style', plugins_url( 'style.css', __FILE__ ) );
}
add_action('admin_print_styles', 'fsl_admin_styles');

//Register stylesheet for page. Enqueue in shortcode function
function fsl_page_styles() {
    wp_register_style( 'fsl_page_style', plugins_url( '/css/main.css', __FILE__ ) );
    wp_register_script( 'fsl_page_script', plugins_url( 'app.js', __FILE__ ), array ('jquery', 'jquery-ui'), false, false);
}
add_action('wp_enqueue_scripts', 'fsl_page_styles');

//Register script for page. Enqueue in shortcode function

function fsl_page_scripts() {
    wp_register_script('fsl_script', plugins_url('app.js', __FILE__), array('jquery'),'1.1', true);
}  
add_action( 'wp_enqueue_scripts', 'fsl_page_scripts' );  





