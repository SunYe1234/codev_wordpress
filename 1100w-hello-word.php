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
/*
select information from the $_POST variable
*/
if(isset($_POST["URL"]))
{
	##save the URL of the API REST	
	$URL=$_POST["URL"];
	file_put_contents("/opt/lampp/htdocs/wordpress/wp-content/plugins/1100w-hello-word/url.txt",$URL);
}
if(isset($_POST["color"]))
{	
	##save the requested color for the table in which the content is shown
	$color=$_POST["color"];
	file_put_contents("/opt/lampp/htdocs/wordpress/wp-content/plugins/1100w-hello-word/color.txt",$color);
}
/* 
get the JSON data of url obtained from txt using CURL
*/
function curl_GET($url)
{
	##initialization of the request
	$ch=curl_init();
	##configuration of the request : no header, no ssl verification
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_HEADER,0);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
	##execution of the request and stockage of the content obtained
	$out_put=curl_exec($ch);
	##closure of the request
	curl_close($ch);
	return $out_put; ##return the content
}
/* 
get the JSON data of url obtained from txt 
and transform it to an object array
*/
 function get_from_url(){
## get the URL from url.txt
	$URL=file_get_contents("/opt/lampp/htdocs/wordpress/wp-content/plugins/1100w-hello-word/url.txt");
## get the json data of $URL
	$content=curl_GET($URL);
## store the data in the content.txt
	file_put_contents("/opt/lampp/htdocs/wordpress/wp-content/plugins/1100w-hello-word/content.txt",$content);
## $content_J is an array of object established from $content
	$content_J=json_decode($content);
	show_table($content_J);
 	return $content_J;
}
/*
**create a table to show the array of objct
*/
function show_table($array)
{
	##read the color from the file
	$color=file_get_contents("/opt/lampp/htdocs/wordpress/wp-content/plugins/1100w-hello-word/color.txt");
	##creation of the table
	echo "<table width=\"100%\" boder=10>";
	echo "<tr><th bgcolor=$color align=\"center\">id</th><th bgcolor=$color align=\"center\">titre</th><th bgcolor=$color align=\"center\">contenu</th></tr>";
	foreach($array as $object)
	{
		echo "<tr><td align=\"center\">$object->id</td>";
		echo "<td align=\"center\">$object->titre</td>";
		echo "<td align=\"center\">$object->contenu</td></tr>";
	}
	echo "</table>";
}
/**
*create a dropdown list in the configuration page to 
*choose a color for the table 
*/
function set_list_color()
{
	
	echo "<h2>choose a color for your table:</h2>"; 
	echo "<form action=\"admin.php?page=management+de+configuration\" method=\"post\">";
	echo "<select  name=\"color\" size=\"1\">";
	echo "<option value=\"#FF5733\" > red";
	echo "<option value=\"#5D76F5\" > blue";
	echo "<option value=\"#F3A343\" > yellow";
	echo "<option value=\"#A4F5C0\" > green";
	echo "<option value=\"#E4A4F5\" > purple";
	echo "</select>";
	echo "<input type=\"submit\" value=\"set color\" name=\"name of button\">";
	echo "</form>";
}
/**
*choose URL of API REST
*/
function set_url()
{
	echo "<h2>choose your API REST:</h2>\n";
	echo "<form action=\"admin.php?page=management+de+configuration\" method=\"post\">";
	echo "<input type=\"text\" name=\"URL\">";
	echo "<input type=\"submit\" value=\"set url\" name=\"name of button\">";
	echo "</form>";
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
	set_url();
	set_list_color();
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
	add_shortcode('get_from_IMTsite','get_from_url');
}
/*
activation of the configuration and shortcode
*/
add_action('admin_menu','addConfigurationPage');
add_action('init','create_shortcode');
?>
