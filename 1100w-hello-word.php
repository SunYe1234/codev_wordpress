<?php
/*
Plugin Name: show IMT official site
Descriprion: show the content of IMT official site where the shortcode 'get_from_IMTsite' is used
Version: 1.0
*/

/*Admin Pages*/

$post_data=array(
	"id"=>"100101","titre"=>"ccc"
);

if(isset($_POST["URL"]))
{
	$URL=$_POST["URL"];
	file_put_contents("/opt/lampp/htdocs/wordpress/wp-content/plugins/1100w-hello-word/url.txt",$URL);
	echo "<h2>$URL saved in the txt</h2>";

	/*$result=curl_GET($URL);
	echo $result;
	$result_J=json_decode($result);
	
	#echo "after decoding:";
	var_dump($result_J);
	#echo $result_J->id;*/
	 get_from_url();
}

/* 
get the JSON data of url obtained from txt using CURL
*/

function curl_GET($url)
{
	$ch=curl_init();
	
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_HEADER,0);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

	$out_put=curl_exec($ch);
	curl_close($ch);
	return $out_put;

}






/* 
get the JSON data of url obtained from txt 
and transform it to an object array
*/
 function get_from_url(){
## get the URL from url.txt
	$URL=file_get_contents("/opt/lampp/htdocs/wordpress/wp-content/plugins/1100w-hello-word/url.txt");
	echo "<h2>$URL from the txt</h2>";

## get the json data of $URL
	$content=curl_GET($URL);

## store the data in the content.txt
	file_put_contents("/opt/lampp/htdocs/wordpress/wp-content/plugins/1100w-hello-word/content.txt",$content);

## $content_J is an array of object established from $content
	$content_J=json_decode($content);
	/*echo "after decoding, all of the id are:";
	echo "<br>";
## output the id value of each object in the array
	$lenth=count($content_J);
	foreach($content_J as $object)
	{
		echo $object->id;
		echo "<br>";
	}*/
	show_table($content_J);
 	return $content_J;
}


/*
**create a table to show the array of objct
*/
function show_table($array)
{
	echo "<table width=\"100%\" boder=10>";
	echo "<tr><th align=\"center\">id</th><th align=\"center\">titre</th><th align=\"center\">contenu</th></tr>";
	foreach($array as $object)
	{
		echo "<tr><td align=\"center\">$object->id</td>";
		echo "<td align=\"center\">$object->titre</td>";
		echo "<td align=\"center\">$object->contenu</td></tr>";

	}
	echo "</table>";

}




/* 
get the HTML Page of the url obtained from txt 
*/
function get_remote_page($url,$args=array())
{
	$http=_wp_http_get_object();
	$html= $http->get($url,$args);

	if(is_wp_error($html))	return;
	
	$data=wp_remote_retrieve_body($html);
	if(is_wp_error($data))	return;

	return $html;
}



/* 
add an item of configuration menu in the configuration page
*/
function configuration_menu()
{
	
	echo "<h2>page de configuration de plugin IMT</h2>\n";
	echo "<form action=\"admin.php?page=management+de+configuration\" method=\"post\">";
	echo "<input type=\"text\" name=\"URL\">";
	echo "<input type=\"submit\" value=\"set\" name=\"name of button\">";
	echo "</form>";
}




/* 
hook for the function configuration_menu()
*/
function addConfigurationPage()
{
	add_menu_page('plugin IMT','configuration','manage_options','management de configuration','configuration_menu');
}



/* 
 creating the shortcode 'get_from_IMTsite' for this plugin
*/
function create_shortcode()
{
	# add_shortcode('recent_post','insert_str');
	add_shortcode('get_from_IMTsite','get_from_url');
}






add_action('admin_menu','addConfigurationPage');
add_action('init','create_shortcode');


?>
