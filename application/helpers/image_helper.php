<?php 
	if(!defined('BASEPATH')) exit('No direct script access allowed');
	if(!function_exists('create_image_thumb')) {
  		function create_image_thumb($src,$destination="",$thumb=FALSE,$dimensions=array()) {
    		// Getting CI class instance.
    		$ci=& get_instance();
			if(!$ci->load->is_loaded('image_lib')){
				$ci->load->library('image_lib');
			} 
			$ci->image_lib->clear();
			$width=$height=200;
			if($destination==''){
				$destination=$src;
			}
			if(!empty($dimensions)){
				if(isset($dimensions['width'])){ $width=$dimensions['width']; }
				if(isset($dimensions['height'])){ $height=$dimensions['height']; }
			}
			
			$config['image_library'] = 'gd2';
			$config['source_image'] = $src;
			$config['new_image'] = $destination;
			$config['create_thumb'] = $thumb;
			$config['maintain_ratio'] = FALSE;
			$config['width']     = $width;
			$config['height']   = $height;
			$ci->image_lib->initialize($config);
			$ci->image_lib->resize();
		}  
	}
	if(!function_exists('base64_to_jpeg')) {
        function base64_to_jpeg($base64_string, $output_file) {
            // open the output file for writing
            $ifp = fopen( $output_file, 'wb' ); 

            // split the string on commas
            // $data[ 0 ] == "data:image/png;base64"
            // $data[ 1 ] == <actual base64 string>
            $data = explode( ',', $base64_string );

            // we could add validation here with ensuring count( $data ) > 1
            if(isset($data[1])){
                fwrite( $ifp, base64_decode( $data[1] ) );
            }
            else{
                fwrite( $ifp, base64_decode( $data[0] ) );
            }
            // clean up the file resource
            fclose( $ifp ); 

            return $output_file; 
        }
    }
	if(!function_exists('create_letter_image')) {
  		function create_letter_image($letter,$color=array(180,112,160)) { 
			// GD is optional in some PHP/XAMPP installations. Return an SVG avatar
			// when it is unavailable instead of failing the image request with HTTP 500.
			if (!function_exists('imagecreatetruecolor')) {
				$letter = strtoupper(substr((string) $letter, 0, 1));
				$letter = $letter === '' ? 'P' : $letter;
				$red = isset($color[0]) ? max(0, min(255, (int) $color[0])) : 180;
				$green = isset($color[1]) ? max(0, min(255, (int) $color[1])) : 112;
				$blue = isset($color[2]) ? max(0, min(255, (int) $color[2])) : 160;

				header('Content-Type: image/svg+xml; charset=UTF-8');
				echo '<svg xmlns="http://www.w3.org/2000/svg" width="250" height="250" viewBox="0 0 250 250" role="img" aria-label="Profile initial">';
				echo '<rect width="250" height="250" fill="rgb(' . $red . ',' . $green . ',' . $blue . ')"/>';
				echo '<text x="125" y="125" fill="#fff" font-family="Arial, sans-serif" font-size="100" font-weight="400" text-anchor="middle" dominant-baseline="central">' . htmlspecialchars($letter, ENT_QUOTES, 'UTF-8') . '</text>';
				echo '</svg>';
				return;
			}

            // Create a blank image with a white background
            $width = 250;
            $height = 250;
            $image = imagecreatetruecolor($width, $height);
            $backgroundColor = imagecolorallocate($image, $color[0], $color[1], $color[2]); // Replace with your desired background color
            imagefill($image, 0, 0, $backgroundColor);

            // Set the text color to white (or any contrasting color)
            $textColor = imagecolorallocate($image, 255, 255, 255);

            // Set the font size and path to a font file
            $fontSize = 100;
            $fontPath = './includes/fonts/Roboto-Regular.ttf'; // Replace with the actual path to a TTF font file

            // The letter you want to display
            $letter = empty($letter)?'P':$letter;

            // Calculate the position to center the letter
            $letterSize = imagettfbbox($fontSize, 0, $fontPath, $letter);
            $letterWidth = (int)($letterSize[2] - $letterSize[0]);
            $letterHeight = (int)($letterSize[1] - $letterSize[7]);
            $x = (int)(($width - $letterWidth) / 2);
            $y = (int)(($height - $letterHeight) / 2 + $letterHeight);

            // Add the letter to the image
            imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $letter);

            // Set the content type header to display the image
            header('Content-Type: image/png');

            // Output the image to the browser
            imagepng($image);

            // Free up memory
            imagedestroy($image);
        }
    }
?>