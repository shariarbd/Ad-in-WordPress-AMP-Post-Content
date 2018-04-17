<?php
/**
 * @package adsense_apm_post_content
 * @version 1.0
 */
/*
Plugin Name: AdSense APM Post Content Ad
Plugin URI: http://kivabe.com/
Description: This plugin is used to add AdSense ad inside AMP post content.
Author: Shariar
Version: 1.0
Author URI: http://kivabe.com/
*/



$wp__amp_ad_pub_id_ = get_option('_amp_ad_pub_id_');
$wp__amp_ad_ad_slot_ = get_option('_amp_ad_ad_slot_');
$wp__amp_ad_para_pos_ = get_option('_amp_ad_para_pos_');

 
if ($wp__amp_ad_pub_id_!="" and $wp__amp_ad_ad_slot_ !="" ) {  

	add_action( 'pre_amp_render_post', 'is_amp_content_filter__' );

	function is_amp_content_filter__() { 
		add_filter( 'the_content', 'is_amp_adsense_in_content__' );
	}

	function is_amp_adsense_in_content__( $content ) {
		global $wp__amp_ad_pub_id_; 
		global $wp__amp_ad_ad_slot_; 
		global $wp__amp_ad_para_pos_;  

 		// Ad code. This is responsive as per Google guidelines for AdSense for AMP. 
		$ad_code = '<amp-ad width="100vw" height=320 type="adsense" data-ad-client="' . $wp__amp_ad_pub_id_ . '" data-ad-slot="' . $wp__amp_ad_ad_slot_ . '" data-auto-format="rspv" data-full-width><div overflow></div></amp-ad>';

 		// Insert Adsense ad between the content, after paragraph $wp__amp_ad_para_pos_
		$new_content = ad_insert_after_paragraph__( $ad_code, $wp__amp_ad_para_pos_, $content );

		return $new_content;

	}
	function ad_insert_after_paragraph__( $insertion, $paragraph_id, $content ) {
		$closing_p = '</p>';
		$counters = 0;
		$p_index = $paragraph_id;

		if ((int)$p_index === 0) {
			$paragraphs = $insertion.$content; 
			return $paragraphs;
		} else { 
			$paragraphs = explode( $closing_p, $content );
			foreach ($paragraphs as $index => $paragraph) {
				if ( trim( $paragraph ) ) {
					$paragraphs[$index] .= $closing_p;
				}
				if ( (int)$p_index - 1 === $index ) {
					$paragraphs[$index] .= $insertion;
				}

		// Uncomment following if you want to show another ad after 7th paragraph

			// if($index > 8) {
			// 	$counters +=1 ;
			// 	if ($counters ==1) {
			// 		$paragraphs[6] .= $insertion;
			// 	} 
			// }


		// Uncomment following if you want to show another ad after 12th paragraph

			// if($index > 13) { 
			// 	if ($counters ==7) {
			// 		$paragraphs[11] .= $insertion;
			// 	} 
			// }

			}

			return implode( '', $paragraphs ); 
		} //  End of (int)$p_index === 0
	} //  End of ad_insert_after_paragraph__

}

 
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'ad_amp_in_content_actions' );
function ad_amp_in_content_actions( $links ) {
	$links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=apm-content-ad.php') ) .'">Settings</a>';
   //$links[] = '<a href="http://wp-buddy.com" target="_blank">More plugins by WP-Buddy</a>';
	return $links;
}

add_action('admin_menu','ad_amp_in_content_Admin',1);
function ad_amp_in_content_Admin()	{
	if (function_exists('add_options_page')) {
		add_options_page('AdSense AMP Content', 'AdSense AMP Content', 9, basename(__FILE__),'ad_amp_in_content_options');
	}
}





// Admin Portion
function ad_amp_in_content_options(){
	if($_POST['amp_content_adSense_ad']){ 
		update_option('_amp_ad_pub_id_',$_POST['_amp_ad_pub_id_']);
		update_option('_amp_ad_ad_slot_',$_POST['_amp_ad_ad_slot_']);
		update_option('_amp_ad_para_pos_',$_POST['_amp_ad_para_pos_']);
		echo '<h3 style="color:green;">Data Updated.</h3>';
	}  
	?>
	<div class="wrap">
		<form method="post" id="amp_adSense_in_post">
			<fieldset class="options">
				<table class="form-table wp-list-table widefat plugins">
					<tr valign="top">
						<td>
							<h2>AdSense Ad in AMP Content Settings</h2>
							<h4>AdSense Publisher ID</h4>

							<input name="_amp_ad_pub_id_" type="text" id="_amp_ad_pub_id_" value="<?php echo get_option('_amp_ad_pub_id_') ;?>" size="60" placeholder="ca-pub-xxxxxxxxxxxxxx">
							<br>
							<p>If Empty, Ad will not show.  Put your AdSense Publisher ID here.</p>

							<h4>AdSense Ad Slot id</h4>
							<input name="_amp_ad_ad_slot_" type="text" id="_amp_ad_ad_slot_" value="<?php echo get_option('_amp_ad_ad_slot_') ;?>" size="60" placeholder="xxxxxxxxxxx" >
							<br><p>If Empty, Ad will not show. Put your AdSense Ad Slot id here </p>

							<h4>Choose First Ad Paragraph positions </h4> 
							<input type="text" size="60" id="_amp_ad_para_pos_" name="_amp_ad_para_pos_" value="<?php echo get_option('_amp_ad_para_pos_') ;?>" placeholder="3"  > 
							<br>
							<p>
								Default value, 3 i.e after 3rd paragraph, Ad will appear. if you want to show after 1st paragraph, put 1 <br>
			            		<!-- If you want to show another ad like may be after 5th and 9th para, put value 1,5,9 <br>
			            		We will count total paragraph, if more then 7, 5th para ad will come, if more then 11, 9th para will come and  so on.  -->
			            	</p> 
			            </td>
			        </tr>
			        <tr>
			        	<td>
			        		<input class="button button-primary" type="submit" name="amp_content_adSense_ad" value="Update" /> 
			        	</td>
			        </tr>
			    </table>
			</fieldset>
		</form>  
	</div>
<?php
}