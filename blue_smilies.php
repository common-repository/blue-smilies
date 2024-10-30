<?php
/*
Plugin Name: Blue Smilies
Plugin URI: http://www.wgd.me/
Description: Replace the basic wordpress emoticons with some nice blue emoticons.
Version: 1.0
Author: B. Dorin (WGD)
Author URI: http://wgd.me/
*/

/* configuration */

### Show hidden emoticons? (true/false)

$smilies_allow_hidden = true;

/* end configuration */



$js_reloader = "ReloadTextDiv();";
$smilies_javascript = '1';

if (!function_exists('get_option')) {
	$smilies_blog_url = $_SERVER['SCRIPT_URI'];
	$smilies_blog_url = preg_replace("/blue_smilies\\.php$/", "", $smilies_blog_url);
} else {
	$smilies_blog_url = get_option('siteurl') . '/wp-content/plugins/blue_smilies';
}

$yahoosmiliestrans = array (
  ':(' => '1.png',
  ':d' => '2.png',
  ':q' => '3.png',
  ':-]' => '4.png',
  ':sl' => '5.png',
  ':*' => '6.png',
  ':s' => '7.png', 
  ':o' => '8.png',
  '[-X' => '9.png',
  ':)' => '10.png',
  '[:]' => '11.png',
  ':h' => '12.png',
  ':((' => '13.png',
  ':P' => '14.png',
  ':e' => '15.png',
  ':))' => '16.png',
  ':bb' => '17.png',
  ':hm' => '18.png',
  'd:' => '19.png',
  ':fq' => '20.png',
  ':">' => '21.png',
  ':~' => '22.png',
  '|:s' => '23.png',
  'b-)' => '24.png',
  ':-b' => '25.png',
  ':->~' => '26.png',
  '=P~' => '27.png',
  '(o)' => '28.png',
  ':p^' => '29.png',
  ':d)' => '30.png',
  '#-o' => '31.png',
  ':-o' => '32.png',
  ':-&' => '33.png',
  ':"->' => '34.png',
  ':p!' => '35.png',
  '=o' => '36.png',
  ':-l' => '37.png',
  ':x' => '38.png',
  ':-h' => '39.png',
  '[-(' => '40.png',
  '(*)' => '41.png',
  ':xP' => '42.png',
  ':-x' => '43.png',
  '>:s' => '44.png',
  '|:))' => '45.png',
  '|-d' => '46.png',

);


$smilies_yahoo_always_shown = array(
	'1.png',
	'2.png',
	'3.png',
	'4.png',
	'5.png',
	'6.png',
	'7.png',
	'8.png',
	'9.png',
	'10.png',
	'11.png',
	'12.png',
	'13.png',
	'14.png',
	'15.png',
);

/* begin real code */

	$smiliestrans = $yahoosmiliestrans;
	$imagesize = $yahooimagesize;
	$smiliesshown = $smilies_yahoo_always_shown;

/* outputs javascript when called directly */

/* the following are taken and modified from wordpress internal smiley converter */

if (!function_exists('smiliescmp')) {
	function smiliescmp ($a, $b) {
		if (strlen($a) == strlen($b)) {
			return strcmp($a, $b);
		}
		return (strlen($a) > strlen($b)) ? -1 : 1;
	}
}

function smileyencode ($smiley) {
	$arr = preg_split('//', $smiley, -1, PREG_SPLIT_NO_EMPTY);
	$out = '';
	foreach ($arr as $char) {
		$out .= "&#" . ord($char) . ";" ;
	}
	return $out;
}

uksort($smiliestrans, 'smiliescmp');

// generates smilies' search & replace arrays
foreach($smiliestrans as $smiley => $img) {
	$smiley_masked = smileyencode($smiley) . " ";
	$width = $imagesize[$img][0];
	$height = $imagesize[$img][1];

	$tosearch = $smiley;
	$pre = '';
	$post = '';
	$tosearch = '/' . $pre . preg_quote($tosearch, '/ ') . $post . '/s';

	$smiliessearch[] = $tosearch;
	$smiliesreplace[] = "<img src='$smilies_blog_url/images/$img' alt='$smiley_masked' class='wp-smiley' width='$width' height='$height' title='$smiley_masked' />";
	
	$escsearch[] = $smiley;
	$escreplace[] = $smiley_masked;
	
	$imagetosmilies[$img] = htmlspecialchars($smiley);
}

if ( !function_exists('add_filter') ) {
	header("Content-type: text/javascript");
	echo "var smiley_smiley2image_s = new Array();\n";
	echo "var smiley_smiley2image_r = new Array();\n";
	foreach($smiliessearch as $i) {
		$i = preg_replace("/^\\//", "", $i);
		$i = preg_replace("/\\/s$/", "", $i);
		$i = addslashes($i);
		echo "smiley_smiley2image_s.push('$i');\n";
	}
	foreach($smiliesreplace as $i) {
		$i = addslashes($i);
		echo "smiley_smiley2image_r.push('$i');\n";
	}
	?>
		function smiley_convert_text(text) {
			var i;
			var smiley;
			var replacement;
			var re = new RegExp("", "i");
			for (i=0; i < smiley_smiley2image_s.length; i++) {
				smiley = smiley_smiley2image_s[i];
				replacement = smiley_smiley2image_r[i];
				// smiley = smiley.replace(/([^a-zA-Z0-9])/gi, "\\$1"); // escape everything
				re.compile(smiley, "gi");
				text = text.replace(re, replacement);
			}
			return text;
		}
		
		function toggleMoreIcons(e) {
			var moreIconsDiv = document.getElementById('moreIcons');
			var isVisible = (moreIconsDiv.style.display == 'inline');
			if (isVisible) {
				moreIconsDiv.style.display = 'none';
				e.innerHTML = 'More &nbsp;&raquo;';
			} else {
				moreIconsDiv.style.display = 'inline';
				e.innerHTML = '&laquo;&nbsp;Less';
			}
		}
		
		function appendTextToComment(text) {
			var commentArea = document.getElementById('comment');
			commentArea.value = commentArea.value + text + ' ';
			<?php echo $js_reloader ?>
		}
	<?php
	exit;
}



/* mask smilies before going to balanceTags */

function convert_custom_smilies_pre($text) {
	global $escsearch, $escreplace;
	$textarr = preg_split("/(<\\/?[a-z!].*>)/U", $text, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between
	$stop = count($textarr);// loop stuff
	for ($i = 0; $i < $stop; $i++) {
		$content = $textarr[$i];
		if ((strlen($content) > 0) && ('<' != $content{0})) { // If it's not a tag		
			$content = str_replace($escsearch, $escreplace, $content);
		}
		$output .= $content;
	}
	return $output;
}

/* unmask smilies after balanceTags */

function convert_custom_smilies_post($text) {
	global $escsearch, $escreplace;
	$textarr = preg_split("/(<\\/?[a-z!].*>)/U", $text, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between
	$stop = count($textarr);// loop stuff
	for ($i = 0; $i < $stop; $i++) {
		$content = $textarr[$i];
		if ((strlen($content) > 0) && ('<' != $content{0})) { // If it's not a tag		
			$content = str_replace($escreplace, $escsearch, $content);
		}
		$output .= $content;
	}
	return $output;
}

/* convert smilies to images */

function convert_custom_smilies($text) {
	global $smiliessearch, $smiliesreplace;
	$textarr = preg_split("/(<\\/?[a-z!].*>)/U", $text, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between
	$stop = count($textarr);// loop stuff
	for ($i = 0; $i < $stop; $i++) {
		$content = $textarr[$i];
		if ((strlen($content) > 0) && ('<' != $content{0})) { // If it's not a tag		
			$content = preg_replace($smiliessearch, $smiliesreplace, $content);
		}
		$output .= $content;
	}
	return $output;
}

/* add javascript */
function smilies_javascript() {
	global $smilies_blog_url;
	?>
	<script type="text/javascript" src="<?php echo $smilies_blog_url; ?>/blue_smilies.php"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $smilies_blog_url; ?>/blue_smilies.css" media="screen" />    
	<?php
}


function smilies_clickable() {
	global $smilies_blog_url, $smiliesshown, $smilies_allow_hidden, $imagesize, $imagetosmilies;
	$hidden_smileys = array_diff(array_keys($imagetosmilies), $smiliesshown);
    
	?>
	<?php
	foreach ($smiliesshown as $curr_smiley_img) {
		?>
		<img src="<?php echo $smilies_blog_url."/images/$curr_smiley_img"?>"
			alt="<?php echo $imagetosmilies[$curr_smiley_img];?>"
			title="<?php echo $imagetosmilies[$curr_smiley_img];?>"
			width="<?php echo $imagesize[$curr_smiley_img][0];?>"
			height="<?php echo $imagesize[$curr_smiley_img][1];?>"
			class="wp-smiley"
			onclick="appendTextToComment('<?php echo str_replace("\\", "\\\\", $imagetosmilies[$curr_smiley_img]);?>')"
		/>
		<?php
	}

	if ($smilies_allow_hidden) {
		?>
		<span id="moreIcons" style="display: none">
		<?php
		foreach ($hidden_smileys as $curr_smiley_img) {
			?>
			<img src="<?php echo $smilies_blog_url."/images/$curr_smiley_img";?>"
				alt="<?php echo $imagetosmilies[$curr_smiley_img];?>"
				title="<?php echo $imagetosmilies[$curr_smiley_img];?>"
				width="<?php echo $imagesize[$curr_smiley_img][0];?>"
				height="<?php echo $imagesize[$curr_smiley_img][1];?>"
				class="wp-smiley"
				onclick="appendTextToComment('<?php echo str_replace("\\", "\\\\", $imagetosmilies[$curr_smiley_img]);?>')"
			/>
			<?php
		}
		?>
		</span>

		<a href="#" onclick="toggleMoreIcons(this); return false;">More&nbsp;&raquo;</a>

		<?php
	}

}

if (function_exists('add_filter')) {
	@add_filter('content_save_pre', 'convert_custom_smilies_pre', 30);
	@add_filter('excerpt_save_pre', 'convert_custom_smilies_pre', 30);
	@add_filter('comment_save_pre', 'convert_custom_smilies_pre', 30);
	@add_filter('pre_comment_content', 'convert_custom_smilies_pre', 5);

	@add_filter('content_save_pre', 'convert_custom_smilies_post', 70);
	@add_filter('excerpt_save_pre', 'convert_custom_smilies_post', 70);
	@add_filter('comment_save_pre', 'convert_custom_smilies_post', 70);
	@add_filter('pre_comment_content', 'convert_custom_smilies_post', 35);

	@add_filter('the_content', 'convert_custom_smilies', 3);
	@add_filter('the_excerpt', 'convert_custom_smilies', 3);
	@add_filter('comment_text', 'convert_custom_smilies', 3);

	if ($smilies_javascript) {
		@add_filter('wp_head', 'smilies_javascript');
	}
}?>
