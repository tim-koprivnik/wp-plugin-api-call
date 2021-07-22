<?php

// Getting API data
function getApiData() {

    // 1. *** Get Client ID & Secret ***
    $url1 = 'https://apidev.citiesapps.com/clients';

    $args1 = array(
        'method' => 'POST',
        'headers' => array(
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 11.2; rv:86.0) Gecko/20100101 Firefox/86.0',
            'X-Requesting-App' => 'cities',
        )
    );

    $response1 = wp_remote_get( $url1, $args1 );

    if ($response1) {
        
        $body1 = wp_remote_retrieve_body( $response1 );
        $results1 = json_decode( $body1 ); // "json_decode" stores JSON data in a PHP variable, and then decode it into a PHP object // "json_encode" encode an associative array into a JSON object
        
        echo '</br>';
        echo '<hr>';
        $clientID = $results1->_id;
        echo '<strong>Client ID: </strong>' . $clientID;
        echo '</br>';
        $clientSecret = $results1->client_secret;
        echo '<strong>Client Secret: </strong>' . $clientSecret;
        echo '<hr>';

    }


    // 2. *** Get final content from API ***
    // Get things from DB
    $options = get_option( 'ca_plugin_settings' );
    // print_r($options);

    $entity_id = $options['entity_id']; // testing entity id: 225d248a69e5fe1f0010f53f35
    $type = $options['content_type'];

    echo "<strong>Entity ID:</strong> " . $entity_id;
    echo '</br>';
    echo "<strong>Type of content:</strong> " . $type;

    // Starting URL
    $url2 = 'https://apidev.citiesapps.com/'.$type.'?filter=%7B%22entityid%22:%7B%22$in%22:%5B%22'.$entity_id.'%22%5D%7D%7D&sort=%7B%22start_time%22:%22desc%22%7D&limit=15&offset=0';

    echo '<hr>';
    echo '<strong>URL:</strong> ' . $url2;

    // Getting offset query parameter (not needed, though)
    $url2_parts = parse_url($url2);
    parse_str($url2_parts['query'], $query);

    $query_offset = (int) $query['offset'];

    echo '</br>';
    echo '<strong>Offset:</strong> ' . $query_offset;
    echo '</br>';

    // New URL (i.e. with dynamic offset query parameter)
    $url2 = 'https://apidev.citiesapps.com/'.$type.'?filter=%7B%22entityid%22:%7B%22$in%22:%5B%22'.$entity_id.'%22%5D%7D%7D&sort=%7B%22start_time%22:%22desc%22%7D&limit=15&offset='.$query_offset;
    echo '<strong>URL with dynamic offset:</strong> ' . $url2;

    $url2_test = 'https://jsonplaceholder.typicode.com/todos/1';
    
    $args2 = array(
        'method' => 'GET',
        // 'timeout' => 100,
        'headers' => array(
            // 'Authorization' => 'Basic NWZmNzIwMWE5ZjZmODIwMDExYWM2ZmNmOmEzOTMxZTIzZTYwMTc0ZDBhYzFhNWZlMDNjNmY3NTQ1MDE3MjkyMWExOGI2NzgwOGM3MGYxNGViMWJmZQ==', 
            'Authorization' => 'Basic ' . base64_encode( $clientID . ':' . $clientSecret ),
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.141 Safari/537.36',
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'X-Requested-With',
            'X-Requesting-App' => 'cities', 
            'Connection' => 'keep-alive',
        )
    );

    $response2 = wp_remote_get( $url2, $args2 );
    
    // Testing
    /*
    echo "<pre>";
    print_r($response2);
    echo "</pre>";
    */

    // When API call is unsuccessful
    if ( is_wp_error( $response2 ) ) {

        $error_message = $response2->get_error_message();
        echo "Something went wrong: $error_message";

    }

    // When API call is successful
    if ($response2) {
        
        $body2 = wp_remote_retrieve_body( $response2 );
        $results2 = json_decode( $body2 );              // "json_decode" stores JSON data in a PHP variable, and then decode it into a PHP object // "json_encode" encodes an associative array into a JSON object
        // $results_arr2 = json_decode( $body2, true ); // ... decode it into a PHP array

        // Document Count
        /*
        $results2_count = $results2->pagination->documents_count;
        echo '</br>';
        echo '<strong>Results Count: </strong>' . $results2_count;
        echo '<hr>';
        */

        // Calling function that writes body of API to file 'data.json'
        $file_link = CAPLUGIN_DIR . 'includes/data.json';
        ca_write_to_file($body2, $file_link);

        // Calling function that converts JSON data ('data.json') to array & inserts data to CPT cities
        ca_json_to_array_and_insert_to_cpt();

    }

}


// Writing body of API to file 'data.json'
function ca_write_to_file($data, $file_link) {
    if (file_exists($file_link)) {
        $file = fopen($file_link, 'w'); // 'a'
        fwrite($file, $data . "\n");
    } else {
        $file = fopen($file_link, 'w');
        fwrite($file, $data . "\n");
    }
    fclose($file);
}


// Converting JSON data ('data.json') to array & Inserting data to CPT cities
function ca_json_to_array_and_insert_to_cpt() {

    $file_link = CAPLUGIN_DIR . 'includes/data.json';
    $json_to_array = json_decode(file_get_contents($file_link), true);

    // Testing 
    /*
    echo "<pre>";
    print_r($json_to_array);
    echo "</pre>";
    */

    echo '</br>';
    if ($json_to_array) { 

        foreach ($json_to_array as $result) {

            foreach ($result as $res) {
                $title = isset($res['title']) ? $res['title'] : '';
                $text = isset($res['text']['ops'][0]['insert']) ? $res['text']['ops'][0]['insert'] : '';
                $date = isset($res['_created_at']) ? $res['_created_at'] : '';
                $author = isset($res['author']['name']) ? $res['author']['name'] : '';
                // $image_src = isset($res['images'][0]['url']) ? $res['images'][0]['url'] : null;
                $image_src = isset($res['images'][0]['url']) ? $res['images'][0]['url'] : 'https://i.stack.imgur.com/y9DpT.jpg';
                $image_filename = isset($res['images'][0]['filename']) ? $res['images'][0]['filename'] : 'cities-placeholder-image-name' ;

                // $image_src = $res['images'][0]['url'];
                // $image_filename = $res['images'][0]['filename'];
                
                // Test: Printing/Echoing all needed data
                echo '</br>';
                echo $title;
                echo '</br>';
                echo $text;
                echo '</br>';
                echo formatDate($date);
                echo '<br>';
                echo $author;
                echo '<br>';
                echo $image_filename;
                echo '<br>';
                echo '<img src="'.$image_src.'" alt="'.$image_filename.'" width="500" height="250">';
                echo '<hr>';
                // print_r(get_page_by_title( $title )); // Featured image already in post. ???


                // If there is no post with that title (as in API), add post to CPT
                if( !get_page_by_title( $title, OBJECT, 'cities' ) ) {

                    // Create post object
                    $ca_post = array(
                        'post_title'        =>  $title, // wp_strip_all_tags( $title )
                        'post_content'      =>  $text,
                        'post_name'		    =>  sanitize_title( $title ), // Sanitizes a string into a slug
                        'post_author'       =>  1,
                        // 'post_category' => array( 8,39 ),
                        'comment_status'	=>	'closed',
                        'ping_status'		=>	'closed',
                        'post_status'       =>  'publish',
                        'post_type'		    =>  'cities', // post
                        // 'meta_input' => array(
                        //     'some_meta_key_...' => $results2->news,
                        // )
                    );
                    
                    // Insert the post into the database
                    $post_id = wp_insert_post( $ca_post );
                    // print_r($post_id); // Test: Prints post ID for every inserted post

                    // Add Featured Image to Post (if there is yet not one in post)
                    require_once(ABSPATH . 'wp-admin/includes/post.php');
                    // if( post_exists( $post_id ) === null ) {
                    if( !has_post_thumbnail( $post_id ) ) { // Determines whether a post has an image attached.

                        $image_url        = $image_src;
                        // $image_name       = sanitize_title( $title );
                        $image_name       = $image_filename;
                        $upload_dir       = wp_upload_dir(); // Set upload folder
                        $image_data       = file_get_contents($image_url); // Get image data
                        // $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
                        // $filename         = basename( $unique_file_name ); // Create image file name
                        $filename         = basename( $image_name );
                        // $filename         = $image_filename;


                        // global $wpdb;
                        // print_r(intval( $wpdb->get_var( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value LIKE 'cities-placeholder-image-name'" ) ));


                        // Check folder permission and define file location
                        if( wp_mkdir_p( $upload_dir['path'] ) ) {
                            $file = $upload_dir['path'] . '/' . $filename;
                        } else {
                            $file = $upload_dir['basedir'] . '/' . $filename;
                        }

                        // Create the image file on the server
                        file_put_contents( $file, $image_data );

                        // Check image file type
                        // $wp_filetype = wp_check_filetype( $filename, null );
                        // $filetype = wp_check_filetype( basename( $filename ), null );

                        // Set attachment data
                        $attachment = array(
                            // 'post_mime_type' => $wp_filetype['type'],
                            'post_mime_type' => 'image/png',
                            'post_title'     => sanitize_file_name( $filename ),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        );

                        // Create the attachment
                        $attach_id = wp_insert_attachment( $attachment, $file, $post_id );

                        // Include image.php
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        // require_once(ABSPATH . "wp-admin" . '/includes/file.php');
                        // require_once(ABSPATH . "wp-admin" . '/includes/media.php');

                        // Define attachment metadata
                        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

                        // Assign metadata to attachment
                        wp_update_attachment_metadata( $attach_id, $attach_data );

                        // And finally assign featured image to post
                        set_post_thumbnail( $post_id, $attach_id );

                    } else {

                        echo 'Featured image already in post!';

                    }
                
                // Otherwise, stop and set a flag
                } else {

                    // Arbitrarily use -2 to indicate that the page with the title already exists
                    $post_id = -2;

                    // Echo that it already exists
                    echo "Post already exists!";

                }

                // echo "<pre>";
                // print_r($res);
                // echo "</pre>";

            }

        }

    }

}


// Changing format for dates
function formatDate($d) {

    $date =  date_create($d);
    // return date_format($date, "F j, Y, g:i a");
    return date_format($date, "F j, Y");

    date_default_timezone_set("Europe/Vienna"); 
    setlocale(LC_ALL, 'de_AT');

    // $longdate = strftime('%d. %B %Y', strtotime($d));
    // return $longdate;

}

?>