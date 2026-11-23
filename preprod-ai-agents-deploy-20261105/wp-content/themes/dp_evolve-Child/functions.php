<?php

// Redirect /resources/?cat=expert-insight to /expert-insights/
add_action( 'template_redirect', function () {
    if ( isset( $_GET['cat'] ) && $_GET['cat'] === 'expert-insight' && strpos( $_SERVER['REQUEST_URI'], '/resources/' ) !== false ) {
        wp_redirect( home_url( '/expert-insights/' ), 301 );
        exit;
    }
} );

// Add nonce verification for security - admin-ajax only
if ( is_admin() ) {
	add_action('wp_ajax_handle_tag_processing_request', 'handle_tag_processing_request');
	add_action('wp_ajax_nopriv_handle_tag_processing_request', 'handle_tag_processing_request');
}
// Properly hook the enqueue function

// Handle AJAX request for tags
function handle_tag_processing_request()
{
	global $wpdb,$post;
	$current_post_id = $_POST['post_id'];
	// Use filter_input instead of direct $_POST access
	$search_terms_val = $_POST['tag_search'];
	$cats = get_the_category($current_post_id);
	$search_terms = str_replace("Grazitti Interactive", "", $search_terms_val);
	$search_terms = str_replace("|", "", $search_terms);
	$search_terms = trim($search_terms); 
	$case_study_page_id = get_page_by_path('case-study')->ID;
	// Validate inputs
    $term = get_term_by('slug', 'case-study', 'resource_cat');
	
if($_POST['post_type'] == 'post'){

$three_years_ago = date('Y-m-d', strtotime('-3 years'));

	      $query_blog = $wpdb->prepare("
            SELECT p.ID, p.post_title, p.guid, p.post_content, 
                   MATCH(p.post_content) AGAINST (%s IN NATURAL LANGUAGE MODE) AS rel,
                   t.slug AS category_slug,
                   t.name AS category_name
            FROM wp_posts p
            JOIN wp_term_relationships tr ON tr.object_id = p.ID
            JOIN wp_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
            JOIN wp_terms t ON t.term_id = tt.term_id
            WHERE MATCH(p.post_content) AGAINST (%s IN NATURAL LANGUAGE MODE)
            AND p.post_type = 'post'
            AND p.post_status = 'publish'
            AND tt.taxonomy = 'category'
            AND t.term_id = %d
			AND p.post_date >= %s
            ORDER BY rel DESC LIMIT 4
        ", $search_terms, $search_terms,$cats[0]->cat_ID,$three_years_ago);
}
else
{
	$query_blog = $wpdb->prepare("
		SELECT p.ID, p.post_title, p.guid, p.post_content, 
			   MATCH(p.post_content) AGAINST (%s IN NATURAL LANGUAGE MODE) AS rel
		FROM wp_posts p
		WHERE MATCH(p.post_content) AGAINST (%s IN NATURAL LANGUAGE MODE)
		AND p.post_type = 'post'
		AND p.post_status = 'publish'
		ORDER BY rel DESC LIMIT 4
	", $search_terms, $search_terms);
	
}
	$query_resources = $wpdb->prepare(
		"SELECT DISTINCT p.ID, p.post_title, MATCH(p.post_title) AGAINST(%s IN NATURAL LANGUAGE MODE) as rel 
		FROM wp_posts p 
		INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id 
		INNER JOIN wp_terms t ON tr.term_taxonomy_id = t.term_id 
		INNER JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id 
		WHERE MATCH(p.post_title) AGAINST(%s IN NATURAL LANGUAGE MODE) AND p.post_type = 'resource'
		AND p.post_status = 'publish' 
		AND tr.term_taxonomy_id != %d
		AND tt.taxonomy = 'resource_cat'
        AND t.slug != 'case-study'
		ORDER BY rel DESC 
		LIMIT 3",
		$search_terms,
		$search_terms,
		(int)$term->term_taxonomy_id
	);
	$query_casestudy = $wpdb->prepare("
    SELECT DISTINCT p.ID, p.post_title, MATCH(p.post_title) AGAINST(%s IN NATURAL LANGUAGE MODE) as rel
    FROM $wpdb->posts p
    INNER JOIN $wpdb->term_relationships tr ON p.ID = tr.object_id
    INNER JOIN $wpdb->terms t ON tr.term_taxonomy_id = t.term_id
    INNER JOIN $wpdb->term_taxonomy tt ON t.term_id = tt.term_id
    WHERE MATCH(p.post_title) AGAINST(%s IN NATURAL LANGUAGE MODE) 
    AND p.post_status = 'publish'
    AND p.post_type = 'resource'
    AND tr.term_taxonomy_id = %d
    ORDER BY rel DESC
    LIMIT 3;
", $search_terms, $search_terms, $term->term_taxonomy_id);

if($_POST['post_type'] == 'post'){
		$category_names = array();


    // Loop through each category and store its name in the array
    foreach ($cats as $category) {
        $category_names[] = $category->name; // Add category name to array
    }
	
	 $last_category = array_pop($category_names); // Remove the last element from the array
     array_unshift($category_names, $last_category);
	
	    $category_names_string = implode(', ', $category_names);

		$search_terms = trim($category_names_string) . ", " . $search_terms;


}
	$query = $wpdb->prepare("
	SELECT DISTINCT p.post_title, p.ID, p.guid, 
       MATCH(p.post_title) AGAINST (%s IN NATURAL LANGUAGE MODE) AS rel
       FROM wp_posts p
       WHERE MATCH(p.post_title) AGAINST (%s IN NATURAL LANGUAGE MODE)
       AND p.post_type = 'page'
       AND p.post_status = 'publish'
	   AND p.ID != %d
	   AND p.post_parent != %d
      ORDER BY rel DESC LIMIT 4
", $search_terms, $search_terms, $current_post_id,$case_study_page_id);

	// Execute the query
	$results = $wpdb->get_results($query);
	$blog_results = $wpdb->get_results($query_blog);
	$resource_results = $wpdb->get_results($query_resources);
	$casestudy_results = $wpdb->get_results($query_casestudy);

	// Process page results
	foreach ($results as $result) {
		$post_url = get_permalink($result->ID);

		$matches[] = $post_url;
		$post_title[] = $result->post_title;
	}

	// Process blog results
	foreach ($blog_results as $result) {
		$blog_urls[] = get_permalink($result->ID);
		$blog_titles[] = $result->post_title;
	}

	// Process resource results
	foreach ($resource_results as $result) {
		$resource_urls[] = get_permalink($result->ID);
		$resource_titles[] = $result->post_title;
		
	// Get the permalink of the post (replace $post_id with your post ID)
$post_permalink = get_permalink($result->ID);

// Extract the path from the permalink URL
$path = parse_url($post_permalink, PHP_URL_PATH);
// Split the path into an array by slashes
$slugs = explode('/', trim($path, '/')); 
if($slugs[1] === 'press-releases')
{
	$slugs[1] = "Press Release";
}
if($slugs[1] === 'ebooks')
{
	$slugs[1] = "E-book";
}
if($slugs[1] === 'articles')
{
	$slugs[1] = "Article";
}
if($slugs[1] === 'infographics')
{
	$slugs[1] = "Infographic";
}
if($slugs[1] === 'datasheet')
{
	$slugs[1] = "Datasheet";
}
$resource_slugs[] = $slugs[1]; 

	}

	// Process casestudy results
	foreach ($casestudy_results as $result) {
		$permalink = get_field('webinar_read_more_link', $result->ID);
		//Fatal Error -> $casestudy_urls[] = $permalink['url'];

		if (is_array($permalink) && isset($permalink['url'])) {
	        $casestudy_urls[] = $permalink['url'];
	    } elseif (is_string($permalink) && !empty($permalink)) {
	        $casestudy_urls[] = $permalink;
	    }

		$casestudy_titles[] = $result->post_title;
	}

	echo json_encode(array(
		'page_url' => $matches,
		'page_title' => $post_title,
		'blog_url' => $blog_urls,
		'blog_title' => $blog_titles,
		'resource_url' => $resource_urls,
		'resource_title' => $resource_titles,
		'resource_slugs' => $resource_slugs,
		'casestudy_url' => $casestudy_urls,
		'casestudy_title' => $casestudy_titles,
		'URL' => $search_terms
	));
	exit;
}
if(!defined('DPTPLNAME')) {	define('DPTPLNAME', 'Evolve');}
function dp_child_theme_stylesheet() {	   	  		  
		wp_dequeue_style( 'dp-css');
		if(!is_singular('jobs')){
			wp_deregister_script('owl-js');
			wp_enqueue_script( 'owl-js', get_stylesheet_directory_uri().'/js/owl.carousel.min.js', false, null);
		}
		wp_deregister_script('jquery-noconflict-js');
		wp_enqueue_script('jquery-noconflict-js', get_stylesheet_directory_uri().'/js/jquery.noconflict.js', '',true);
		wp_deregister_script('jquery-cookie-js');
		wp_enqueue_script('jquery-cookie-js', get_stylesheet_directory_uri().'/js/jquery-cookie.js', '',true);
		// jQuery appear plugin

		wp_deregister_script('frontend-js');
		wp_enqueue_script('frontend-js', get_stylesheet_directory_uri().'/js/frontend.js',false,true);
	    
		wp_enqueue_script( 'bootstrap-js', get_stylesheet_directory_uri().'/js/bootstrap.min.js', false, null);
		wp_enqueue_script( 'source-tracking', get_stylesheet_directory_uri().'/js/source-tracking.js?ver=1.0', false, null);
}
add_action('wp_enqueue_scripts', 'dp_child_theme_stylesheet', 110);
register_nav_menus(
    array('maginate_menu'=>'Maginate Menu')
);
/**
 *	Shortcode [blogcategory]
 */
function blogcategory($atts){	
		global $post;
		extract(shortcode_atts(array('catg_slug' => 'default','limit' => 'default'),$atts));
		if($atts['exclude']){
			$excats = preg_replace('/\s+/', '', $atts['exclude']);
			$args_c = array(
				'posts_per_page'         => -1,
				'category_name'          => $excats,
				'post_type'              => 'post',
				'post_status'            => 'publish',
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			);
			$posts_array = get_posts( $args_c );
			$expost = array();
			foreach($posts_array as $excludedpost){$expost[] = (int) $excludedpost;};
		}else{	$expost[] = 0;}			  
		if ( empty( $atts['catg_slug'] ) ) {
			$args = array(
		'posts_per_page' => $limit,'offset'=> 0,'category'=> '','category_name'=> '','orderby'=> 'post_date','order' => 'DESC','include' => '','exclude' => '','meta_key' => '','meta_value' => '','post_type' => 'post','post_mime_type'   => '','post_parent' => '','author' => '','post_status' => 'publish',/* 	'suppress_filters' => true  */);
		}else if(strpos($catg_slug, ",") === false){
			$args=array('category_name' => $catg_slug,'exclude' => $expost,'showposts' => $limit, //'paged' => $paged,
				'orderby' => 'post_date','order' => 'DESC','post_status' => 'publish'); 
		}else{
			$dd = explode(',',$catg_slug);
			$args=array('category_name' => $catg_slug,'exclude' => $expost, 'showposts' => $limit,  //'paged' => $paged,          
				'orderby' => 'post_date', 'order' => 'DESC', 'post_status' => 'publish'); 
		}
		$res = get_posts( $args ); 
		echo '<div class="super-rss-reader-widget"><div class="srr-wrap  srr-style-none">';
		$i=0;
		$div_flag = true;
			foreach( $res as $pst ){ setup_postdata($pst); $i++;?>
<div class="srr-item <?php echo $div_flag ? 'odd' : 'even'; ?>">
    <div class="srr-meta"></div>
    <div class="srr-title">
        <div class="blog-image"><?php 
						if ( has_post_thumbnail( $pst->ID ) ) {
							$url = wp_get_attachment_url( get_post_thumbnail_id($pst->ID, 'thumbnail') ); 
							$ext = pathinfo($url, PATHINFO_EXTENSION);
							if($url){ if($ext=='gif'){ ?>
            <img src="<?php echo $url ?>" style="width:70px;height:65px" class="srr-thumb"
                alt="<?php echo substr($pst->post_title,0,45); ?>" /><?php 
							}else{
								$resize_url = rw_image_resize( get_post_thumbnail_id($pst->ID, $thumb_size), 70, 65, $crop = true ); ?>
            <img src="<?php echo $resize_url ?>" class="srr-thumb" alt="<?php echo substr($pst->post_title,0,45); ?>" /><?php 
							} 
							} 
							else{ ?><img src="<?php echo $resize_url ?>" class="srr-thumb"
                alt="<?php echo substr($pst->post_title,0,45); ?>" /> <?php
							}
						}?>
        </div>
        <div class="blog-title">
            <a href="<?php echo  get_permalink( $pst->ID ); ?>" target="_blank" title="Posted on <?php $date = date_create($pst->post_date);
									echo date_format($date,'d M Y'); ?>">
                <?php  if(strlen($pst->post_title)>45){echo trim(substr($pst->post_title,0,45)).'...';}else{echo trim(substr($pst->post_title,0,45));} ?>
            </a>
            <p class="post-summary srr-clearfix"><?php 
								$post_content = strip_tags(html_entity_decode( $pst->post_content, ENT_QUOTES, "UTF-8"));
								if (strlen($post_content) > 150 && preg_match('/^.{1,150}\b/s', $post_content, $match))	{
									if (strpos($match[0], 'vc_column_text') !== false) {										
										if (($pos = strpos($match[0], 'vc_column_text')) !== FALSE) { 
											$pos_r = strpos($match[0],']',$pos);
											$post_content = substr($match[0], $pos_r+1);//."&hellip;"; 
											$post_content = trim(substr($post_content,0,40)).'...';
										}										
									}else{$post_content = trim(substr($match[0],0,40))."...";}
								}
								echo $post_content;?>
            </p>
            <div class="infoFOB">
                <span class="post_dates">
                    <span class="calender-icon-blog_sprite"></span>&nbsp;<?php 
							$date = date_create($pst->post_date);echo date_format($date,'d M Y').'&nbsp;'; ?>
                </span>
                <span class="post-fobcat">in&nbsp;<i class="post-fitbit-content"><?php	
							if(strpos($catg_slug, ",") === false){
								$category_detail=get_the_category($pst->ID);
								$j=0;
								$ct = count($category_detail);
								foreach($category_detail as $cd){ 
								if($j<=$ct){if($j==0){ 	echo ''; }else{ echo ', ';}}
									echo $cd->cat_name;
									$j++;
									if($j == 2){break;}
								}
							}else{	
								$dp=explode(',',$catg_slug);
								foreach($dp as $d){ //echo '<pre>'; print_r($d);
									$category = get_category_by_slug($d);
									//echo $category->name.',';
								}
								$category_detail=get_the_category($pst->ID);//$post->ID
								$j=0;
								$ct = count($category_detail);
								foreach($category_detail as $cd){ 
									if($j<=$ct){if($j==0){ 	echo ''; }else{ echo ', ';}}
									echo substr($cd->cat_name,0,15);
									$j++;
									if($j == 2){break;}
								}
							}?>
                    </i>
                </span>
            </div>
        </div>
    </div>
</div><?php		
			$div_flag = !$div_flag;
			} //echo $i;
		echo '</div></div>';
} 
add_shortcode('blogcategory','blogcategory');

add_shortcode('blogsarchives','blogs_archives');
function blogs_archives(){
	echo '<div id="execphp-4" class="widget_execphp"><div><h3 class="box-title"><span> Archives</span></h3>
	<div class="execphpwidget"> <aside id="meta" class="widget">
                <ul class="catgs">
					<li class="arch">Select Archives
						<ul>';
							wp_get_archives( array( 'type' => 'monthly' ) );
	echo '				</ul>
					</li>
                </ul>
            </aside>              	
        </div>
		</div></div>';	        
}

add_shortcode('blogsarchivesnew','blogs_archives_new');
function blogs_archives_new(){
	echo '<div id="execphp-4" class="widget_execphp"><div><h3 class="box-title"><span> Archives</span></h3>
	<div class="execphpwidget"> <aside id="meta" class="widget">
                <div class="btn-group w-100">
					<a id="archives-cta" class="archives_btn btn rounded px-3 py-2 bg-white border text-left dropdown-toggle w-100" data-toggle="dropdown">Select Archives </a>
						<ul class="custom-archives rounded dropdown-menu w-100" aria-labelledby="archives-cta">';
							wp_get_archives( array( 'type' => 'monthly' ) );
	echo '				</ul>
                </div>
            </aside>              	
        </div>
		</div></div>';		
        
}
register_nav_menus(array('blogs_uber_menu' => 'Uber Menu for Blogs','marketo_services'=>'Menu [Marketo Services]','desktop-menu'=>'Desktop Menu',));
function wordpress_breadcrumbs() {
  $delimiter = '';
  $currentBefore = '<span class="current">';
  $currentAfter = '</span>';
  if(is_page('27752')){}else{
  if ( !is_home() && !is_front_page() || is_paged() ) { 
    echo '<div class="dp-breadcrumbs">';
    global $post;
	if ( is_page() && !$post->post_parent ) {	the_title();}
	elseif(is_page('24144')){ the_title();}
	elseif ( is_page() && $post->post_parent ) { 
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ($parent_id) {
        $page = get_page($parent_id);
		$no_link = get_the_title($page->ID);
		if((strpos($no_link,'Services') !== false) || (strpos($no_link,'Legal') !== false)||(strpos($no_link,'Solutions') !== false)||(strpos($no_link,'Resources') !== false)||(strpos($no_link,'Company') !== false)){ 
			$breadcrumbs[] = '<a href="javascript:void(0);">' . get_the_title($page->ID) . '</a>';
		}else{$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>'; }
		$parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs); 
      foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
      echo $currentBefore;
      the_title();
      echo $currentAfter;
    }
    echo '</div>';
  }}
}
add_shortcode('wordpress_breadcrumbs','wordpress_breadcrumbs');
 add_shortcode('wordpress_scriptbreadcrumbs','wordpress_scriptbreadcrumbs');
function wordpress_scriptbreadcrumbs() {
	$breadcrumbs = array();
	$title='';
	global $post;	
	if(is_page('27752') || (is_page_template('template.fullwidth.nosidebar.vc.php') && !$post->post_parent ) || (is_page_template('template.fullwidth.sticky.header.php') && !$post->post_parent )){}else{
	if ( !is_home() && !is_front_page() || is_paged() ) {  
	  if ( is_page() && !$post->post_parent && !is_page( 'sitemap' ) && !is_page( 'au' ) && !is_page( 'sg' ) && !is_page( '39846' ) && !is_page( '40137' ) && !is_page( '41051' )) { the_title();}
	  elseif(is_page('24144')){the_title(); }
	  elseif ( is_page() && $post->post_parent ) {
		$parent_id  = $post->post_parent;
		while ($parent_id) {
		  $page = get_page($parent_id);
		  $no_link = get_the_title($page->ID);
		  $breadcrumbs[] =  get_the_title($page->ID);		
		  $parent_id  = $page->post_parent;
		} 
	  }	  
	}}
	$breadcrumbs = array_reverse($breadcrumbs); 	
	return $breadcrumbs; 
} 

function ubermenu_post_grid( $atts ){ 
    //we're going to need access to the UberMenu object for image functionality
    global $uberMenu;     
    //Merge user-provided attributes with default values
    //and extract into individual variables
    extract(shortcode_atts(array( 
        'num'       =>   4,          //maximum number of posts to retrieve
        'grid'      =>   4,          //number of columns per row
        'category'  =>   '',         //optional category to retrieve posts from 
        'img'       =>   'on',       //'on' or 'off' to display the image or not
        'img_width' =>   220,        //image width in pixels
        'img_height'=>   120,        //image height in pixels         
        'excerpt'   =>   'off',      //'on' or 'off' to display the excerpt
        'default_img' => false,      //URL of a default image to display when no featured image is available
        'offset'    =>   0,          //offset for get_posts() (number of posts to skip) 
    ), $atts));     
    //Setup default query arguments
    $query_args = array( 'numberposts'   =>   $num,'offset'  =>   $offset, 'suppress_filters' => true ); 
    //If the user has provided a query, parse it into the query args
    if( !empty( $category ) ){
        //Handle numeric category values
        if(is_numeric($category)){  $query_args['category'] = $category; }
        //Handle category names as well
        else $query_args['category_name'] = $category;     
    } 
    //Retrieve matching posts from the database
    $posts = get_posts( $query_args ); 
    //Wrap our grid in an unordered list container
    $html = '<ul class="uber-post-grid uber-post-grid-'.$grid.'">'; 
    //Loop through each post and output the image, title, and excerpt
    foreach( $posts as $post ){ 
        //Each post becomes a list item
        $html.= '<li class="uber-post-grid-item post-'.$post->ID.'">';
 
        //We will wrap the entire item contents with the post's permalink (note anchors can be block level elements in HTML5)        
 
        //Get the image at the right size and append it
        $style = '';
        if($img == 'on'){
						$imagesrc = wp_get_attachment_url( get_post_thumbnail_id($post->ID, 'thumbnail') ); 
						if($imagesrc){$style = 'style="background-image:url('. $imagesrc .');"'; }
          $html.= '<a href="'.get_permalink( $post->ID ).'" '.$style.'>';
        }else{$html.= '<a href="'.get_permalink( $post->ID ).'">';}
         
        //Add the post title
        $html.= '<h5>'.$post->post_title.'</h5>';
 
        //Add the excerpt
        /*if($excerpt == 'on')
            $html.= '<span class="uber-post-grid-excerpt">'.apply_filters( 'get_the_excerpt', $post->post_excerpt ).'</span>';*/
 
        $html.= '</a>'; 
        $html.= '</li>';
    } 
    $html.= '</ul>';
 
    //Return the entire list
    return $html;
}
add_shortcode( 'ubermenu-post-grid' , 'ubermenu_post_grid' );
add_shortcode('term_tag','limit_to_five_tags');
function limit_to_five_tags() {
	$output = '';
	if ( function_exists( 'wp_tag_cloud' ) ) : 
	$args = array('smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 5,'format' => 'flat', 'separator' => "\n", 'orderby' => 'name', 'order' => 'ASC','exclude' => '', 'include' => '', 'link' => 'view', 'taxonomy' => 'post_tag', 'post_type' => '', 'echo' => false );
		$output .= '<h3 class="box-title"><span>Tags</span></h3>';
		$output .= '<div class="tagcloud">';
		$output .= wp_tag_cloud( $args );
		$output .= '</div>';
 endif;
	return $output;
}
add_image_size( 'custom-size', 234, 170 );
add_image_size( 'customsize', 415, 270 );

remove_action('wp_head', 'wp_generator');

function remove_version(){	return '';}

add_filter('the_generator', 'remove_version');

/*-----Shortcode for showing multiple or single employees testimonial-----*/
add_shortcode('emptestimonial','emp_testimonial');
	function emp_testimonial($atts){
			global $post;
			extract(shortcode_atts(array('testimonial_text' => 'default', 'author_image_id'=>'default','author' => 'default','designation'=>'default','indicators'=>'default'),$atts));
			$i=0;
		   $arr_testimonial_text = explode('|',$testimonial_text);
		   $arr_author_image_id = explode('|',$author_image_id);
		   $arr_author = explode('|',$author);
		   $arr_designation = explode('|',$designation);		  
		  if($indicators=='true'){
		   if(sizeof($arr_testimonial_text)>1||sizeof($arr_author_image_id)>1||sizeof($arr_author)>1||sizeof($arr_designation)>1){ 
				echo '
			 <div class="emp_maincontainer">
				<div class="emp_container">
					<div class="main-content">
						<div class="banner-heading">Infinite Opportunities. <br style="transition: all 0s ease 0s; min-height: 0px; min-width: 0px; line-height: 60px; border-width: 0px; margin: 0px; padding: 0px; letter-spacing: 0px; font-size: 48px;">Infinite Growth. </div>
						<div class="banner-desc">Grazitti has been growing at a phenomenal rate and we want <br style="transition: all 0s ease 0s; min-height: 0px; min-width: 0px; line-height: 30px; border-width: 0px; margin: 0px; padding: 0px; letter-spacing: 0px; font-size: 18px;">YOU to become a part of this growth!</div>
						<div class="css-lmore">
						<a class="career-current-opening-button" href="#current-opening" style="line-height:70px;">View Current Openings</a>
					</div>
					</div>
					<div class="tp-caption emp_details_slider">
						<div class="container" id="employee-details-slider">
							<div class="row">
								<div class="col-md-12">
									<section class="text-center" id="testimonials-slides"> 
										<div data-ride="carousel" class="carousel slide" id="testimonialCarousell">
											<!-- Indicators -->
												<ol class="carousel-indicators">';
													for($i=0;$i<sizeof($arr_testimonial_text);$i++){ 
														if($i==0){
															echo '<li data-target="#testimonialCarousell" data-slide-to="'.$i.'" class="active"></li> ';
														}else{
															echo '<li data-target="#testimonialCarousell" data-slide-to="'.$i.'" ></li> ';
														}
													}
										  echo '</ol>
												<div class="carousel-inner center-block">'; 
													for($i=0;$i<sizeof($arr_testimonial_text);$i++){ 
														if($i==0){
															echo '<div class="item active">';
														}else{
															echo '<div class="item">';
														}
																echo '<div class="testimonial_text">
																		<p>
																			<img alt="white quote" src="/wp-content/uploads/2016/06/white-quote.png">'.esc_html($arr_testimonial_text[$i]).'
																		</p>
																	  </div>';	
															$src[$i] = wp_get_attachment_url( $arr_author_image_id[$i] );
															    echo '<div class="imgg">
																		<div><img alt="" class="testimonial_thumnail" src="'.$src[$i].'"></div>
																		<p class="testimonial_author">'.$arr_author[$i].', </p><p class="testimonial_designation">'.$arr_designation[$i].'</p>
																	  </div>
															</div>';
													}
										echo '</div>';											
											echo '<!--<a data-slide="prev" href="#testimonialCarousel" class="left carousel-control">
														<span class="glyphicon glyphicon-chevron-left"></span>
													  </a>
													  <a data-slide="next" href="#testimonialCarousel" class="right carousel-control">
														<span class="glyphicon glyphicon-chevron-right"></span>
													  </a>-->
									</div>
								</section>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>';
		}
	}elseif($indicators=='false'){
			   if(sizeof($arr_testimonial_text)>1||sizeof($arr_author_image_id)>1||sizeof($arr_author)>1||sizeof($arr_designation)>1){ 
				echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
					<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>      				
					<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" type="text/css" rel="stylesheet">
			 <div class="tp-caption emp_details_slider">
				<div class="container" id="employee-details-slider">
					<div class="row">
						<div class="col-md-12">
							<section class="text-center" id="testimonials">
								<div data-ride="carousel" class="carousel slide" id="testimonialCarousell" data-ride="carousel"><h3>We love Grazitti</h3>
									<div class="carousel-inner center-block">'; 
										for($i=0;$i<sizeof($arr_testimonial_text);$i++){ 
											if($i==0){
												echo '<div class="item active">';
											}else{
												echo '<div class="item">';
											}
												echo '<div class="testimonial_text">
													<p><img alt="logo-icon" src="/wp-content/uploads/2015/07/logo-icon.png">'.$arr_testimonial_text[$i].'</p>
												</div>';	
												$src[$i] = wp_get_attachment_url( $arr_author_image_id[$i] );
												echo '<div class="imgg">
														<div><img alt=""  class="testimonial_thumnail" src="'.$src[$i].'"></div>
																		<p class="testimonial_author">'.$arr_author[$i].', </p><p class="testimonial_designation">'.$arr_designation[$i].'</p>
													</div>
													</div>';
										}
								echo '</div>					
							</div>';
							echo '<!--<a data-slide="prev" href="#testimonialCarousel" class="left carousel-control">
											<span class="glyphicon glyphicon-chevron-left"></span>
										</a>
										<a data-slide="next" href="#testimonialCarousel" class="right carousel-control">
											<span class="glyphicon glyphicon-chevron-right"></span>
									</a>-->
								</div>
							</section>
						</div>
					</div>
				</div>
			</div>';
			}
		  }else{
		   $src = wp_get_attachment_url( $author_image_id );
		   echo '
			<div class="tp-caption empslider">
				<div class="container" id="employee-slider">
					<div class="row">
						<div class="col-md-12">
							<section class="text-center" id="testimonials">
								<div data-ride="carousel" class="carousel slide" id="testimonialCarousel">
									<div class="carousel-inner center-block">
										<div class="item">
											<div class="testimonial_text">
												<p><img alt="white-quote" src="/wp-content/uploads/2016/06/white-quote.png">'.$testimonial_text.'</p>
											</div>
											<div class="imgg">
												<img alt="white quote" class="testimonial_thumnail" src="'.$src.'">
												<p class="testimonial_author"><span>'.$author.'</span>, '.$designation.'</p>
											</div>		
										</div>					
									</div>
									<!--<a data-slide="prev" href="#testimonialCarousel" class="left carousel-control">
											<span class="glyphicon glyphicon-chevron-left"></span>
										</a>
										<a data-slide="next" href="#testimonialCarousel" class="right carousel-control">
											<span class="glyphicon glyphicon-chevron-right"></span>
									</a>-->
								</div>
							</section>
						</div>
					</div>
				</div>
			</div>';
		   }
	}
/*-----End-----*/
/*-----Shortcode for showing limited tags on Blog Detail Page-----*/
add_shortcode('term_tag_blog_single','limit_to_tags');
function limit_to_tags() {
	global $post;
	$term_list = wp_get_post_terms($post->ID, 'post_tag', array("fields" => "all",'orderby' => 'name','order' => 'ASC'));
	if(!empty($term_list)){
		echo '<h3 class="box-title blog-box-title"><span><i class="icon-tag"></i>Tags:</span></h3><div class="tagcloud blog-tagcloud">';
		foreach($term_list as $tl){echo '<a href="'.get_tag_link($tl->term_id).'">'.$tl->name.'</a>';}
		echo '</div>';
	}
}
add_shortcode('term_tag_blog_single_new','limit_to_tags_new');
function limit_to_tags_new() {
	global $post;
	$term_list = wp_get_post_terms($post->ID, 'post_tag', array("fields" => "all",'orderby' => 'name','order' => 'ASC'));
	if(!empty($term_list)){
		echo '<h3 class="box-title blog-box-title"><span class="fa fa-tag font-18"></span><span>Tags:</span></h3><div class="tagcloud blog-tagcloud">';
		foreach($term_list as $tl){
			$tag_color = get_field( 'color', $tl );
			$style='';
			if($tag_color != ''){
				$style="background-color:' . $tag_color . ';" ;
			}
			echo '<a class="mr-1" ' . $style . ' href="'.get_tag_link($tl->term_id).'">'.$tl->name.'</a>';
		}
		echo '</div>';
	}
} 
/*-----End-----*/
/*-----Adding Meta Boxes under posts of Career Custom Post Type 27-05-2016-----*/
add_action( 'add_meta_boxes', 'add_career_metaboxes' );
function add_career_metaboxes(){add_meta_box( 'my-meta-box-experience-id', 'Experience', 'career_metaboxes', 'career', 'normal', 'high' );}
function career_metaboxes(){  global $post;
	$meta_box_text = get_post_meta( $post->ID, 'experience', true );?>
<input type="text" name="experience" id="experience" placeholder="eg. Fresher / 6 months / 3-4 years "
    style="width:100%;" value="<?php if(isset($meta_box_text)){echo $meta_box_text;}else{ echo ''; } ?>" /><?php    
}
add_action( 'add_meta_boxes', 'add_career_metaboxes_vacancy' );
function add_career_metaboxes_vacancy(){
	add_meta_box( 'my-meta-box-vacancy-id', 'Vacancy Closed Date', 'career_metaboxes_vacancy', 'career', 'normal', 'high' );
}
function career_metaboxes_vacancy(){ 
  global $post;
  $meta_box_text = get_post_meta( $post->ID, 'vacancy_closed_date', true );?>
<input type="date" name="vacancy_closed_date" id="vacancy_closed_date" placeholder="mm-dd-yyyy"
    value="<?php if(isset($meta_box_text)){echo $meta_box_text;}else{ echo ''; } ?>" /><?php    
}
/*-----End-----*/

/*-----Saving Meta Boxes values under posts of Career Custom Post Type 27-05-2016-----*/
add_action('save_post', 'save_details');
	function save_details($post_id){
		global $post;
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {return $post_id;}
		if ($post->post_type == 'career') { // Check to see if Event Type.  
			update_post_meta($post->ID, "vacancy_closed_date", $_POST["vacancy_closed_date"]);
			update_post_meta($post->ID, "experience", $_POST["experience"]);
		}
	}
/*-----End-----*/ 
/*-----Custom validation for submit form(Apply now)-----*/
add_filter( 'wpcf7_validate_text', 'validateform', 10, 2 );
add_filter( 'wpcf7_validate_text*', 'validateform', 10, 2 );
function validateform( $result=NULL, $tag ){
	$type = $tag['type'];
	$name = trim($tag['name']);
	if($name == 'fullname'){
		$the_value = $_POST[$name];		  
		  if(validateform(NULL,$the_value!='')){
			if(!preg_match('/^[a-zA-Z -]+$/', $the_value)){ $result->invalidate( $name, "Please enter a valid name" ); }
		    if(is_numeric($the_value)){$result->invalidate( $name, "Please enter characters only" );}
	    }
	}
	else if($name == 'firstname'){
		$the_value = $_POST[$name];		  
		  if(validateform(NULL,$the_value!='')){
			if(!preg_match('/^[a-zA-Z -]+$/', $the_value)){$result->invalidate( $name, "Please enter a valid name" );}
		    if(is_numeric($the_value)){$result->invalidate( $name, "Please enter characters only" );}
	    }
	}
    else if($name == 'LastName'){		
		$the_value = $_POST[$name];
		if(validateform(NULL,$the_value!='')){
			if(!preg_match('/^[a-zA-Z -]+$/', $the_value)){$result->invalidate( $name, "Please enter a valid name" );}
			if(is_numeric($the_value)){$result->invalidate( $name, "Please enter characters only" );}
	    }
	}
	else if($name == 'city'){
		$the_value = $_POST[$name];		
		if(validateform(NULL,$the_value!='')){
			if(!preg_match('/^[a-zA-Z -]+$/', $the_value)){$result->invalidate( $name, "Please enter characters only" ); }
			if(is_numeric($the_value)){$result->invalidate( $name, "Please enter characters only" );}
	    }
	}
	else if($name == 'state'){
		$the_value = $_POST[$name];
		if(validateform(NULL,$the_value!='')){
			if(!preg_match('/^[a-zA-Z -]+$/', $the_value)) {$result->invalidate( $name, "Please enter characters only" );}
			if(is_numeric($the_value)){$result->invalidate( $name, "Please enter characters only" );}
	    }
	}
	else if($name == 'qualification'){
		$the_value = $_POST[$name];
		if(validateform(NULL,$the_value!='')){
			if(is_numeric(substr($the_value, 0, 1))) {$result->invalidate( $name, "Please enter characters only" ); }
		}
	}
	else if($name == 'Currentjob'){		
		$the_value = $_POST[$name];
		if(validateform(NULL,$the_value!='')){
			if(!preg_match('/^[a-zA-Z -]+$/', $the_value)){ $result->invalidate( $name, "Please enter characters only" ); }
	    }
	}
	else if($name == 'expectedsalary'){
		$the_value = $_POST[$name];
		if(validateform(NULL,$the_value!='')){					
			if(!preg_match('/^\d+\.?\d*$/', $the_value)) {$result->invalidate( $name, "Please enter digits only" );}
		}
	}
	else if($name == 'totalexperience'){
		$the_value = $_POST[$name];
		if(validateform(NULL,$the_value!='')){
			if(!is_numeric($the_value)) { $result->invalidate( $name, "Please enter digits only" );}
		}
	}
	else if($name == 'noticeperiod'){
		$the_value = $_POST[$name];
		if(validateform(NULL,$the_value!='')){if(!is_numeric($the_value)) { $result->invalidate( $name, "Please enter digits only" ); }}
	}else if($name == 'phone'){		
		$the_value = $_POST[$name];		
		if(validateform(NULL,$the_value!='')){			
			if(!preg_match('/^[\+0-9\-\(\)\s]*$/', $the_value)){$result->invalidate( $name, "Please enter digits only" );}
		}	
	}else if($name == 'currentctc'){		
		$the_value = $_POST[$name];		
		if(validateform(NULL,$the_value!='')){			
			if(!preg_match('/^[\+0-9\-\(\)\s]*$/', $the_value)){$result->invalidate( $name, "Please enter digits only" );}
		}	
	}else if($name == 'mobile'){		
		$the_value = $_POST[$name];		
		if(validateform(NULL,$the_value!='')){			
			if(!preg_match('/^[0-9]{10}$/', $the_value)){	$result->invalidate( $name, "Please enter valid phone number" );}
		}	
	}else if($name == 'company'){		
		$the_value = $_POST[$name];
		if(validateform(NULL,$the_value!='')){
			if(!preg_match('/^[a-zA-Z -]+$/', $the_value)){ $result->invalidate( $name, "Please enter characters only" );}
	    } 
	}else if($name == 'country'){		
		$the_value = $_POST[$name];
		if(validateform(NULL,$the_value!='')){
			if(!preg_match('/^[a-zA-Z -]+$/', $the_value)){  $result->invalidate( $name, "Please enter characters only" ); }
	    }
	}
	 			return $result;
}

function shiftnav_blog_register_theme_locations() {
	register_nav_menu( 'shiftnav_blog', __( 'ShiftNav [Blog]' ) );
}
add_action( 'init', 'shiftnav_blog_register_theme_locations' );

/**
 * This function is used for save data regarding donload histroy
 * This ajax action call when some fill the marketo form from selenium page
 */
add_action( 'wp_ajax_download-file-initialize', 'downloadfileInitialize' );
add_action( 'wp_ajax_nopriv_download-file-initialize', 'downloadfileInitialize' );
function downloadfileInitialize(){
	global $wpdb;
	$table = $wpdb->prefix."download_requests";
	$email=base64_decode($_REQUEST['e']);
	$slug = $_REQUEST['slug'];
	if($slug=='selenium-data-driven-framework'){$version = '3';}
	else {	$version = '4';	}
	$checkData = $wpdb->get_row( "SELECT * FROM ".$table." WHERE email_id = '".$email."' AND version = '".$version."' LIMIT 1" );
	//$checkData = $wpdb->get_row( "SELECT * FROM ".$table." WHERE email_id = '".$email."'  LIMIT 1" );
	
	if($checkData){		
		$data = array('is_download'=>'0');
		$where = array('email_id'=>$email,'version' => $version);
		//$where = array('email_id'=>$email);
		$wpdb->update( $table, $data,$where );		
	} else {		
		$data = array('email_id'=>$email,'is_download'=>'0','version' => $version);
		//$data = array('email_id'=>$email,'is_download'=>'0');
		$wpdb->insert( $table, $data );	
	}	
}

add_action( 'wp_ajax_download-file', 'downloadfile' );
add_action( 'wp_ajax_nopriv_download-file', 'downloadfile' );
function downloadfile(){
	apache_setenv('no-gzip', 1);
	ini_set('zlib.output_compression', 0);	
	global $wpdb;
	$table = $wpdb->prefix."download_requests";
	$email=$_REQUEST['email'];
	/** Checking if user already download file or not **/
	$checkData = $wpdb->get_row( "SELECT * FROM ".$table." WHERE email_id = '".$email."' LIMIT 1" );	
	if($checkData){		
		/** Checking if is_download flag **/
		if($checkData->is_download=='0'){			
			$data = array('is_download'=>'1');
			$where = array('email_id'=>$email);
			$wpdb->update( $table, $data,$where );		
			$filename = "sliced Images.zip";	
			$upload_dir = wp_upload_dir();	
			$filepath = $upload_dir['basedir'];				
			$pdf_path = $filepath.'/'.$filename;			
			header('Content-Description: File Transfer');
			header("Content-Type: application/zip");
			header('Content-Disposition: attachment; filename="'.basename($pdf_path).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($pdf_path));
			ob_clean();
			readfile($pdf_path);			
		} else {
			$redirectUrl = site_url('products/selenium-hybrid-framework/');
			echo '<script>window.location.href = "'.$redirectUrl.'";</script>';			
		}
	} else {		
		$redirectUrl = site_url('products/selenium-hybrid-framework/');
		echo '<script>window.location.href = "'.$redirectUrl.'";</script>';
	}	
}
/*End*/
/*Function for Removing Automatic feed links from header*/
function my_remove_feeds() {
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'feed_links', 2 );
}
add_action( 'after_setup_theme', 'my_remove_feeds' );
/*End*/
add_shortcode('iframe', 'ag_iframe');
function ag_iframe($atts, $content) {
 if (!$atts['width']) { $atts['width'] = 630; }
 if (!$atts['height']) { $atts['height'] = 1500; }

 return '<iframe border="0" class="shortcode_iframe" src="' . $atts['src'] . '" width="' . $atts['width'] . '" height="' . $atts['height'] . '"></iframe>';
}

/**
 * Function name:       theme_js
 * Function arguments:  NA
 * Function purpose:    Adding js file in header for imageMapResizer.    
 **/	
function theme_js() {
	 global $post;
	 if($post->post_type == 'articles'){
		wp_enqueue_script( 'imageMapResizer', get_template_directory_uri() . '/js/imageMapResizer.min.js', array( 'jquery' ), '1.0', true );
	 }
}
add_action('wp_enqueue_scripts', 'theme_js');
global $jal_db_version;
$jal_db_version = '1.0';

/*
function exit_install() {
 global $wpdb;
 global $jal_db_version;

 $table_name = $wpdb->prefix . 'exitfeedback';
 
 $charset_collate = $wpdb->get_charset_collate();

 $sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  status tinytext NOT NULL,
  role varchar(55) DEFAULT '' NOT NULL,
  comment varchar(55) DEFAULT '' NOT NULL,
  PRIMARY KEY  (id)
 ) $charset_collate;";

 require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
 dbDelta( $sql );

 add_option( 'jal_db_version', $jal_db_version );
}

function exit_form_data() {
 $wpcf =  WPCF7_Submission::get_instance();
 $formData['posted_data'] = $wpcf->get_posted_data();
 $data['role']=$formData['posted_data']['role']; 
 $data['comment']= $formData['posted_data']['textarea-717'];
 $data['status']=$formData['posted_data']['yes'][0];
 return $data;
}

function exit_popup_data() {
 $data= exit_form_data();
 global $wpdb;
 
 $role = $data['role'];
 $comment = $data['comment'];
 $status = $data['status'];
 
 $table_name = $wpdb->prefix . 'exitfeedback';
 
 $wpdb->insert( 
  $table_name, 
  array( 
   'time' => current_time( 'mysql' ), 
   'status' => $status, 
   'role' => $role, 
   'comment' => $comment, 
  ) 
 );
}

add_action( 'wpcf7_before_send_mail', 'exit_form_data' );
add_action( 'wpcf7_before_send_mail', 'exit_install' );
add_action( 'wpcf7_before_send_mail', 'exit_popup_data' );*/

add_action ('admin_init','add_sub_caps');
 
function add_sub_caps() {
    global $wp_roles;
    $role = get_role('subscriber');
    $role->add_cap('tt_add_menu_items');
}
function rw_image_resize( $attachment_id, $width, $height, $crop = true ){
    $path = get_attached_file( $attachment_id );
    if ( ! file_exists( $path ) )   {    return false;  }

    $upload    = wp_upload_dir();
    $path_info = pathinfo( $path );
    $base_url  = $upload['baseurl'] . str_replace( $upload['basedir'], '', $path_info['dirname'] );
    $meta = wp_get_attachment_metadata( $attachment_id );
    foreach ( $meta['sizes'] as $key => $size ){
        if ( $size['width'] == $width && $size['height'] == $height ){
            return "{$base_url}/{$size['file']}";
        }
    }

    // Generate new size
    $resized = image_make_intermediate_size( $path, $width, $height, $crop );
    if ( $resized && ! is_wp_error( $resized ) )
    {
        // Let metadata know about our new size.
        $key                 = sprintf( 'resized-%dx%d', $width, $height );
        $meta['sizes'][$key] = $resized;
        wp_update_attachment_metadata( $attachment_id, $meta );
        return "{$base_url}/{$resized['file']}";
    }

    // Return original if fails
    return "{$base_url}/{$path_info['basename']}";
}
/*
Function Name:  save_contact_form
Function Params: N/A 
Functions purpose: Saving Form data in database.
*/
function save_contact_form(){
	$wpcf =  WPCF7_Submission::get_instance();
	$formData = $wpcf->get_posted_data();	
	global $wpdb;	
	$table_name = $wpdb->prefix . '_cf_send_resume';
	if($formData['res-your-name']):
	$wpdb->insert($table_name,array('name' => $formData['res-your-name'], 'email' => $formData['res-your-email'], 'resume' => $formData['res_file'],));	
	endif;
}
add_action( 'wpcf7_before_send_mail', 'save_contact_form' ); 
/*------REGISTER widget-------*/
require_once 'register-widget.php';
/*------End-------*/
	if (!(is_admin() )) {
    function defer_parsing_of_js ( $url ) {
        if ( FALSE === strpos( $url, '.js' ) ) return $url;
        if ( strpos( $url, 'jquery.js' ) || strpos( $url, 'ubermenu.min.js' ) || strpos( $url, 'shiftnav.min.js' ) || strpos( $url, 'custom-js.js' ) || strpos( $url, 'bootstrap-multiselect.js' ) || strpos( $url, 'jquery.touchwipe.js' ) || strpos($url, 'jquery.tipsy.js') || strpos($url, 'jquery.themepunch.tools.min.js')|| strpos($url, 'jquery.themepunch.revolution.min.js') || strpos($url, 'owl.carousel.min.js') || strpos($url, 'testimonial.js') || strpos($url, 'exit-popup.js')  || strpos($url, 'imageMapResizer.min.js') || strpos($url, 'bootstrap.min.js') || strpos($url, 'scripts.js') ) return $url;
        // return "$url' defer ";
        return "$url' defer async onload='";
    }
    add_filter( 'clean_url', 'defer_parsing_of_js', 11, 1 );
	}

	/* if (!(is_admin() )) {
    function defer_parsing_of_css ( $url ) {
        if ( FALSE === strpos( $url, '.css' ) ) return $url;
        if ( strpos( $url, 'style.css' ) )  return "$url' defer async onload='";

        // return "$url' defer ";
        return "$url' defer async onload='";
    }
    add_filter( 'clean_url', 'defer_parsing_of_css', 10, 2 );
	} */

	/*function to add async to all scripts*/
	/*
function js_async_attr($tag){

# Add async to all remaining scripts
return str_replace( ' src', ' async="async" defer="defer" src', $tag );
}
add_filter( 'script_loader_tag', 'js_async_attr', 10 );*/
function add_oembed_soundcloud(){
wp_oembed_add_provider( 'http://soundcloud.com/*', 'http://soundcloud.com/oembed' );
wp_oembed_add_provider( '#http://(www\.)?youtube\.com/watch.*#i', 'http://www.youtube.com/oembed', true );
wp_oembed_add_provider( '#http://(www\.)?vimeo\.com/.*#i', 'http://vimeo.com/api/oembed.{format}', true );
}
add_action('init','add_oembed_soundcloud');

add_filter( 'widget_text', array( $wp_embed, 'run_shortcode' ), 8 );
add_filter( 'widget_text', array( $wp_embed, 'autoembed' ), 8 );
	// Before VC Init
add_action( 'vc_before_init', 'vc_before_init_actions' );
 
function vc_before_init_actions() {     
    // Link your VC elements's folder
    if( function_exists('vc_set_shortcodes_templates_dir') ){      
        vc_set_shortcodes_templates_dir( get_stylesheet_directory_uri() . '/vc-templates' );         
    }     
}

add_action( 'wp_print_styles', 'deregister_my_styles', 100 );
 function deregister_my_styles() {
	 if(is_front_page()){
		  wp_deregister_style('flash_sale_shortcodes');  
		  wp_deregister_style('contact-form-7'); 
		  wp_deregister_style('override-css'); 
		  wp_deregister_style('dynamo-style'); 
		  wp_deregister_style('desktop-small-css'); 
	 }	
}

function wpdocs_dequeue_script() {
   wp_dequeue_script( 'frontend-js' );
   wp_dequeue_script( 'jm_like_post' );
   wp_dequeue_script( 'vc-plugin-js' );
   wp_dequeue_script( 'jquery-noconflict-js' );
   wp_dequeue_script( 'ubermenu-custom' );
   wp_dequeue_script( 'dsq_count_script' );
}
add_action( 'wp_print_scripts', 'wpdocs_dequeue_script', 100 );

global $shortname;

# Register custom post type
/* Webinar custom post type */
$labels = array('name' => _x('Webinar', 'post type general name', DPTPLNAME),
    'singular_name' => _x('Webinar', 'post type singular name', DPTPLNAME),
    'add_new' => _x('Add New', 'webinar', DPTPLNAME),
    'add_new_item' => __('Add New Webinar Item', DPTPLNAME),
    'edit_item' => __('Edit Webinar Item', DPTPLNAME),
    'new_item' => __('New Webinar Item', DPTPLNAME),
    'view_item' => __('Preview Webinar Item', DPTPLNAME),
    'search_items' => __('Search Webinar', DPTPLNAME),
    'not_found' => __('No webinar found', DPTPLNAME),
    'not_found_in_trash' => __('No webinar items found in Trash.', DPTPLNAME),
	'parent_item_colon' => '',    'menu_name' => 'Webinar'
);

register_post_type('webinar', array( 'label' => __('Webinar', DPTPLNAME),  'labels' => $labels,'singular_label' => __('Webinar', DPTPLNAME),
    'public' => true, 'show_ui' => true, 'show_in_menu' => true,'menu_position' => null, '_builtin' => false, 'exclude_from_search' => false, 
    'capability_type' => 'page','hierarchical' => false,'rewrite' => array("slug" => "resources/webinars", 'with_front' => false), 
    'query_var' => "webinar", 'supports' => array('title', 'thumbnail', 'page-attributes', 'editor', 'comments','excerpt'),'menu_icon' => ''));
/* END */

/* Podcast custom post type */
/* $labels = array(
	'name' => _x('Podcast', 'post type general name', DPTPLNAME),
    'singular_name' => _x('Podcast', 'post type singular name', DPTPLNAME),
    'add_new' => _x('Add New', 'podcast', DPTPLNAME),
    'add_new_item' => __('Add New Podcast Item', DPTPLNAME),
    'edit_item' => __('Edit Podcast Item', DPTPLNAME),
    'new_item' => __('New Podcast Item', DPTPLNAME),
    'view_item' => __('Preview Podcast Item', DPTPLNAME),
    'search_items' => __('Search Podcast', DPTPLNAME),
    'not_found' => __('No podcast found', DPTPLNAME),
    'not_found_in_trash' => __('No podcast items found in Trash.', DPTPLNAME),
	'parent_item_colon' => '',
    'menu_name' => 'Podcast'
);

register_post_type('podcast', array(
    'label' => __('Podcast', DPTPLNAME),
    'labels' => $labels,
    'singular_label' => __('Podcast', DPTPLNAME),
    'public' => true,
    'show_ui' => true, 
	'show_in_menu' => true,
	'show_in_rest' => true,
	'rest_base' => 'podcast',
	'menu_position' => null, 
    '_builtin' => false, 
    'exclude_from_search' => false, 
    'capability_type' => 'page',
    'hierarchical' => false,
	'rewrite' => array("slug" => "resources/podcasts", 'with_front' => false), 
    'query_var' => "podcast", 
    'supports' => array('title', 'thumbnail', 'page-attributes', 'editor', 'comments','excerpt'),
    'menu_icon' => ''
)); */

##############################################################
# Register associated taxonomy
##############################################################
/* Webinar Taxonomy */
function register_webinar_taxonomy($args_webinar) {
$labels_webinar = array(
    'name' => __('Webinar Categories', 'post type general name'),
    'all_items' => __('All Categories', 'all items'),
    'add_new_item' => __('Add New Category', 'adding a new item'),
    'new_item_name' => __('New Category Name', 'adding a new item'),
);
$args_webinar = array('labels' => $labels_webinar, 'hierarchical' => true);
register_taxonomy( 'webinars', 'webinar', $args_webinar );
}
add_action("init", "register_webinar_taxonomy",1); 
/* END */

/* Podcast Taxonomy */
add_action("init", "register_podcast_taxonomy",1);
 function register_podcast_taxonomy($args_podcast) {
$labels_podcast = array(
    'name' => __('Podcast Categories', 'post type general name'),
    'all_items' => __('All Categories', 'all items'),
    'add_new_item' => __('Add New Category', 'adding a new item'),
    'new_item_name' => __('New Category Name', 'adding a new item'),
);
$args_podcast = array('labels' => $labels_podcast,'hierarchical' => true);
register_taxonomy( 'podcasts', 'podcast', $args_podcast );
} 

/* END */

/*Start include category in post type permalink*/

/* END */

##############################################################
# Customize Manage Posts interface
##############################################################

function edit_columns_webinar($columns) {    
    $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => __("Webinar Item Title", DPTPLNAME),
		"date" => __("Date", DPTPLNAME),
        "pcategories" =>__("Categories", DPTPLNAME),
        "webinar_image" => __("Image", DPTPLNAME)
    );
    return $columns;
}

function custom_columns_webinar($column) {
    global $post;
   switch ($column) {
        case "webinar_image":
		if (has_post_thumbnail()) { $imageurl = wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>
<img alt="" src="<?php echo $imageurl ?>" height="120" />
<?php }
			break;
        case "pcategories":
            
            $pcategories = get_the_terms(0, 'webinars');
			$pcategories_html = array();
			if (!is_wp_error($pcategories) && !empty($pcategories)) {
			    foreach ($pcategories as $pcategory) {
			        $pcategories_html[] = $pcategory->name;
			    }
			    echo implode(', ', $pcategories_html);
			}

            break;
    }
}
add_filter("manage_edit-webinar_columns", "edit_columns_webinar");
add_action("manage_posts_custom_column", "custom_columns_webinar");

function dp_child_excerpt($text) {
   return str_replace('[&hellip;]', '<a class="read_more_press read-more-image" href="'.get_permalink().'">'.__( 'Read more', DPTPLNAME ).'</a>', $text); 
}
add_filter('the_excerpt', 'dp_child_excerpt');
/*
function advanced_search_url_rewrite() {
 if ( is_search() && ! empty( $_GET['s'] ) ) { 
  $index = '1';
  $searchString = $_GET['s']; 
  $from ='0';
  $cookie = '1502180166968_112.196.45.10_231';
  $userip =  $_SERVER['REMOTE_ADDR'];
  $dateClient = '1502190631727';
   $url = "/search-results/#/search?index=".$index."&searchString=".rawurlencode($searchString)."&from=".$from."&sortby=&pageNo=&aggregations=&withoutTheWords=&exactPhrase=&withOneOrMore=".rawurlencode($searchString)."&generateSearchClientID=&platformId=&cookie=&userip=".$userip."&dateClient=".$dateClient."&pagesize=";
  wp_redirect( home_url($url ));
 }
} 
add_action( 'template_redirect', 'advanced_search_url_rewrite' ); */

/**
 * Filter the excerpt "read more" string.
 *
 * @param string $more "Read more" excerpt string.
 * @return string (Maybe) modified "read more" excerpt string.
 */
/* function wpdocs_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'wpdocs_excerpt_more' ); */

/*Infinite Search Posts Scrolling*/

add_action('wp_ajax_load_search_posts_ajax', 'load_search_posts_ajax');
add_action('wp_ajax_nopriv_load_search_posts_ajax', 'load_search_posts_ajax');
function load_search_posts_ajax(){
	global $post; 
	$paged = $_REQUEST['paged'];
	$s = $_REQUEST['s'];
	$display_count = 10;
	// After that, calculate the offset
	$offset = ( $paged - 1 ) * $display_count;		
	$args=array('post_type' => array('post','page','press-releases','articles'), 'post_status'=>'publish', 'paged' => $paged, 'offset' => $offset, 's' => $s, 'posts_per_page'	=> 10);
	$ar = new WP_Query( $args );
	if($ar->have_posts()) :
	while ( $ar->have_posts() ) { $ar->the_post();		
	    if($post->post_title){ ?>
<article class="post-<?php echo $post->ID; ?> testimonial type-testimonial status-publish hentry large"
    id="post-<?php echo $post->ID; ?>">
    <div class="date-container">
        <div class="inner">
            <dt class="month">
                <h4>Apr</h4>
            </dt>
            <dt class="day">
                <h2>13</h2>
            </dt>
            <dt class="like"><a title="Like" data-post_id="<?php echo $post->ID; ?>" class="jm-post-like" href="#"><i
                        class="icon-heart-2"></i>&nbsp;0</a><span class="jm-load"></span></dt>
        </div>
    </div>
    <div class="shifted-content">
        <div class="meta"></div>
        <header>
            <h3>
                <a rel="bookmark" title="Permalink to <?php echo $post->post_title;?>"
                    href="<?php echo post_permalink($post->ID); ?>">
                    <?php echo $post->post_title; ?> </a>
            </h3>
        </header>

    </div>
</article><?php 
		} 
		}		
		wp_reset_query();
		else: // Else
			echo 'postscompleted';
		endif; // End if 
		 wp_die();   
} 

/*-------Press Releases--------*/
/* add_action( 'init', 'Press_Releases_post_type' );
function Press_Releases_post_type() {
 register_post_type( 'Press-Releases',
  array(
   'labels' => array(
    'name' => __( 'Press Releases' ),
    'singular_name' => __( 'Press Releases' )
   ),
   'hierarchical' => true,
  'taxonomies' => array(''),
  'public' => true,
  'has_archive' => true,
	'rewrite' => array("slug" => "press-releases", 'with_front' => false),
  'supports' => array( 'thumbnail', 'editor','custom-fields','title','page-attributes' ),
  )
 );
} */ 
/*--------End---------------*/
function gp_remove_cpt_slug_anil( $post_link, $post, $leavename ) {     if ( ('press-releases' != $post->post_type && 'articles' != $post->post_type) || 'publish' != $post->post_status ) {        return $post_link;    }     $post_link = str_replace( '/' . $post->post_type . '/', '/resources/' . $post->post_type . '/', $post_link );     return $post_link;}add_filter( 'post_type_link', 'gp_remove_cpt_slug_anil', 10, 3 );
/*---------Articles---------*/
/*  add_action( 'init', 'Articles_post_type' );
function Articles_post_type() {	
add_rewrite_rule('^resources/press-releases/page/([^/]*)/?', 'index.php?page_id=15776&paged=$matches[1]', 'top');
add_rewrite_rule('^resources/press-releases/([^/]*)/?', 'index.php?post_type=press-releases&name=$matches[1]', 'bottom'); 
add_rewrite_rule('^resources/articles/page/([^/]*)/?', 'index.php?page_id=15840&paged=$matches[1]', 'top');
add_rewrite_rule('^resources/articles/([^/]*)/?', 'index.php?post_type=articles&name=$matches[1]', 'bottom');
 register_post_type( 'Articles',
  array(
   'labels' => array(
    'name' => __( 'Articles' ),
    'singular_name' => __( 'Articles' )
   ),
   'hierarchical' => true,
  'taxonomies' => array('category'),
  'public' => true,
  'has_archive' => true,
	'rewrite' => array("slug" => "articles", 'with_front' => false),
  'supports' => array( 'thumbnail', 'editor','custom-fields','title','page-attributes' ),
  )
 );
} */  
/*-------Eoc----------*/

/*-----------Soc shortcode for showing comments box in visual composer of Page in Selenium Pages----------*/
	add_shortcode( 'leavereply', 'leave_reply' );
	function leave_reply(){	comments_template();}
/*-----------Eoc---------*/

/*-------Soc for fixing the warning coming with activating Email Subscriber Plugin 21-03-16--------*/
	add_filter( 'wp_mail_content_type', 'set_html_content_type' );
	function set_html_content_type() {return 'text/html';}
/*--------Eoc--------*/

/*-------Sumbit request a meeting to marketo--------*/
	/*function proquickStartCommunities() {
		$params = array();
		parse_str($_REQUEST['formdata'], $params); 
			if(isset($params['your_email'])){
				include_once('marketo_tracking_api.php');
							
				$attrs = array();
				if($params['community_type']){
					$attrs['Type_of_Community__c'] = $params['community_type'];
				}
				if($params['package']){
					$attrs['Quick_Start_Package_Details__c'] = $params['package'];
				}
				if($params['column_layout']){
					$attrs['column_layout'] =  $params['column_layout'];
				}
				if($params['Currently_Using_Marketing_Automation__c']){
					$attrs['Currently_Using_Marketing_Automation__c'] = $params['Currently_Using_Marketing_Automation__c'];
				}
				if($params['Currently_Using_CRM__c']){
					$attrs['Currently_Using_CRM__c'] = $params['Currently_Using_CRM__c'];
				}
				if($params['i_njoy']){
					$attrs['Primary_Community_Challenge__c'] = implode(";", $params['i_njoy']);
				}
				if($params['i_wantto']){
					$attrs['Communities_Objective__c'] = implode(";", $params['i_wantto']);
				}
			
				if($params['custom_url_in'] && $params['column_layout']=='Custom Layout'){
					$attrs['custom_url_in'] = $params['custom_url_in'];
				}
						
				if($params['Challenges_in_Sales_Process__c']){
					$attrs['Challenges_in_Sales_Process__c'] = implode(";", $params['Challenges_in_Sales_Process__c']);
				}
				if($params['Currently_using_Sales_Optimization_Platf__c']){
					$attrs['Currently_using_Sales_Optimization_Platf__c'] = implode(";", $params['Currently_using_Sales_Optimization_Platf__c']);
				}
			
				if($params['Support_Obstacles__c']){
					$attrs['Support_Obstacles__c'] = implode(";", $params['Support_Obstacles__c']);
				}
				if($params['Support_Investment_Focus__c']){
					$attrs['Support_Investment_Focus__c'] = implode(";", $params['Support_Investment_Focus__c']);
				}
				if($params['Currently_using_Support_Platform__c']){
					$attrs['Currently_using_Support_Platform__c'] = $params['Currently_using_Support_Platform__c'];
				}
				if($params['Support_Setup_Budget__c']){
					$attrs['Support_Setup_Budget__c'] = $params['Support_Setup_Budget__c'];
				}
			
				if($params['Community_Obstacles__c']){
				   $attrs['Community_Obstacles__c'] = implode(";", $params['Community_Obstacles__c']);
				}
				if($params['Currently_Using_CRM_Support_Ticket_Soft__c']){
				   $attrs['Currently_Using_CRM_Support_Ticket_Soft__c'] = implode(";", $params['Currently_Using_CRM_Support_Ticket_Soft__c']);
				}
				if($params['Currently_using_Community_Platform__c']){
				  $attrs['Currently_using_Community_Platform__c'] = $params['Currently_using_Community_Platform__c'];
				}
				if($params['Currently_Using_CRM_Support_Ticket_Soft__c']){
				  $attrs['Currently_Using_CRM_Support_Ticket_Soft__c'] = $params['Currently_Using_CRM_Support_Ticket_Soft__c'];
				}
			
				$attrs['FirstName'] =$params['first_name'];
				$attrs['LastName'] = $params['last_name'];
				$attrs['Email'] = $params['your_email'];
				$attrs['Phone'] = $params['your_phone'];					
				$accessKey = "grazittiproduction1_07507189544E1F2A8C84D4";
				$secretKey = "5771595430937121554444EE11FF22BC88CC89E83460";    
				$soapEndPoint = "https://789-BYX-800.mktoapi.com/soap/mktows/3_1";
				$connection = new mktMktowsClient($accessKey, $secretKey, $soapEndPoint);
				$key = $_REQUEST['your_email'];				
				$data=$connection->syncLeadNew($key, $attrs);					
				$status=$data->result->syncStatus->status;
			}else{
				$status="Error";
			}
		echo $status;
		}
	add_action('wp_ajax_proquickStartCommunities', 'proquickStartCommunities');
	add_action('wp_ajax_nopriv_proquickStartCommunities', 'proquickStartCommunities'); */
/*-----End----*/

/*------Added Sidebar for Quick Start Communities-------*/
/* register_sidebar(array(
  'name' => 'Request Meeting',
  'before_widget' => '<li id="%1$s" class="request_meeting-container %2$s">',
  'after_widget'  => '</li>',
  'before_title'  => '<h3 class="request_meeting-title">',
  'after_title'  => '</h3>',
 )); */
/*------End-------*/

/*------Page Slug Body Class------*/
	function add_slug_body_class( $classes ) {
		global $post;
		if((is_page('11440'))||(is_page('11786'))||(is_page('11634'))||(is_page('12212'))||(is_page('12382'))){$classes[] = 'common-selenium';}
		if(is_page('11786')){$classes[] = 'sel-data-driven';}
		if(is_page('11634')){$classes[] = 'sel-hybrid';	}
		if(is_page('12212')){$classes[] = 'faq-sel-data-driven';}
		if(is_page('12382')){$classes[] = 'faq-sel-hybrid';}
		if((is_page('15776'))||(is_page('15840'))){	$classes[] = 'page-press-articles';	}
		if ( isset( $post ) ) {$classes[] = $post->post_type . '-' . $post->post_name;}
		return $classes;
	}
	add_filter( 'body_class', 'add_slug_body_class' );
/*-------End-------*/

function add_metaboxes() {
	add_meta_box( 'dynamo-slide-setting', __('Slide Setting', DPTPLNAME), 'dynamo_slide_setting_callback', 'slide', 'normal', 'high' );
	add_meta_box( 'dynamo-portfolio-setting', __('Portfolio Item Setting', DPTPLNAME), 'dynamo_portfolio_setting_callback', 'portfolio', 'normal', 'high' );
	add_meta_box( 'dynamo-webinar-setting', __('Webinar Item Setting', DPTPLNAME), 'webinar_setting_callback', 'webinar', 'normal', 'high' );
	add_meta_box( 'dynamo-podcast-setting', __('Webinar Item Setting', DPTPLNAME), 'podcast_setting_callback', 'podcast', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'add_metaboxes' );
 
function webinar_setting_callback($post) { include_once ('dynamo_framework/metaboxes/webinar_meta_box.php'); }
function podcast_setting_callback($post) { include_once ('dynamo_framework/metaboxes/podcast_meta_box.php'); }
add_action( 'init', 'add_shortcode_file');
function add_shortcode_file() {	
include_once ('dynamo_framework/post_types/portfolio.php'); 
include_once ('dynamo_framework/helpers/helpers.shortcodes.php'); 
}
/****************************************mkt lead status****************************************/
	
function curlrequest($url){
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json'));
	$response = json_decode(curl_exec($ch));
	curl_close($ch);
	//$token = $response->access_token;
	return $response;
}	
function csvString($fields){
	$csvString = "";
	$i = 0;
	foreach($fields as $field){
		if ($i > 0){	$csvString = $csvString . "," . $field;	}
		elseif ($i === 0){	$csvString = $field;}	
	    $i++;
	}
	return $csvString;
}
add_action('wp_ajax_lead_status', 'leadStatus');
add_action('wp_ajax_nopriv_lead_status', 'leadStatus');
function leadStatus(){ 
$fields			= "leadStatus,ID";
$clientId     	= "aaca675f-c26f-4caa-bbb3-9813b61d2338";
$clientSecret 	= "ciOQkfDNvHz2T71adk5uINQtN0WL9DP4";
$url = "https://690-NGB-767.mktorest.com/identity/oauth/token?grant_type=client_credentials&client_id=".$clientId."&client_secret=".$clientSecret;
$response		= curlrequest($url);
$access_token	= $response->access_token;
$filterValues 	= $_POST["formData"];  
$url   			= "https://690-NGB-767.mktorest.com/rest/v1/leads.json?access_token=".$access_token."&filterType=email&filterValues=".$filterValues;
if(isset($fields)){	$url = $url."&fields=".$fields;}
$response		= curlrequest($url);
echo json_encode($response);
die;
}

add_action('wp_ajax_lead_status_cookie', 'leadStatusUpdate');
add_action('wp_ajax_nopriv_lead_status_cookie', 'leadStatusUpdate');
function leadStatusUpdate(){ 
$fields			= "leadStatus,ID,new_googleclickid";
$clientId     	= "aaca675f-c26f-4caa-bbb3-9813b61d2338";
$clientSecret 	= "ciOQkfDNvHz2T71adk5uINQtN0WL9DP4";
$url = "https://690-NGB-767.mktorest.com/identity/oauth/token?grant_type=client_credentials&client_id=".$clientId."&client_secret=".$clientSecret;
$response		= curlrequest($url);
$access_token	= $response->access_token;
$gaClientId	=  $_POST['gaClientId']; 
$filterValue	=  $_POST['filterValue']; 
$filterValue	= explode ("token:", $filterValue ); 
$filterValue	= $filterValue[1]; 
$url   			= "https://690-NGB-767.mktorest.com/rest/v1/leads.json?access_token=".$access_token."&filterType=cookies&filterValues=".$filterValue;
if(isset($fields)){
	$url = $url."&fields=".$fields;
}
$response = curlrequest($url);
$decode_res = json_decode(json_encode($response), true);
if(count($decode_res['result']) > 0){
	if($decode_res['result'][0]['new_googleclickid']==NULl){
		//echo "yes null";
		$url = "https://690-NGB-767.mktorest.com/rest/v1/leads.json?access_token=" . $access_token;
		$ch = curl_init($url);
		$body = new stdClass();
		$lead1 = new stdClass();
		$lead1->id = $decode_res['result'][0]['id'];
		$lead1->new_googleclickid = $gaClientId;
		$body->lookupField = 'Id';
		$body->input = array($lead1);
		$requestBody = json_encode($body);
		curl_setopt($ch,  CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json','Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
		curl_getinfo($ch);
		$resp = curl_exec($ch);
	}
}
echo $response = json_encode($response,true);
die;
}
$params['Sales_Cloud_Objective__c']='';
$params['Why_Sales_Cloud__c']='';
if($params['Sales_Cloud_Objective__c']){$attrs['Sales_Cloud_Objective__c'] = implode(";", $params['Sales_Cloud_Objective__c']);}
if($params['Why_Sales_Cloud__c']){$attrs['Why_Sales_Cloud__c'] = implode(";", $params['Why_Sales_Cloud__c']);}
function add_inforgraphic_metabox() {add_meta_box('infographic','Infographic','infographic_fields','articles','side','default');}
add_action( 'add_meta_boxes', 'add_inforgraphic_metabox' ); 
function infographic_fields($object){wp_nonce_field(basename(__FILE__), "inforgraphic-meta-box-nonce");?>
<div>
    <p class="post-attributes-label-wrapper"><label for="meta_form_id">Form ID</label></p>
    <input name="meta_form_id" type="text" value="<?php echo get_post_meta($object->ID, "meta_form_id", true); ?>" />
    <p class="post-attributes-label-wrapper"><label for="meta_pdf_link">PDF url</label></p>
    <input name="meta_pdf_link" type="text" value="<?php echo get_post_meta($object->ID, "meta_pdf_link", true); ?>" />
    <p class="post-attributes-label-wrapper"><label for="meta_bg_image">Background Image url</label></p>
    <input type="text" id="meta_bg_image" name="meta_bg_image"
        value="<?php echo get_post_meta($object->ID, "meta_bg_image", true); ?>" />
</div>
<?php }
function save_infographic_fields($post_id, $post, $update){
	if (!isset($_POST["inforgraphic-meta-box-nonce"]) || !wp_verify_nonce($_POST["inforgraphic-meta-box-nonce"], basename(__FILE__)))
		return $post_id;
	if(!current_user_can("edit_post", $post_id))
		return $post_id;
	if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
		return $post_id;
	$slug = "articles";
	if($slug != $post->post_type)
		return $post_id;
	$meta_form_id_value = "";
	$meta_pdf_link_value = "";
	$meta_bg_image_value = "";
	if(isset($_POST["meta_form_id"])){$meta_form_id_value = $_POST["meta_form_id"];}   
	update_post_meta($post_id, "meta_form_id", $meta_form_id_value);
	if(isset($_POST["meta_pdf_link"])){$meta_pdf_link_value = $_POST["meta_pdf_link"];}   
	update_post_meta($post_id, "meta_pdf_link", $meta_pdf_link_value);
	if(isset($_POST["meta_bg_image"])){$meta_bg_image_value = $_POST["meta_bg_image"];}
	update_post_meta($post_id, "meta_bg_image", $meta_bg_image_value);
}
add_action("save_post", "save_infographic_fields", 10, 3);
remove_action( 'wp_footer', 'exit_popup', 10 );
function exit_popup1() {
	if(is_page('online-communities')){
		if(!isset($_COOKIE['viewedExitPopupWP']) && $_COOKIE['viewedExitPopupWP'] != 'true') {		
			if(esc_attr( get_option('exit_popup_click_outside') ) == "true") {$exit_popup_click_outside = "";} 
			else {
				$exit_popup_click_outside = "
				$('body').on('click', function() {
				$('#exitpopup-modal').hide();
			  });
				";
			}?>
<!-- Exit Popup -->
<div id='exitpopup-modal'>
    <div class='underlay'></div>
    <div class='exitpopup-modal-window'
        style='width:<?php echo esc_attr( get_option('exit_popup_modal_width') ); ?>px !important; height:<?php echo esc_attr( get_option('exit_popup_modal_height') ); ?>px !important;'>
        <div class='modal-title'
            style='background-color:<?php echo esc_attr( get_option('exit_popup_popup_title_color') ); ?> !important;'>
            <h3><?php echo esc_attr( get_option('exit_popup_popup_title') ); ?></h3>
        </div>
        <div class='modal-body'><?php echo do_shortcode(get_option('exit_popup_popup_body')); ?></div>
        <div class='exitpopup-modal-footer'>
            <p><?php echo esc_attr( get_option('exit_popup_popup_footer') ); ?></p>
        </div>
        <?php if (get_option('exit_popup_powered_by') == true) { ?> <div
            style="margin-top:-5px;position:absolute;right:8px;"><a
                style="font-size:x-small;color:white;text-decoration:none;"
                href="https://www.brontobytes.com/blog/exit-popup-free-wordpress-plugin/">Exit Popup for Wordpress</a>
        </div> <?php } ?>
    </div>
</div>
<script>
var _exitpopup = exitpopup(document.getElementById('exitpopup-modal'), {
    aggressive: true,
    timer: 0,
    sensitivity: 20,
    delay: 0,
    sitewide: true,
    cookieExpire: <?php echo esc_attr( get_option('exit_popup_cookie_expire') ); ?>,
    callback: function() {
        //console.log('exitpopup fired!'); 
    }
});

jQuery(window).scroll(function() {
    if (jQuery(window).scrollTop() >= (jQuery(document).height() - jQuery(window).height()) * 0.7) {
        jQuery("#exitpopup-modal").show();
        jQuery('.exit-popup-dev .fancybox-close').click(function() {
            jQuery("#exitpopup-modal").remove();
        });
        jQuery('.exit-popup-dev .popup-btn').click(function() {
            jQuery("#exitpopup-modal").remove();
        });
    }
});
</script>
<!-- End Exit Popup -->
<?php
		} //if viewedExitPopupWP
	}
} //function exit_popup
add_action( 'wp_footer', 'exit_popup1', 10 );

function show_press_release_posts_to_api( $args, $post_type ) {
    if ( 'press-releases' === $post_type || 'articles' === $post_type || 'portfolio' === $post_type || 'banner-slider' === $post_type) {
        $args['show_in_rest'] = true;
    }
    return $args;
}
add_filter( 'register_post_type_args', 'show_press_release_posts_to_api', 10, 2 );

add_shortcode( 'slidercustomshortcode', 'slidercustom' );
function slidercustom( $atts ){
	$str = '<div id="myCarousel" class="carousel slide" data-ride="carousel">';
	$args = array('post_type' => 'banner-slider','order' => 'DESC',
		'tax_query' => array(array('taxonomy' => 'banner_slider_category','field' => 'id','terms' => $atts['slider_id'],),),
		'showposts'=>10);
	query_posts( $args );
	$str .='<ol class="carousel-indicators">';
	if(have_posts()){
		$i=0;
		while (have_posts()) : the_post(); 
		if($i==0){ $active = 'active'; }else{ $active = ''; }
		$str .='<li data-target="#myCarousel" data-slide-to="'.$i.'" class="'.$active.'"></li>';
		$i++;
		endwhile;
	}
	$str .='</ol>';
	$str .='<div class="carousel-inner" role="listbox">';
	query_posts( $args );
	if(have_posts()){
		$i=0;
		while (have_posts()) : the_post();		
			$featured_images = array();
			if( class_exists('Dynamic_Featured_Image') ) {
				global $dynamic_featured_image;
				if ( is_object( $dynamic_featured_image ) ) {
					$featured_images = $dynamic_featured_image->get_featured_images( );
				}
			}
			$noicon = '';
			$fullcaption = '';
			if(!$featured_images){
				$noicon = "display:none";
				$fullcaption = 'fullcaption';
				$newstr = '';
			}else{
				$newstr = '<div class="carouel-right" style="'.$noicon.'"><img class="carouel-image" src="'.$featured_images[0]['full'].'?ver=1" alt="'.get_the_title($featured_images[0]['attachment_id']).'"></div>';
			}
			if($i==0){ $active = ' active'; }else{ $active = ''; }
			$feat_image = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) );
			$bannerclass_text = get_post_meta(get_the_ID(), 'bannerclass_text', true);
			$content = get_the_content();
			$field_id_value='';
			$field_id_value = get_post_meta(get_the_ID(), 'customnotification_field_id', true);
			$readmore='';
			if(!empty($field_id_value) OR $field_id_value!=''){
				$readmore = '<a href="'.$field_id_value.'" class="learn-more-image" style="min-height: 0px; min-width: 0px; line-height: 48px; border-width: 0px; margin: 20px 0px 0px; padding: 0px 20px; letter-spacing: 0px; font-size: 16px;">'.$bannerclass_text.'</a>';
			}else{$readmore='';}
			$title = get_the_title();
			$custom_class_banner = get_post_meta(get_the_ID(), 'bannerclass_field_id', true);			
			$str .= '<div class="item '.$custom_class_banner.$active.'" style="background:url('.$feat_image.')">
				<div class="carousel-caption"><div class="dp-page"> <div class="carouel-left '.$fullcaption.'">'.$content.'<p>'.$readmore.'</p></div>'.$newstr.'</div></div>
			</div>';
			$i++;
		endwhile;
		wp_reset_query();
	}else{		
	}
	$str .='</div>';
	$str .='</div>';
	echo $str;
}
# Register custom post type
require_once 'custompost-type.php';
##############################################################

/* ADDING EMAIL NOTIFICATION CHECKBOX ON POST LEVEL*/
	add_action("admin_init", "banner_read_more");
	function banner_read_more(){add_meta_box("banner_read_more_box", "Read More Url", "banner_read_more_func", "banner-slider", "normal", "high");}
	function banner_read_more_func(){
		global $post;
		$field_id_value = get_post_meta($post->ID, 'customnotification_field_id', true); ?>
<input style="width:100%;border: 1px solid #ededed;" type="input" name="customnotification_field_id"
    value="<?php echo $field_id_value; ?>" />
<?php }

add_action( 'save_post', 'save_global_notice_meta_box_data' );
function save_global_notice_meta_box_data( $post_id ) {

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {return;}

	/* OK, it's safe for us to save the data now. */

	// Make sure that it is set.
	if ( ! isset( $_POST['customnotification_field_id'] ) ) {return;}
	
	// Sanitize user input.
	$my_data = sanitize_text_field( $_POST['customnotification_field_id'] );
	$my_data1 = sanitize_text_field( $_POST['bannerclass_field_id'] );
	$my_data2 = sanitize_text_field( $_POST['bannerclass_text'] );

	// Update the meta field in the database.
	update_post_meta($post_id, "customnotification_field_id", $my_data);
	update_post_meta($post_id, "bannerclass_field_id", $my_data1);
	update_post_meta($post_id, "bannerclass_text", $my_data2);
}		
	
add_action("admin_init", "banner_class_custom");
function banner_class_custom(){add_meta_box("banner_class_each", "Banner Class", "banner_class", "banner-slider", "normal", "high");}
function banner_class(){
	global $post; 
	$bannerclass_value = get_post_meta($post->ID, 'bannerclass_field_id', true); ?>
<input style="width:100%;border: 1px solid #ededed;" type="input" name="bannerclass_field_id"
    value="<?php echo $bannerclass_value; ?>" />
<?php }

add_action("admin_init", "banner_button_text");
function banner_button_text(){add_meta_box("banner_text", "Banner Button Text", "banner_text", "banner-slider", "normal", "high");}
function banner_text(){
	global $post; 
	$bannertext_value = get_post_meta($post->ID, 'bannerclass_text', true); ?>
<input style="width:100%;border: 1px solid #ededed;" type="input" name="bannerclass_text"
    value="<?php echo $bannertext_value; ?>" />
<?php }

/* ADDING EMAIL NOTIFICATION CHECKBOX ON POST LEVEL*/

/*add_filter( 'rest_authentication_errors', 'wp_snippet_disable_rest_api' );
function wp_snippet_disable_rest_api( $access ) {
     return new WP_Error( 'rest_disabled', __('The WordPress REST API has been disabled.'), array( 'status' => rest_authorization_required_code()));
}*/

/*add_action( 'rest_pre_echo_response', function( $response, $object, $request ){
	foreach (apache_request_headers() as $name => $value) {		
		if($name=='checkstring'){
			$passkey = $value;
		}
		if($name=='Referer'){
			$referervalue = $value;
		}
	}
	if($passkey=='ehzfsP1tpkVhCtcV'){
		return $response;
	}elseif($referervalue=='https://dev.grazitti.com/company/careers/'){
		return $response;
	}else{
		return new WP_Error( 'rest_disabled', __('The WordPress REST API has been disabled.'), array( 'status' => rest_authorization_required_code()));
	}
}, 10, 3 );*/

class My_Walker_Nav_Menu extends Walker_Nav_Menu {
  public function start_lvl(&$output, $depth = 0, $args = array() ) {
    $indent = str_repeat("\t", $depth);
    $output .= "\n$indent<ul class=\"dropdown-menu\">\n";    
  }
}
add_filter('nav_menu_css_class', 'add_active_class', 10, 2 );
function add_active_class($classes, $item) {

  if( in_array( 'current-menu-item', $classes ) ||
    in_array( 'current-menu-ancestor', $classes ) ||
    in_array( 'current-menu-parent', $classes ) ||
    in_array( 'current_page_parent', $classes ) ||
    in_array( 'current_page_ancestor', $classes )
    ) {
    $classes[] = "ubermenu-highlight-custom";
  }
  return $classes;
}

/*******************Function used to Get country code by IP Address ******************/
function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {      $ip = $_SERVER["REMOTE_ADDR"]; 
    }
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array("AF" => "Africa","AN" => "Antarctica","AS" => "Asia","EU" => "Europe","OC" => "Australia (Oceania)","NA" => "North America",
        "SA" => "South America");
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => $ipdat->geoplugin_city,
                        "state"          => $ipdat->geoplugin_regionName,
                        "country"        => $ipdat->geoplugin_countryName,
                        "country_code"   => $ipdat->geoplugin_countryCode,
                        "continent"      => $continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => $ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = $ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = $ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = $ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = $ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = $ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}
/*******************END Function used to Get country code by IP Address ******************/
add_action('wp', 'check_home');
function check_home() {
	/*session_start();
	if(!isset($_SESSION['visit_time'])){	
		$_SESSION['visit_time'] ='first-test';
	} 
    if(is_home() || is_front_page()) {
        $country_code = ip_info("Visitor", "Country Code");			
		if($country_code == "AU"){ 
			if((isset($_SESSION['visit_time'])) && ($_SESSION['visit_time']=='first-test')){
				$_SESSION['visit_time'] ='second';					
				header('Location: '.home_url().'/au/'); 
			}
		}
		else if($country_code == "SG"){ 
			if((isset($_SESSION['visit_time'])) && ($_SESSION['visit_time']=='first-test')){
				$_SESSION['visit_time'] ='second';					
				header('Location: '.home_url().'/sg/'); 
			}
		} 		 
	}*/
}
add_filter( 'body_class', function( $classes ) {
    if(isset($_COOKIE['cookieconsent_status'])){return array_merge( $classes, array( 'cookie_is_set' ) );}
	else{return array_merge( $classes, array( 'cookie_not_set' ) );}
});

add_action('init', 'customRSS');
function customRSS(){add_feed('drupalfeeds', 'customRSSFunc');}
function customRSSFunc(){get_template_part('rss', 'drupalfeeds');}

// function hide_admin_bar_from_front_end(){ return false;}
// add_filter( 'show_admin_bar', 'hide_admin_bar_from_front_end' );

/* Fix the per_page to 100 for the post tags query of get_terms() */
add_filter('rest_post_tag_query', function( $args, $request ){   $args['number'] = 100;   return $args;}, 10, 2 );

/* Rest api by aditi for post count */
add_action( 'rest_api_init',  'register_route');
function register_route(){
	register_rest_route( 'wp/v2', '/get_total_count', array('methods' => 'GET',	'callback' => 'get_total_count',));
	register_rest_route( 'wp/v2', '/get_posts_tag', array( 'methods' => 'GET', 'callback' => 'get_posts_tag', ));
}
function get_total_count(){
	$category1 = get_category(1693);
	$category2 = get_category(1433);
	$total = $category1->category_count + $category2->category_count;
	return rest_ensure_response(array('status' => 200, 'total' => $total));
}
function get_posts_tag($request){
	$tag =  $request['tag'];
	$the_query = new WP_Query( 'tag='.$tag );
	$arr = array();
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$src = wp_get_attachment_image_src(get_post_thumbnail_id(), true);
			$arr[] = array('title' => get_the_title(),'post_img' => $src[0],'content' => get_the_content()); 
		}
	}
	echo json_encode(array('status' => 200, 'data' => $arr ));
	die;
}
/* Rest api by aditi for post count */

/* allow cross origin */
add_filter( 'allowed_http_origins', 'add_allowed_origins' );
function add_allowed_origins( $origins ) {
    $origins[] = 'https://pages.grazitti.com';
    return $origins;
}
/* allow cross origin */

add_filter('next_posts_link_attributes', 'posts_link_attributes');
add_filter('previous_posts_link_attributes', 'posts_link_attributes');

function posts_link_attributes() {	return 'class="btn btn-primary btn-blue hover-bg-blue redius-2 border-none"';}

/* Start rest api banner slider custom feilds */
add_action( 'rest_api_init', 'slug_register_starship' );
function slug_register_starship() {
    register_rest_field( 'banner-slider','mobile_banner_slide',array('get_callback'=>'slug_get_mobile_banner_slide'));
    register_rest_field( 'banner-slider','mobile_banner_slide_button_text',array('get_callback'=>'slug_get_mobile_banner_slide_button_text'));
    register_rest_field( 'banner-slider','mobile_banner_slide_button_link',array('get_callback'=>'slug_get_mobile_banner_slide_button_link'));
}
function slug_get_mobile_banner_slide( $object, $field_name, $request ) {
    $meta_keys = get_post_meta( $object[ 'id' ], $field_name , true );	return get_post_meta( $meta_keys, '_wp_attached_file' , true );
}
function slug_get_mobile_banner_slide_button_text( $object, $field_name, $request ) {
    $meta_keys = get_post_meta( $object[ 'id' ], 'bannerclass_text' , true );	return $meta_keys;
}
function slug_get_mobile_banner_slide_button_link( $object, $field_name, $request ) {
    $meta_keys = get_post_meta( $object[ 'id' ], 'customnotification_field_id' , true );	return $meta_keys;
}
/* END rest api banner slider custom feilds */

/* Start rest api Events custom fields */
add_action( 'rest_api_init', 'slug_register_event' );
function slug_register_event() {
    register_rest_field( 'event','mobile_event_start_date',array('get_callback'=>'slug_get_mobile_event_start_date'));
    register_rest_field( 'event','mobile_event_end_date',array('get_callback'=>'slug_get_mobile_event_end_date'));
    register_rest_field( 'event','mobile_event_place',array('get_callback'=>'slug_get_mobile_event_place'));
}
function slug_get_mobile_event_start_date( $object, $field_name, $request ) {
    $meta_keys = get_post_meta( $object[ 'id' ], 'start_date' , true );	return $meta_keys;
}
function slug_get_mobile_event_end_date( $object, $field_name, $request ) {
    $meta_keys = get_post_meta( $object[ 'id' ], 'end_date' , true );	return $meta_keys;
}
function slug_get_mobile_event_place( $object, $field_name, $request ) {
    $meta_keys = get_post_meta( $object[ 'id' ], 'place' , true );	return $meta_keys;
}
/* END rest api Events custom fields */

/* function add_cors_http_header(){
    header("Access-Control-Allow-Origin: *");
}
add_action('init','add_cors_http_header'); */

/**  HR Manager Role code Starts here **/
function my_custom_css() { ?>
<style>
div#wpadminbar,
div#contextual-help-link-wrap,
li#toplevel_page_vc-welcome,
li#menu-posts,
li#menu-posts-career,
li#menu-posts-client,
li#menu-posts-banner-slider,
li#menu-comments,
li#menu-posts-podcast,
li#menu-posts-ad_general_search,
li#menu-posts-press-releases,
li#menu-posts-articles,
li#menu-posts-testimonial,
li#toplevel_page_wpcf7,
li#menu-tools,
div#setting-error-dp-theme,
div#wpassetcleanup_asset_list,
div#advanced-sortables,
div#wpassetcleanup_page_options,
label[for="wpassetcleanup_page_options-hide"],
label[for="wpassetcleanup_asset_list-hide"],
label[for="ndg_page_redirect-hide"],
li#menu-users {
    display: none;
}

html.wp-toolbar {
    padding-top: 0;
}
</style>
<script>
jQuery(document).ready(function($) {
    $("#menu-users").after(
        "<li class='wp-not-current-submenu menu-top menu-top-first' id='logout-link-custom'><a href='<?php echo wp_logout_url(); ?>' class='wp-not-current-submenu menu-top menu-top-first'><div class='wp-menu-image dashicons-before dashicons-admin-users'><br></div><div class='wp-menu-name'>Log Out</div></a></li>"
        );
});
</script><?php
}	
if ( is_user_logged_in() ) {
	$user = wp_get_current_user();
	if ( in_array( 'hr-manager', (array) $user->roles ) ) {
		add_action('admin_head', 'my_custom_css');
		add_action( 'admin_init', 'remove_dashboard_meta' );
	}
} 	
function remove_dashboard_meta() {
	remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');
}
/**  HR Manager Role code ends here **/

/**Start Popular Blog insert IP and Count Key in DB**/
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}	
	
function wpb_get_post_views	($postID){
    $count_key = 'wpb_post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 View";
    }
    return $count.' Views';
}
function wpb_set_post_views($postID) {
    $count_key = 'wpb_post_views_count';
    $getClientIP = get_post_meta($postID, 'client_ip', true);
	if($getClientIP==''){
		add_post_meta($postID, 'client_ip', get_client_ip());
		add_post_meta($postID, $count_key, '1');
	}else{
		if($getClientIP!=get_client_ip()){
			$count = get_post_meta($postID, $count_key, true);
			$total_count = $count + 1;
			update_post_meta($postID, $count_key, $total_count);
		}
	}
}
/**END Popular Blog insert IP and Count Key in DB**/

/** START Save Post View **/
function save_post_views($postID){
	global $wpdb;
	$wp_post_view_table = $wpdb->prefix . "post_views";
	$ip = $_SERVER['REMOTE_ADDR'];
	$getResult = $wpdb->get_row("select * from $wp_post_view_table where postID = '$postID' and ip = '$ip'");
	if(empty($getResult)){		
		$date_time = date('Y-m-d H:i:s');
		$wpdb->insert($wp_post_view_table, array('ip' => $ip, 'postID' => $postID, 'date_time' => $date_time)); 
	}
}
/** END Save Post View **/

/** START GET Post View **/
function get_post_views($postID){
	global $wpdb;
	$wp_post_view_table = $wpdb->prefix . "post_views";
	$getResults = $wpdb->get_results("select * from $wp_post_view_table where postID = '$postID'");
	if(count($getResults)>1){
		return count($getResults).' Views';
	}
	else{
		return count($getResults).' View';
	}	
}
/** END GET Post View **/

/**
 * Post IDs explicitly hidden from the blog listing.
 *
 * Posts without the `show_blog_posts` meta are shown, and posts whose meta
 * contains `show_in_blog_page` are shown. The old REGEXP/OR meta_query forced
 * MySQL to scan every meta row of every post (4M+ rows here); resolving the
 * exclusions first and passing them as `post__not_in` keeps the listing fast.
 */
function gz_blog_excluded_post_ids() {
	static $ids = null;
	if ($ids !== null) {
		return $ids;
	}

	$ids = get_posts(array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_query'     => array(
			array(
				'key'     => 'show_blog_posts',
				'value'   => 'show_in_blog_page',
				'compare' => 'NOT LIKE',
			),
		),
	));

	$ids = array_map('intval', (array) $ids);
	return $ids;
}

/** Get Blog Post After Load More **/
/** Get Blog Post After Load More **/
add_action( 'wp_ajax_get_blog_load_more_post', 'get_blog_load_more_post' );
add_action( 'wp_ajax_nopriv_get_blog_load_more_post', 'get_blog_load_more_post' );
function get_blog_load_more_post(){
	$offset = $_REQUEST['offset'];
	$catID = $_REQUEST['catID'];	
	$tagID = $_REQUEST['tagID'];
	$authorID = $_REQUEST['authorID'];
	$orderVal = $_REQUEST['orderVal'];
	$posts_per_page = $_REQUEST['post_per_page'];
	$author = '';
	$tax_query = array();
	if($_REQUEST['tagID']){$tax_query  = array(array('taxonomy' => 'post_tag','field' => 'tag_ID','terms' => $tagID));}
	$blogArg = array(
		'post_status' => 'publish',
		'offset' =>$offset,
		'posts_per_page' => $posts_per_page,
		'cat' => ($catID != '') ? $catID : '',
		'author' => ($authorID != '') ? $authorID : '',
		'tax_query' => $tax_query,
		'order' => ($orderVal == 'no-order') ? 'desc' : $orderVal,
		'post__not_in' => gz_blog_excluded_post_ids(),
	);
	$blogPost= new WP_Query($blogArg); //echo "<pre>"; print_r($blogPost); echo "</pre>";exit;
	$data = $_POST['data'];	
	$counter = 1;
	$html = '';		
	if ( $blogPost->have_posts() ) {
		while ( $blogPost->have_posts() ) : $blogPost->the_post();
			
			if(in_array(get_the_ID(), $data)){
				continue;
			}
			
			$post_categories = get_the_category( get_the_ID() );
			//echo "<pre>"; print_r($post_categories); echo "</pre>";
			$custom_link = '';				
			if(!empty(get_field('custom_link'))) {		
				$custom_link = get_field('custom_link');				 
			}else{
				$custom_link = get_the_permalink();
			}			
			if($offset % 2 !=0){
				if($counter%7==0){
					$html .= '<div class="col-md-8 gz-mb-30"><div class="post1"><div class="blog-page-content h-100">';	
				}else if($counter%8==0){
					$html .= '<div class="col-md-4 gz-mb-30"><div class="post10"><div class="blog-page-content">';
				}else if($counter%9==0){
					$html .= '<div class="post10"><div class="blog-page-content mb-0">';
				}else{
					$html .= '<div class="col-lg-4 col-md-6 gz-mb-30"><div class="post3 h-100"><div class="blog-page-content h-100">';
				}	
			
			}else{				
				if($counter%7==0){
					$html .= '<div class="col-md-4 gz-mb-30 d-flex flex-column justify-content-between"><div class="post10"><div class="blog-page-content">';
				}else if($counter%8==0){
					$html .= '<div class="post10"><div class="blog-page-content mb-0">';
				}else if($counter%9==0){
					$html .= '<div class="col-md-8 gz-mb-30"><div class="post1"><div class="blog-page-content h-100">';
				}else{
					$html .= '<div class="col-lg-4 col-md-6 gz-mb-30"><div class="post3 h-100"><div class="blog-page-content h-100">';
				}			
			}					
			if (has_post_thumbnail()) { 
				$html .= '<figure class="article-preview-image"><a href="'.$custom_link.'">'.get_the_post_thumbnail(get_the_ID(),array(415, 270)).'</a></figure>'; 
			}
			$html .= '<div class="nt-blog-card-body"><h2><a href="'.$custom_link.'" class="category-title-link">'.get_the_title().'</a></h2><div class="category-post">';
			$html .= '<h6 class="article-meta-extra">';			
			$html .= getCatAncester(get_the_ID(), 'category');			
					
			$html .= '</h6><h6 class="time-read">';
			if(!empty(get_field('custom_read_time')) && ($post_categories[0]->slug=='digital-marketing')) {
				$html .= '<span class="span-reading-time rt-reading-time"><span class="rt-label rt-postfix">'.get_field('custom_read_time').'</span></span>';
			}else{
				$html .= do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] ');
			}
			$html .= '</h6></div></div>';			
			if($offset % 2 !=0){
				if($counter%8==0){	$html .= '</div></div>';}
				else if($counter%7==0){
					$html .= '</div></div>
					<div class="ads_slider blog-page-add-slider w-100">
					<div class="item h-100"><div class="blogs-generic-ads">
					<div class="blog-ads-btm-img position-relative">
					<img class="img-fluid" src="/wp-content/uploads/2020/06/maginate-ads-btm.png" alt="maginate-ads">
					<div class="maginate-blog-btn position-absolute">
					<a href="https://www.grazitti.com/maginate/?utm_source=blog_page&amp;utm_medium=cta&amp;utm_campaign=maginate_generic_ads" target="_blank">Request a Demo</a>
					</div>
					</div>
					</div></div>
					</div>
					</div>';
					}
				else{	$html .= '</div></div></div>';	}
			}else{
				if($counter%7==0){$html .= '</div></div>';}
				else if($counter%9==0){
					$html .= '</div></div>
					<div class="ads_slider blog-page-add-slider w-100">
					<div class="item h-100"><div class="blogs-generic-ads">
					<div class="blog-ads-btm-img position-relative">
					<img class="img-fluid" src="/wp-content/uploads/2020/06/Ad.png" alt="video-ads">
					<div class="position-absolute maginate-ads-btn">
					<a href="https://video.grazitti.com/?utm_source=blog_page&amp;utm_medium=cta&amp;utm_campaign=video_generic_ads" target="_blank">Know More</a>
					</div>
					</div>
					</div></div>
					</div>
					</div>';
					}
				else{$html .= '</div></div></div>';}
			}
			if($counter%9==0){ $counter = 1;}
			else{$counter++;}
		endwhile;
	}
	echo $html; exit;
}
/** END Get Blog Post After Load More **/

/** END Get Blog Post After Load More **/

/** Get Blog Post On Search **/
add_action( 'wp_ajax_get_blog_search_listing', 'get_blog_search_listing' );
add_action( 'wp_ajax_nopriv_get_blog_search_listing', 'get_blog_search_listing' );
function get_blog_search_listing(){ 
	$offset = $_REQUEST['offset'];
	$catID = $_REQUEST['catID'];	
	$tagID = $_REQUEST['tagID'];
	$authorID = $_REQUEST['authorID'];
	$posts_per_page = $_REQUEST['post_per_page'];
	$searchInput = ($_REQUEST['input'])?$_REQUEST['input']:'';
	$author = '';	
	$tax_query = array();
	$blogArg = array(
		'post_status' => 'publish', 
		'cc_search_post_title' => $searchInput, 
		'offset' =>$offset, 
		'posts_per_page' => $posts_per_page,
		'post__not_in' => gz_blog_excluded_post_ids(),
	);
	
	if($catID != ''){
		$blogArg['cat'] = $catID;
	}
	if($authorID != ''){
		$blogArg['author'] = $authorID;
	}
	if($_REQUEST['tagID']){
		$tax_query  = array(array('taxonomy'  => 'post_tag', 'field' => 'tag_ID','terms'     => $tagID));
		$blogArg['tax_query'] = $tax_query;
	}	
	
	add_filter( 'posts_where', 'cc_post_title_filter', 10, 2 );
	$blogPost = new WP_Query($blogArg);	
	remove_filter( 'posts_where', 'cc_post_title_filter', 10 );
	
	$html = '<div class="row ms-container">';		
	if ( $blogPost->have_posts() ) {
		while ( $blogPost->have_posts() ) : $blogPost->the_post(); 
			$post_categories = get_the_category( get_the_ID() );
			
			/* skip record only for Blog page */
			/* $show_blog_posts = get_field('show_blog_posts');
			if (!empty($show_blog_posts)){
				if(!in_array('show_in_blog_page', $show_blog_posts)){
					$html .= '<article class="no-posts"> <h2>No result matching your search</h2></article>';
					continue;
				}	
			} */

			$html .= '<div class="col-lg-4 col-md-6 gz-mb-30"><div class="post3 h-100"><div class="blog-page-content h-100">';			
			
			if (has_post_thumbnail()) { 
				$html .= '<figure class="article-preview-image"><a href="'.get_the_permalink().'">'.get_the_post_thumbnail().'</a></figure>'; 
			}
			$html .= '<div class="nt-blog-card-body"><h2><a href="'.get_the_permalink().'" class="category-title-link">'.get_the_title().'</a></h2><div class="category-post">';
			$html .= '<h6 class="article-meta-extra">';	
			$html .= getCatAncester(get_the_ID(), 'category');			
					
			$html .= '</h6><h6 class="time-read">';
			$html .= do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] ');
			$html .= '</h6></div></div>';			
			$html .= '</div></div></div>';
		endwhile;
	}else{
		$html .= '<article class="no-posts"> <h2>No result matching your search</h2></article>';
	}
	$html .= '</div>';
	echo $html; exit;
}
/** END Get Blog Post On Search **/

/** Start Ajax Request for post like**/
add_action( 'wp_ajax_post_emoji_reactions', 'post_emoji_reactions' );
add_action( 'wp_ajax_nopriv_post_emoji_reactions', 'post_emoji_reactions' );
function post_emoji_reactions(){
	global $wpdb;
	$wp_like_table = $wpdb->prefix . "ulike";
	$postID = $_POST['post_id'];
	$reaction = $_POST['reaction'];
	$ip = $_SERVER['REMOTE_ADDR'];	
	if(getLikeStatus($postID)){
		$data = array('reaction'=>$reaction);
		$where = array('ip' => $ip, 'post_id' => $postID);
		$wpdb->update( $wp_like_table, $data, $where );		
	}else{
		$wpdb->insert($wp_like_table, array('ip' => $ip, 'post_id' => $postID, 'reaction' => $reaction)); 
	}	
	$jsonArr = array('like' => getTotalLiked($postID, 'like'), 'love' => getTotalLiked($postID, 'love'), 'wow' => getTotalLiked($postID, 'wow'), 'insightful' => getTotalLiked($postID, 'insightful'), 'good_stuff' => getTotalLiked($postID, 'good_stuff'), 'curious' => getTotalLiked($postID, 'curious'), 'dislike' => getTotalLiked($postID, 'dislike'), 'boring' => getTotalLiked($postID, 'boring'));	
	echo json_encode($jsonArr);
	exit;
}
function getLikeStatus($post_id){
	global $wpdb;
	$wp_like_table = $wpdb->prefix . "ulike";
	$ip = $_SERVER['REMOTE_ADDR'];
	$getresult = $wpdb->get_row("SELECT * FROM $wp_like_table WHERE post_id = '$post_id' and ip='$ip'");
	if(!empty($getresult)){return true;	}
	else{return false;}
}
function getTotalLiked($post_id, $reaction){
	global $wpdb;
	$wp_like_table = $wpdb->prefix . "ulike";
	$getresult = $wpdb->get_results("SELECT * FROM $wp_like_table WHERE post_id = '$post_id' and reaction='$reaction'");
	return count($getresult);
}
/** END Ajax Request for post like **/

/* Add Custom Meta Key in Post Type List */
add_filter('manage_edit-jobs_columns', 'job_columns');
function job_columns($columns) {   $columns['job_key'] = 'Job Key';   return $columns;}

/* Get Custom Meta Key in Post Type List */
add_action('manage_posts_custom_column',  'job_show_columns');
function job_show_columns($name) {
    global $post;
    switch ($name) {  case 'job_key':
		$job_key = get_post_meta($post->ID, 'job_key', true);
		echo $job_key;
    }
}

/* Search Custom Meta Key in Post Type List */
function job_extend_admin_search( $query ) { 
	// Extend search for document post type
	$post_type = 'jobs';
	// Custom fields to search for
	$custom_fields = array( "job_key",);
    if( ! is_admin() )
    	return;
  	if ( $query->query['post_type'] != $post_type )
  		return;
    $search_term = $query->query_vars['s'];
    // Set to empty, otherwise it won't find anything
    $query->query_vars['s'] = '';
    if ( $search_term != '' ) {
        $meta_query = array( 'relation' => 'OR' ); 
        foreach( $custom_fields as $custom_field ) {array_push( $meta_query, array('key' => $custom_field,'value' => $search_term, 'compare' => 'LIKE'));}
        $query->set( 'meta_query', $meta_query );
    };
}
add_action( 'pre_get_posts', 'job_extend_admin_search' );
/* END Custom Meta Key in Post Type List */

/*******------------ Start Resource Center -----------*******/
require_once 'function-resourceCenter.php';
/*******------------ END Resource Center -----------*******/

/*adding metabox for banner personalization on basis of job function*/
add_action("admin_init", "jobFunctionCheckbox");
function jobFunctionCheckbox(){
		add_meta_box("jobfunctioncheckbox_box", "Job Function", "jobfunctioncheckbox_func", "banner-slider", "side", "high");
}
function jobfunctioncheckbox_func(){
	global $post;
	$jobfunction_order_marketing_value = get_post_meta($post->ID, 'jobfunction_order_marketing', true);	
	$jobfunction_order_Sales_value = get_post_meta($post->ID, 'jobfunction_order_Sales', true);	
	$jobfunction_order_IT_value = get_post_meta($post->ID, 'jobfunction_order_IT', true);	
	$jobfunction_order_Success_value = get_post_meta($post->ID, 'jobfunction_order_Success', true);	
	$jobfunction_order_Support_value = get_post_meta($post->ID, 'jobfunction_order_Support', true);	
	$jobfunction_marketing = get_post_meta($post->ID, 'jobFunction_options_Marketing', true);	
	$jobfunction_Sales = get_post_meta($post->ID, 'jobFunction_options_Sales', true);	
	$jobfunction_IT= get_post_meta($post->ID, 'jobFunction_options_IT', true);	
	$jobfunction_Success = get_post_meta($post->ID, 'jobFunction_options_Success', true);	
	$jobfunction_Support = get_post_meta($post->ID, 'jobFunction_options_Support', true);	
	if($jobfunction_marketing ) {$field_marketing_checked = 'checked="checked"';}
	if($jobfunction_Sales) {$field_Sales_checked = 'checked="checked"';}
	if($jobfunction_IT) {$field_IT_checked = 'checked="checked"';}
	if($jobfunction_Success) {$field_Success_checked = 'checked="checked"';}
	if($jobfunction_Support) {$field_Support_checked = 'checked="checked"';}?>
<ul>
    <li style="height: 25px;">
        <label for="jobFunction_Marketing">
            <input type="checkbox" id="jobFunction_Marketing" name="jobFunction_options_Marketing" value="1"
                <?php echo $field_marketing_checked; ?>>
            Marketing
        </label>
        <input type="number" style="width:20%;float: right;margin-left: 5px;" name="jobfunction_order_marketing"
            value="<?php echo $jobfunction_order_marketing_value;?>" /><span style="float: right" min="1"
            max="10"><b>Order:</b></span>
    </li>
    <li style="height: 25px;">
        <label for="jobFunction_Sales">
            <input type="checkbox" id="jobFunction_Sales" name="jobFunction_options_Sales" value="1"
                <?php echo $field_Sales_checked; ?>>Sales
        </label>
        <input type="number" style="width:20%;float: right;margin-left: 5px;" name="jobfunction_order_Sales"
            value="<?php echo $jobfunction_order_Sales_value;?>" /><span style="float: right" min="1"
            max="10"><b>Order:</b></span>
    </li>
    <li style="height: 25px;">
        <label for="jobFunction_IT">
            <input type="checkbox" id="jobFunction_IT" name="jobFunction_options_IT" value="1"
                <?php echo $field_IT_checked; ?> />IT
        </label>
        <input type="number" style="width:20%;float: right;margin-left: 5px;" name="jobfunction_order_IT"
            value="<?php echo $jobfunction_order_IT_value;?>" /><span style="float: right" min="1"
            max="10"><b>Order:</b></span>
    </li>
    <li style="height: 25px;">
        <label for="jobFunction_Success">
            <input type="checkbox" id="jobFunction_Success" name="jobFunction_options_Success" value="1"
                <?php echo $field_Success_checked; ?> />Success
        </label>
        <input type="number" style="width:20%;float: right;margin-left: 5px;" name="jobfunction_order_Success"
            value="<?php echo $jobfunction_order_Success_value;?>" /><span style="float: right" min="1"
            max="10"><b>Order:</b></span>
    </li>
    <li style="height: 25px;">
        <label for="jobFunction_Support">
            <input type="checkbox" id="jobFunction_Support" name="jobFunction_options_Support" value="1"
                <?php echo $field_Support_checked; ?> />Support
        </label>
        <input type="number" style="width:20%;float: right;margin-left: 5px;" name="jobfunction_order_Support"
            value="<?php echo $jobfunction_order_Support_value;?>" /><span style="float: right" min="1"
            max="10"><b>Order:</b></span>
    </li>
</ul><?php 
}
/*-----Saving Meta Boxes values under posts of Banner slider Custom Post Type 15-10-2019-----*/
add_action('save_post', 'save_jobfunction_details');
	function save_jobfunction_details($post_id){
		global $post;
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {return $post_id;}
		if ($post->post_type == 'banner-slider') { // Check to see if Event Type.  
			update_post_meta($post->ID, "jobfunction_order_marketing", $_POST["jobfunction_order_marketing"]);
			update_post_meta($post->ID, "jobfunction_order_Sales", $_POST["jobfunction_order_Sales"]);
			update_post_meta($post->ID, "jobfunction_order_IT", $_POST["jobfunction_order_IT"]);
			update_post_meta($post->ID, "jobfunction_order_Success", $_POST["jobfunction_order_Success"]);
			update_post_meta($post->ID, "jobfunction_order_Support", $_POST["jobfunction_order_Support"]);
			update_post_meta($post->ID, "jobFunction_options_Marketing", $_POST["jobFunction_options_Marketing"]);
			update_post_meta($post->ID, "jobFunction_options_Sales", $_POST["jobFunction_options_Sales"]);
			update_post_meta($post->ID, "jobFunction_options_IT", $_POST["jobFunction_options_IT"]);
			update_post_meta($post->ID, "jobFunction_options_Success", $_POST["jobFunction_options_Success"]);
			update_post_meta($post->ID, "jobFunction_options_Support", $_POST["jobFunction_options_Support"]);
		}
}

/*START Security Parameter for limit login Attempt*/
function my_custom_user_action(){
  
	$allowedRetries = get_option('limit_login_allowed_retries');
	$reset = maybe_serialize(['::1' => $allowedRetries]);
	update_option('limit_login_retries', $reset);
}
add_action('admin_init', 'my_custom_user_action');
/*END Security Parameter for limit login Attempt*/

require_once 'shortcode.php';

function redirection301QueryString(){
	$querystring = "/maginate?";
	if($_REQUEST['utm_source']){$querystring .= "utm_source=".$_REQUEST['utm_source']."&";}
	if($_REQUEST['utm_medium']){$querystring .= "utm_medium=".$_REQUEST['utm_medium']."&";}
	if($_REQUEST['utm_campaign']){$querystring .= "utm_campaign=".$_REQUEST['utm_campaign']."&";}
	if($_REQUEST['mkt_tok']){$querystring .= "mkt_tok=".$_REQUEST['mkt_tok']."&";}
	if($_REQUEST['mkt_tok']){$querystring .= "mkt_tok=".$_REQUEST['mkt_tok']."&";}
	return $querystring;
}
/**** START Get Category Parent & Child Acc to Ancester ****/
function getCatAncester($post_id, $taxonomy, $optional=NULL){
	if (has_category() && !has_category('Uncategorized')) {
		$termName = "";
		$term_list = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'all' ) );
		if(count($term_list) ==1){
			//$termName =  $term_list[0]->name;
			$term_link = get_term_link($term_list[0]);
			if($optional){
				$category_color = "#ffffff";
				$termName .= '<span class="nt-single-cat category_name"><a class="custom_cat_by_post" style="color:' . $category_color . ';" href="'.esc_url($term_link).'">'.$term_list[0]->name.'</a></span>, ';
			}else{
				$category_color = get_field( 'color', $term_list[0] );
				$termName .= '<a class="custom_cat_by_post" style="color:' . $category_color . ';" href="'.esc_url($term_link).'">'.$term_list[0]->name.'</a>, ';
			}			
			
		}
		if(count($term_list) >1){
			foreach($term_list as $key => $term){
				if($optional){
					$category_color = "#ffffff";
				}else{
					$category_color = get_field( 'color', $term );
				}
				
				$term_link = get_term_link($term);
				if($term->parent!=0){
					if($optional){
						$termName .= '<span class="nt-single-cat category_name"><a class="custom_cat_by_post" style="color:' . $category_color . ';" href="'.esc_url($term_link).'">'.$term->name.'</a></span>';
					}else{
						$termName .= '<a class="custom_cat_by_post" style="color:' . $category_color . ';" href="'.esc_url($term_link).'">'.$term->name.'</a>, ';
					}
				}
			}
		}
		return substr(trim($termName), 0, -1);
	}
}
/**** END Get Category Parent & Child Acc to Ancester ****/

/**START Category parent $ child for ADs**/
function getCatAncesterAds($post_id, $taxonomy){
	if (has_category() && !has_category('Uncategorized')) {
		$adName = "";
		$ad_list = wp_get_post_terms( $post_id, 'category', array( 'fields' => 'all' ) );
		if(count($ad_list) ==1){
			//$adName =  $term_list[0]->name;
			$blog_cat = get_field( 'sidebar_ads_description', $ad_list[0] );
			$adName .= 	'<div>' . $blog_cat . ' </div>';			
			
		}
		if(count($ad_list) >1){ 
			foreach($ad_list as $child_ad){
				$blog_cat = get_field( 'sidebar_ads_description', $child_ad);
				if($child_ad->parent!=0){
					$adName .= 	'<div>' . $blog_cat . ' </div>';
				}
			}
		}
		return substr(trim($adName), 0, -1);
	}
}
/**END Category parent $ child for ADs**/

/**** Header host injection start ****/ 
 add_action('init', 'rss_prevent_func');
function rss_prevent_func(){
 $str = urldecode($_SERVER['REQUEST_URI']);
 $lowerStr = strtolower($str);
  $lowerStr = $str;
 $pattern = "/>/i";
	if(preg_match($pattern, $lowerStr) == 1)
	{
		echo "Oops invalid URL"; exit;
	}
 }
 /**** Header host injection end ****/
 
function cc_mime_types( $mimes ){
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

function more_post_ajax(){

    $ppp = (isset($_POST["offset"])) ? $_POST["offset"] : 12;
    //$ppp = (isset($_POST['pageNumber'])) ? $_POST['pageNumber'] : 0;

    header("Content-Type: text/html");
if($_POST['cat'] != 'all'){
	
			 $args= array(
'post_type'=> 'airesource',
'orderby'    => 'ID',
'post_status' => 'publish',
'order'    => 'DESC',
 'posts_per_page' =>$ppp,
'tax_query' => array(
        array(
            'taxonomy' => 'aiResource_category',
            'field' => 'slug',
            'terms' => $_POST['cat'],
         ),
    ),
);
	
}
else
{
	
	  $args = array(
        'suppress_filters' => true,
        'post_type' => 'airesource',
		'post_status' => 'publish',
		'orderby'    => 'ID',
        'post_status' => 'publish',
        'order'    => 'DESC',
		 'posts_per_page' =>$ppp,
       
    );
	
}

    $loop = new WP_Query($args);

	$numberOfPosts = $loop->found_posts;
 	echo '<input name="hidden_all_load_'. $_POST['cat'].'" id="hidden_all_load_'. $_POST['cat'].'" type="hidden" value="'.$numberOfPosts. '" class="hidden_all_load_' . $_POST['cat'] . '">';

    $out = '';

    if ($loop -> have_posts()) :  while ($loop -> have_posts()) : $loop -> the_post(); 
	
	$feat_image_url = wp_get_attachment_url( get_post_thumbnail_id() );
	$meta_key="airesource_count";
$countView = get_post_meta(get_the_ID(), $meta_key, true);
if($countView > 0){
	$countView=$countView ;
}
else
{
	$countView='No views';
}
$terms = get_the_terms(get_the_ID(), 'aiResource_category' );
if ( !empty( $terms ) ){
    // get the first term
    $term = array_shift( $terms );
    $catName= $term->name;
}
$source_link = get_field('source_link',get_the_ID());	
	    $out .= '<div class="card">
		<div class="card-img-top">
		<img src="'.$feat_image_url.'" class="img-f h--100" alt="icon"/>
		</div>
		<div class="card-title">
		'.get_the_title().'
		</div>
		<div class="card-body">
		   <p>
		   '.get_the_content().'
		   </p>
		</div>
		<div class="card-text d-flex">
		   <div class="eye-icon d-flex">
			  <img src="/wp-content/uploads/2023/10/view-4.png" class="mt-2 " alt="Path">
				 <p><span class="mt-0 ml-2" href="" target="_blank" rel="noopener">'.ucfirst($countView).'</span></p>
		   </div>
		   <div class="source-link d-flex">
		   <img src="/wp-content/uploads/2023/10/sourcelink-5.png" class=" mr-1 imgfolder" alt="file-export">
		   <a onclick="myFunctionCount('.get_the_ID().')" href="'.$source_link.'" class="badge badge-light" target="_blank" rel="noopener">Source Link</a>
		   </div>
		</div>
	 </div>
	';
    endwhile;
    endif;
    wp_reset_postdata();
    die($out);
}

add_action('wp_ajax_nopriv_more_post_ajax', 'more_post_ajax');
add_action('wp_ajax_more_post_ajax', 'more_post_ajax');

function post_count_view()
{
	
	$post_id=$_POST['post_id'];
	$meta_key="airesource_count";

$total= get_post_meta($post_id, $meta_key, true);

if(metadata_exists('post', $post_id, $meta_key)) {
    $total= update_post_meta($post_id, $meta_key, $total+1);
}
else
{
    add_post_meta( $post_id, $meta_key, $total+1);
}	
    $totalCount= get_post_meta($post_id, $meta_key, true);
	die($totalCount);

}

add_action('wp_ajax_nopriv_post_count_view', 'post_count_view');
add_action('wp_ajax_post_count_view', 'post_count_view');

function update_ip() {
	
	 global $wpdb;

 $user_ip = $_SERVER['REMOTE_ADDR'];

    $tablename=$wpdb->prefix.'ip_address';
	
	 $data=array(
        'ip_address' => $user_ip,
		'email' => trim($_POST['email']));
	$email=trim($_POST['email']);
    $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $tablename WHERE ip_address = '$user_ip' AND email='$email'");

    if($rowcount > 0)
	{
		echo "already exist";
	}
	else
	{
		 $wpdb->insert( $tablename, $data);
		 $_SESSION['verified_user'] = $email;
		 echo "new record added";
	} 
   
   wp_die();
}

add_action( 'wp_ajax_nopriv_update_ip', 'update_ip' );
add_action( 'wp_ajax_update_ip', 'update_ip' );

function checkemail() {
	
	 global $wpdb;
     global $wp_session;


    $tablename=$wpdb->prefix.'ip_address';
	
	
	$email=trim($_POST['email']);
    $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $tablename WHERE email='$email'");

    if($rowcount > 0)
	{
		$_SESSION['verified_user'] = $email;
		echo "1";
	}
	else
	{
		
		echo "0";
	} 
   
  exit;
}

add_action( 'wp_ajax_nopriv_checkemail', 'checkemail' );
add_action( 'wp_ajax_checkemail', 'checkemail' );

add_action('wp_ajax_data_fetch', 'data_fetch');
add_action('wp_ajax_nopriv_data_fetch', 'data_fetch');
function data_fetch(){ 
	$offset = 0; $posts_per_page = 16;
                    $loop = new WP_Query(array('post_status' => 'publish', 'order' => $_POST['order'], 'offset' => $offset, 'posts_per_page' => $posts_per_page ));
                    $row = 1;
                    if ( $loop->have_posts() ) :
                    while ( $loop->have_posts() ) : 
                    $loop->the_post(); 
					
					$post_categories = get_the_category( $post->ID );
					$custom_link = '';	
					$query_str = do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] ');			
					
					if (!empty(get_field('custom_link')) && ($post_categories[0]->slug=='digital-marketing' || $post_categories[0]->slug=='salesforce-3')) {
						//echo "testlinkup=>".the_field('custom_link');
						 $custom_link = get_field('custom_link');	
							//echo "testlink1=>".$custom_link;								 
					}
					else{
						$custom_link = get_the_permalink();
					}
					
					/* skip record only for Blog page */
					$show_blog_posts = get_field('show_blog_posts');
					if (!empty($show_blog_posts)){
						if(!in_array('show_in_blog_page', $show_blog_posts)){
							continue;
						}	
					}
					
                    if($row >=1 && $row <= 3){ 
						if($row ==1){?>
<div class="row ms-container">
    <?php
						}?>
    <div class="col-md-4 mb-4 mb-md-0">
        <div class="post3 h-100">
            <div class="blog-page-content h-100" id=<?php echo $post->ID;?>><?php 
									if (has_post_thumbnail()) { ?>
                <figure class="article-preview-image"><a
                        href="<?php echo $custom_link; ?>"><?php the_post_thumbnail(array(415, 270)); ?></a></figure><?php 
									} ?>
                <div class="nt-blog-card-body">
                    <h2><a href="<?php echo $custom_link; ?>" class="category-title-link"><?php the_title(); ?></a></h2>
                    <div class="category-post">
                        <h6 class="article-meta-extra"><?php 																			
												if (has_category() && !has_category('Uncategorized')) {
													$cat_count = 0;
													foreach( $post_categories as $post_category ){
														if($cat_count==0){
															$category_color = get_field( 'color', $post_category );
															echo '<a class="custom_cat_by_post" style="color:' . $category_color . ';" href="' . esc_url( get_category_link( $post_category ) ) . '">' . esc_html( $post_category->name ) . '</a>';
														}
														$cat_count++;
													}													
												}?>
                        </h6>
                        <h6 class="time-read">
                            <?php if(!empty(get_field('custom_read_time')) &&$post_categories[0]->slug=='digital-marketing' || $post_categories[0]->slug=='salesforce-3') {?><span
                                class="span-reading-time rt-reading-time"><span
                                    class="rt-label rt-postfix"><?php echo get_field('custom_read_time');?></span></span><?php }else{ echo do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] '); } ?>
                        </h6>
                    </div>
                    <!-- END CATEGORY POST -->
                </div>
            </div>
            <!-- END EACH BLOG-->
        </div>
    </div> <?php
							if($row == 3){?>
</div> <?php }
					}
					
					if($row >=4 && $row <= 6){ 
						if($row ==4){?>
<div class="row ms-container d-none"><?php
						}?>
    <div class="col-md-4 mb-4 mb-md-0">
        <div class="post3 h-100">
            <div class="blog-page-content h-100" id=<?php echo $post->ID;?>><?php 
									if (has_post_thumbnail()) { ?>
                <figure class="article-preview-image"><a
                        href="<?php the_permalink(); ?>"><?php the_post_thumbnail(array(415, 270)); ?></a></figure><?php 
									} ?>
                <div class="nt-blog-card-body">
                    <h2><a href="<?php the_permalink(); ?>" class="category-title-link"><?php the_title(); ?></a></h2>
                    <div class="category-post">
                        <h6 class="article-meta-extra"><?php 
																								
												if (has_category() && !has_category('Uncategorized')) {
													$cat_count = 0;
													foreach( $post_categories as $post_category ){
														if($cat_count==0){
															$category_color = get_field( 'color', $post_category );
															echo '<a class="custom_cat_by_post" style="color:' . $category_color . ';" href="' . esc_url( get_category_link( $post_category ) ) . '">' . esc_html( $post_category->name ) . '</a>';
														}
														$cat_count++;
													}													
												}?>
                        </h6>
                        <h6 class="time-read">
                            <?php echo do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] '); ?>
                        </h6>
                    </div>
                    <!-- END CATEGORY POST -->
                </div>
            </div>
            <!-- END EACH BLOG-->
        </div>
    </div> <?php
							if($row == 6){?>
</div> <?php }
					}
                    if($row == 7){ ?>
<div class="row" id="ms-container">
    <div class="col-xl-8 col-md-7">
        <div class="blog-page-content post1 h-100" id=<?php echo $post->ID;?>> <?php 
								if (has_post_thumbnail()) { ?>
            <figure class="article-preview-image"><a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('large'); ?></a></figure> <?php 
                                } ?>
            <div class="nt-blog-card-body">
                <h2><a href="<?php the_permalink(); ?>" class="category-title-link"><?php the_title(); ?></a></h2>
                <div class="category-post">
                    <h6 class="article-meta-extra"><?php 
											
											echo getCatAncester($post->ID, 'category');											
											/* if (has_category() && !has_category('Uncategorized')) {
												$cat_count = 0;
												foreach( $post_categories as $post_category ){
													if($cat_count==0){
														$category_color = get_field( 'color', $post_category );
														echo '<a class="custom_cat_by_post" style="color:' . $category_color . ';" href="' . esc_url( get_category_link( $post_category ) ) . '">' . esc_html( $post_category->name ) . '</a>';
													}
													$cat_count++;
												}											
											} */?>
                    </h6>
                    <h6 class="time-read">
                        <?php echo do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] '); ?>
                    </h6>
                </div>
                <!-- END CATEGORY POST -->
            </div>
        </div>
        <!-- END EACH BLOG-->
    </div>
    <div class="col-xl-4 col-md-5">
        <div class="side-bar h-100">
            <div class="blog-listing">
                <div class="popular-post">
                    <p>Popular Blog Posts</p>
                </div>
                <ul class="nt-blog-list"><?php  
										global $post;										
										$PopularPostArgs = array( 'posts_per_page' => 6, 'meta_key' => 'wpb_post_views_count', 'orderby' => 'meta_value_num', 'order' => 'DESC'  ) ;
										$popularPost = new WP_Query($PopularPostArgs);
										if ( $popularPost->have_posts() ) {
											while($popularPost-> have_posts()) : $popularPost-> the_post();									
											$view_count = get_post_meta(get_the_ID(), 'wpb_post_views_count', true);?>
                    <li>
                        <h5><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h5>
                        <ul class="nt-blog-inner-list">
                            <li>
                                <div class="category-post category-sidebar">
                                    <h6 class="article-meta-extra"><?php 
															echo getCatAncester($post->ID, 'category');	
																/* $post_categories = get_the_category( $post->ID );											
																if (has_category() && !has_category('Uncategorized')) {
																	$cat_count = 0;
																	foreach( $post_categories as $post_category ){
																		if($cat_count==0){
																			$category_color = get_field( 'color', $post_category );
																			echo '<a class="custom_cat_by_post" style="color:' . $category_color . ';" href="' . esc_url( get_category_link( $post_category ) ) . '">' . esc_html( $post_category->name ) . '</a>';
																		}
																		$cat_count++;
																	}																	
																} */?>
                                    </h6>
                                    <!--h6 class="most_views <?php //echo get_the_ID(); ?>"><span class="view_count"><?php //echo $view_count.' Views';?></span></h6-->
                                    <h6 class="time-read">
                                        <?php echo do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] '); ?>
                                    </h6>
                                </div>
                                <!-- END CATEGORY POST -->
                            </li>
                        </ul>
                    </li><?php 
											endwhile; wp_reset_postdata();
										} else{ echo '<h5 style="color:#000;" class="no_found_data">No Popular Blog Found</h5>'; }?>
                </ul>
            </div>
        </div>
    </div>
</div><?php  
					}
					
                    if($row >=8 && $row <=11){ 
						if($row ==8){?>
<div class="row mb-4">
    <div class="col-lg-8 mb-md-4 mb-lg-0">
        <div class="row"><?php 
						} ?>
            <div class="col-sm-6 left-inner-blogs">
                <div class="post4 h-100">
                    <div class="blog-page-content h-100" id=<?php echo $post->ID;?>><?php 
										if (has_post_thumbnail()) {
													$attachment_id = get_post_thumbnail_id( $post->ID );

												$alt_name = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
												$title_name = get_the_title($image_id);
												if($alt_name != ''){
													$alt_text=$alt_name;
												}
												else{
													$alt_text=$title_name;
												}
												$post_thumbnail_attr = array(
												'alt'   => $alt_text
											);
											
											?>
                        <figure class="article-preview-image"><a
                                href="<?php the_permalink(); ?>"><?php the_post_thumbnail(array(415, 270), $post_thumbnail_attr ); ?></a>
                        </figure><?php
										} ?>
                        <div class="nt-blog-card-body">
                            <h2><a href="<?php the_permalink(); ?>" class="category-title-link"> <?php the_title(); ?>
                                </a></h2>
                            <div class="category-post">
                                <h6 class="article-meta-extra"><?php 
														echo getCatAncester($post->ID, 'category');	
															/* $post_categories = get_the_category( $post->ID );												
															if (has_category() && !has_category('Uncategorized')) {
																$cat_count = 0;
																foreach( $post_categories as $post_category ){
																	if($cat_count==0){
																		$category_color = get_field( 'color', $post_category );
																		echo '<a class="custom_cat_by_post" style="color:' . $category_color . ';" href="' . esc_url( get_category_link( $post_category ) ) . '">' . esc_html( $post_category->name ) . '</a>';
																	}
																	$cat_count++;
																}																
															} */?>
                                </h6>
                                <h6 class="time-read">
                                    <?php echo do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] '); ?>
                                </h6>
                            </div>
                            <!-- END CATEGORY POST -->
                        </div>

                    </div>
                    <!-- END EACH BLOG-->
                </div>
            </div><?php 
								if($row == 11){?>
        </div>
    </div> <?php 
								}
							if( $row == 11) {?>
    <div class="col-lg-4">
        <!-- New blog ads -->
        <div id="carouselExampleIndicators" class="carousel slide h-100" data-ride="carousel"><?php
								$args = array('post_type' => 'banner-slider', 'order' => 'DESC'	 );									
                                   $getBannerSlide = new WP_Query( $args ); ?>
            <ol class="carousel-indicators"><?php
									if($getBannerSlide->have_posts()){
										if($getBannerSlide->post_count > 1){
											$activeCounter = 0;	$i = 0;											
											while($getBannerSlide->have_posts()) : $getBannerSlide->the_post();
											$blog_ads_content = get_field('blog_ads_content');
												if($activeCounter == 0){
													$class= 'active'; $i = 0;
												}else{$class= '';	}
												if(!empty($blog_ads_content)){?>
                <li data-target="#carouselExampleIndicators" data-slide-to="<?php echo $i;?>"
                    class="<?php echo $class;?>"></li><?php 
												}
											$activeCounter++;
											$i++;
											endwhile;
										}
									}?>
            </ol>
            <div class="carousel-inner h-100"><?php
									if($getBannerSlide->have_posts()){									
										$activeCounter = 0;										
                                        while ($getBannerSlide->have_posts()) : $getBannerSlide->the_post();
											$blog_ads_content = get_field('blog_ads_content');									 					
											if(!empty($blog_ads_content)){
												if($activeCounter==0){ $active = 'active';}else{ $active = ''; }  ?>
                <div class="carousel-item <?php echo $active;?> h-100"><?php   echo $blog_ads_content;?></div><?php
											}
											$activeCounter++;
										//$i++;
                                        endwhile;  wp_reset_postdata(); 
																			
									}?>
            </div>
        </div>
    </div>
    <div style="clear:both;"> </div>
</div> <?php 
						} 
				    }
					
					if($row >=12 && $row <= 14){ 
						if($row ==12){ ?>
<div class="row ms-container"><?php
						}?>
    <div class="col-md-4 mb-4 mb-md-0">
        <div class="post3 h-100">
            <div class="blog-page-content h-100" id=<?php echo $post->ID;?>><?php 
									if (has_post_thumbnail()) { ?>
                <figure class="article-preview-image"><a
                        href="<?php the_permalink(); ?>"><?php the_post_thumbnail(array(415, 270)); ?></a></figure><?php 
									} ?>
                <div class="nt-blog-card-body">
                    <h2><a href="<?php the_permalink(); ?>" class="category-title-link"><?php the_title(); ?></a></h2>
                    <div class="category-post">
                        <h6 class="article-meta-extra"><?php 
											echo getCatAncester($post->ID, 'category');	
												/* $post_categories = get_the_category( $post->ID );												
												if (has_category() && !has_category('Uncategorized')) {
													$cat_count = 0;
													foreach( $post_categories as $post_category ){
														if($cat_count==0){
															$category_color = get_field( 'color', $post_category );
															echo '<a class="custom_cat_by_post" style="color:' . $category_color . ';" href="' . esc_url( get_category_link( $post_category ) ) . '">' . esc_html( $post_category->name ) . '</a>';
														}
														$cat_count++;
													}													
												} */?>
                        </h6>
                        <h6 class="time-read">
                            <?php echo do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] '); ?>
                        </h6>
                    </div>
                    <!-- END CATEGORY POST -->
                </div>
            </div>
            <!-- END EACH BLOG-->
        </div>
    </div><?php
							if($row == 14){?>
</div> <?php }
						} 
						if($row >=15 && $row <= 16){
							if($row == 15){ ?>
<div class="row ads-slider">
    <div class="col-md-8 mb-4 mb-md-0">
        <div class="post1">
            <div class="blog-page-content h-100" id=<?php echo $post->ID;?>>
                <?php if (has_post_thumbnail()) : ?>
                <figure class="article-preview-image"><a
                        href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a></figure>
                <?php else : ?>
                <?php endif; ?>
                <div class="nt-blog-card-body">
                    <h2><a href="<?php the_permalink(); ?>" class="category-title-link"> <?php the_title(); ?> </a></h2>
                    <div class="category-post">
                        <h6 class="article-meta-extra"><?php 
											echo getCatAncester($post->ID, 'category');	
												?>
                        </h6>
                        <h6 class="time-read">
                            <?php echo do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] '); ?>
                        </h6>
                    </div>
                    <!-- END CATEGORY POST -->
                </div>
            </div>
            <!-- END EACH BLOG-->
        </div>
        <div class="text-white ads_slider blog-page-add-slider w-100">
            <div id="blog-slider" class="owl-carousel owl-theme"><?php
								   $adsArgs = array('post_type' => 'ads-slider','order' => 'DESC');									
								   $getAdsSlide = new WP_Query( $adsArgs ); 
									if($getAdsSlide->have_posts()){								
										while ($getAdsSlide->have_posts()) : $getAdsSlide->the_post();?>
                <div class="item h-100"><?php the_content(); ?></div><?php							
										endwhile;  wp_reset_postdata(); 
									}?>
            </div>
        </div>
    </div><?php 
						}                    
								if($row == 16){ ?>
    <div class="col-md-4">
        <div class="post10 mb-4 mb-lg-0">
            <div class="blog-page-content" id=<?php echo $post->ID;?>>
                <?php if (has_post_thumbnail()) : ?>
                <figure class="article-preview-image"><a
                        href="<?php the_permalink(); ?>"><?php the_post_thumbnail(array(415, 270)); ?></a></figure>
                <?php else : ?>
                <?php endif; ?>
                <div class="nt-blog-card-body">
                    <h2><a href="<?php the_permalink(); ?>" class="category-title-link"><?php the_title(); ?></a></h2>
                    <div class="category-post">
                        <h6 class="article-meta-extra"><?php
												echo getCatAncester($post->ID, 'category');	?>
                        </h6>
                        <h6 class="time-read">
                            <?php echo do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] '); ?>
                        </h6>
                    </div>
                    <!-- END CATEGORY POST -->
                </div>
            </div>
            <!-- END EACH BLOG-->
        </div>
        <div class="blog-ads-bottom">
            <div class="nt-blog-slider-sm text-center">
                <div id="myCarousel2" class="carousel slide" data-ride="carousel"><?php
										$today = date('Ymd');
										$args = array('post_type' => 'event','order' => 'DESC',		
											'meta_query' => array(array('key' => 'start_date','compare'	=> '>=','value'	=> $today,)),
										);
										$args = array('post_type' => 'event','order' => 'ASC',
											'meta_query' => array('relation'    => 'OR',
												array('key' => 'start_date', 'value' => $today,'compare' => '>=','type' => 'DATE',),
												array('key' => 'end_date','value' => $today,'compare' => '>=','type' => 'DATE',)
											),
											'orderby' => 'start_date',		
										);
										$getEventSlide = new WP_Query( $args );?>
                    <!-- Indicators -->
                    <ol class="carousel-indicators"><?php
										if($getEventSlide->have_posts()){
											if($getEventSlide->post_count > 1){
												$counter = 0; $i = 0;
												while($getEventSlide->have_posts()) : $getEventSlide->the_post();
													if($counter == 0){
														$class= 'active'; $i = 0;
													}else{
														$class= '';																				
													}?>
                        <li data-target="#myCarousel2" data-slide-to="<?php echo $i;?>" class="<?php echo $class;?>">
                        </li><?php
												$counter++;
												$i++;
												endwhile;
											}
										}?>
                    </ol>
                    <!-- Wrapper for slides -->
                    <div class="carousel-inner sidebar-bottom-carousal h-100"><?php
											if($getEventSlide->have_posts()){
												$counter = 0;
												while ($getEventSlide->have_posts()) : $getEventSlide->the_post();
												$event_link = get_field('read_more_link_for_blog_page');
												if($counter==0){ $active = 'active'; }else{ $active = ''; }	?>
                        <div class="carousel-item h-100 <?php echo $active;?>">
                            <div class="nt-blog-bottom-carousel h-100 d-flex align-items-center justify-content-center">
                                <div class="nt-blog-carousel-sm-inner">
                                    <h3><?php echo get_the_title(); ?></h3>
                                    <h5>Place: <?php the_field('place'); ?></h5><?php
																if( $event_link ){
																	$event_link_url = $event_link['url'];
																	$event_link_target = $event_link['target'] ? $event_link['target'] : '_self';?>
                                    <a href="<?php echo $event_link_url; ?>"
                                        target="<?php echo esc_attr($event_link_target); ?>"
                                        class="btn btn-bottomCarousel btn-sideCarousel text-uppercase">View Our Event <i
                                            class="fa fa-angle-double-right"></i></a><?php 
																} else{
																	echo '<a href="/company/events/" class="btn btn-bottomCarousel btn-sideCarousel text-uppercase">View Our Event <i class="fa fa-angle-double-right"></i></a>';
																}?>
                                </div>
                            </div>
                        </div><?php 
												$counter++;
												endwhile; 
											}
											else{echo "<h5 class='no_found_data'>No Upcoming Event</h5>";}
											wp_reset_postdata();?>
                    </div>
                    <!-- Left and right controls -->
                    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left"></span><span class="sr-only">Previous</span>
                    </a>
                    <a class="right carousel-control" href="#myCarousel" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right"></span><span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div><?php
						}
						}					 
                    $row++;
                    endwhile;
                    else :  ?>
<article class="no-posts">
    <h1>No Blogs Found.</h1>
</article>
<?php endif; ?>
<div class="row blog_post_row"></div>
<div class="row statewise_scroll"></div>
<div class="load_more text-center" id="loaderOnScroll"><img src='/wp-content/uploads/2019/10/giphy.gif' alt='Loading'>
</div>
<?php die(); 
}

add_action('init', 'start_session', 1);

function start_session() {
	// wp-admin page loads never read $_SESSION; starting one there only takes a
	// session-file lock and serialises concurrent admin requests.
	if ( is_admin() && ! wp_doing_ajax() ) {
		return;
	}
	if(!session_id()) {
		session_start();
	}
}

function get_post_id_by_excerpt($excerpt)
{
	global $wpdb;
	$result = $wpdb->get_row(
		$wpdb->prepare("SELECT post_content FROM {$wpdb->prefix}posts WHERE post_excerpt LIKE %s", $excerpt)
	);
	return $result;
}
add_action('wp_ajax_data_fetch_event', 'data_fetch_event');
add_action('wp_ajax_nopriv_data_fetch_event', 'data_fetch_event');
function data_fetch_event(){ 

	$offset = 0; $posts_per_page = 9;
	if($_POST['year']==date('Y')){
		$date_val=date('m/dY');
		
	}else{
		$date_val = '12/31/' . $_POST['year'];
	}
		$meta_query = [
    [
        'key' => 'start_date',
        'value' => array(date('Y/m/d', strtotime('1/1/'.$_POST['year'])), date('Y/m/d', strtotime($date_val))),
        'compare' => 'BETWEEN',
        'type' => 'DATE',
    ]
  ];

if (empty($_POST['year'])) {
	$todayP = date('m/d/Y');
    	$meta_query = [
    [
        'key' => 'start_date',
        'value' => date('Y/m/d', strtotime($todayP)),
        'compare' => '<',
        'type' => 'DATE',
    ]
  ];
}
	if($_POST['tab_set']== 'webinar-blog'){
		$getPastEvent = new WP_Query(array('post_type' => 'event', 'post_status' => 'publish', 'order' => $_POST['order'], 'posts_per_page' => $posts_per_page, 'meta_query' => $meta_query, 'tax_query' => array(
			array(
				'taxonomy' => 'events',
				'field' => 'slug',
				'terms' => 'event-webinar',
			)
		),));
	}else{
		$getPastEvent = new WP_Query(array('post_type' => 'event', 'post_status' => 'publish', 'order' => $_POST['order'], 'posts_per_page' => $posts_per_page,'meta_query' => $meta_query,
		'tax_query' => array(
			array(
				'taxonomy' => 'events',
				'field' => 'slug',
				'terms' => 'event-webinar', 'operator' => 'NOT IN',
			)
		),));
	}

	                    $row = 1; ?>
<div class="row">
    <?php if ( $getPastEvent->have_posts() ){
                    while ( $getPastEvent->have_posts() ) : 
                    $getPastEvent->the_post(); 
					
					if (has_post_thumbnail()) {
						$feat_image_url = wp_get_attachment_url(get_post_thumbnail_id(),'full');
					}
					$start_date = get_field('start_date');
					$date1 = date("F j", strtotime($start_date));
					$date1Year = date("Y", strtotime($start_date));
					
					$end_date = get_field('end_date');
					$date2 = date("j", strtotime($end_date));
					$event_link = get_field('read_more_link');
                    $chckdate2 = date("j", strtotime($start_date));
					
					if(!empty($event_link['title']))
							{
								$eventlptitle=$event_link['title'].' <i class="fa-solid fa-chevron-right"></i>';
							}
							else
							{
								$eventlptitle='LEARN MORE <i class="fa-solid fa-chevron-right"></i>';
							}
						
					?>

    <div class="col-12 col-sm-8 col-md-6 col-lg-4">
        <div class="card">
            <div class="card-img-calander">
                <img class="card-img-top" src="<?php echo $feat_image_url; ?>" class="calander-img img-fluid"
                    alt="Bologna">

                <div class="calander-date">
                    <div class="date">
                        <p><i class="fa fa-calendar-days" aria-hidden="true"></i><?php
						if (!empty($start_date)) {
							echo $date1;
						}
						if ($start_date != $end_date) {
						if($chckdate2 != $date2){
						if (!empty($end_date)) { ?>-<?php echo $date2;} }
						}
							echo ", ".$date1Year;
						?></p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <?php if($_POST['tab_set'] != 'webinar-blog'){ ?>
                <p class="card-text-location"><i class="fa-sharp fa-solid fa-location-dot"></i><strong>LOCATION:
                    </strong><?php echo the_field(
						"place"
							); ?></p><?php } ?>
                <p class="card-text"><?php if ($event_link) {

						$event_link_url = $event_link["url"];
						$event_link_target = $event_link["target"]
							? $event_link["target"]
							: "_self";
						?>
                    <a href="<?php echo esc_url($event_link_url); ?>" target="<?php echo esc_attr(
						$event_link_target
					); ?>"><?php echo get_the_title(); ?></a><?php
					} else {
						echo get_the_title();
					} ?>
                </p>
                <div class="learn-more-link">
                    <a href="<?php echo esc_url($event_link_url); ?>" target="<?php echo esc_attr(
						$event_link_target
					); ?>" class="card-link"><?php echo $eventlptitle;?></a>
                </div>
            </div>
        </div>
    </div>

    <?php 
		endwhile;
				wp_reset_postdata();?>
</div>
<?php 
	}  else {
			if ($_POST['tab_set'] == 'webinar-blog') {
				echo "<div class='for-justify-content-results'><h3 class='text-center'>No Webinar Found.</h3>";
			} else {
				echo "<div class='for-justify-content-results'><h3 class='text-center'>No Events Found.</h3>";
			}
		}
		 if ($getPastEvent->found_posts > 9) { ?>

<div class="row blog_post_row "></div>
<div class="col-lg-12 text-center">
    <div tabindex="all" id="loaderOnScroll" class="more_posts more_all ">
        <a onclick="load_more_postsdata()" class="gz-btn btn-blue text-center load-more-btn loadval"
            href="javascript:void(0);" id="9">Load More</a>
    </div>

</div>
<?php } 
	 die();
}
add_action('wp_ajax_get_blog_load_more_post_event', 'get_blog_load_more_post_event');
add_action('wp_ajax_nopriv_get_blog_load_more_post_event', 'get_blog_load_more_post_event');
function get_blog_load_more_post_event(){ 
	$offset = $_POST['offset']; 
	$posts_per_page = 9;
	if ($_POST['year'] == date('Y')) {
		$date_val = date('m/dY');
	} else {
		$date_val = '12/31/' . $_POST['year'];
	}
			
				$meta_query = [
    [
        'key' => 'start_date',
        'value' => array(date('Y/m/d', strtotime('1/1/'.$_POST['year'])), date('Y/m/d', strtotime($date_val))),
        'compare' => 'BETWEEN',
        'type' => 'DATE',
    ]
  ];

if (empty($_POST['year'])) {
	$todayP = date('m/d/Y');
    	$meta_query = [
    [
        'key' => 'start_date',
        'value' => date('Y/m/d', strtotime($todayP)),
        'compare' => '<',
        'type' => 'DATE',
    ]
  ];
}
	if ($_POST['tab_set'] == 'webinar-blog') {
	$getPastEvent = new WP_Query(array('post_type' => 'event', 'post_status' => 'publish', 'order' => 'DESC','offset' => $_POST['offset'], 'posts_per_page' => $posts_per_page, 'meta_query' => $meta_query, 'tax_query' => array(
			array(
				'taxonomy' => 'events',
				'field' => 'slug',
				'terms' => 'event-webinar'
			)
		),));
		}else{
	$getPastEvent = new WP_Query(array('post_type' => 'event', 'post_status' => 'publish', 'order' => 'DESC', 'offset' => $_POST['offset'], 'posts_per_page' => $posts_per_page, 'meta_query' => $meta_query, 'tax_query' => array(
					array(
						'taxonomy' => 'events',
						'field' => 'slug',
						'terms' => 'event-webinar', 'operator' => 'NOT IN',
					)
				),));
			}
	                    $row = 1; 
						
						if ( $getPastEvent->have_posts() ){
                    while ( $getPastEvent->have_posts() ) : 
                    $getPastEvent->the_post(); 
					
					
					if (has_post_thumbnail()) {
						$feat_image_url = wp_get_attachment_url(get_post_thumbnail_id());
					}
					$start_date = get_field('start_date');
					$date1 = date("F j", strtotime($start_date));
					$date1Year = date("Y", strtotime($start_date));
					$end_date = get_field('end_date');
					$date2 = date("j", strtotime($end_date));
					$event_link = get_field('read_more_link'); ?>

<div class="col-12 col-sm-8 col-md-6 col-lg-4">
    <div class="card">
        <div class="card-img-calander">
            <img class="card-img-top" src="<?php echo $feat_image_url; ?>" class="calander-img img-fluid" alt="Bologna">

            <div class="calander-date">
                <div class="date">
                    <p><i class="fa fa-calendar-days" aria-hidden="true"></i><?php
						if (!empty($start_date)) {
							echo $date1;
						}
						if (!empty($end_date)) { ?>-<?php echo $date2;}
						echo ", ".$date1Year;
						?></p>
                </div>
            </div>
        </div>

        <div class="card-body">
            <?php if($_POST['tab_set'] != 'webinar-blog'){ ?>
            <p class="card-text-location"><i class="fa-sharp fa-solid fa-location-dot"></i><strong>LOCATION: </strong><?php echo the_field(
						"place"
							); ?></p><?php } ?>
            <p class="card-text"><?php if ($event_link) {

						$event_link_url = $event_link["url"];
						$event_link_target = $event_link["target"]
							? $event_link["target"]
							: "_self";
						?>
                <a href="<?php echo esc_url($event_link_url); ?>" target="<?php echo esc_attr(
						$event_link_target
					); ?>"><?php echo get_the_title(); ?></a><?php
					} else {
						echo get_the_title();
					} ?>
            </p>
            <div class="learn-more-link">
                <a href="<?php echo esc_url($event_link_url); ?>" target="<?php echo esc_attr(
						$event_link_target
					); ?>" class="card-link">LEARN MORE <i class="fa-solid fa-chevron-right"></i> </a>
            </div>
        </div>
    </div>
</div>

<?php
		endwhile;
		
				wp_reset_postdata();?>

<?php 
	}  else {
			if ($_POST['tab_set'] == 'webinar-blog') {
				echo "<div class='for-justify-content-results'><h3 class='text-center'>No Webinars Found.</h3>";
			}else{
				echo "<div class='for-justify-content-results'><h3 class='text-center'>No Events Found.</h3>";
			}
		}
		if($getPastEvent->found_posts  > 9) { ?>
<!-- <div class="row blog_post_row"></div> -->
<div class="col-lg-12 text-center">
    <div tabindex="all" id="loaderOnScroll" class="more_posts more_all">
        <a onclick="load_more_postsdata()" class="gz-btn btn-blue text-center load-more-btn loadval"
            href="javascript:void(0);">Load More</a>
    </div>
</div>
<?php }
	 die();
}

function truncateString($str, $maxWords) {
	// Split the string into words
	$words = explode(' ', $str);
  
	if (count($words) > $maxWords) {
	  // Join the first $maxWords words and add ellipsis
	  $str = implode(' ', array_slice($words, 0, $maxWords)) . '...';
	}
	return $str;
  }
  
function custom_post_maginate_Resource() {
    $labels = array(
        'name'               => _x( 'Maginate Resources', 'post type general name' ),
        'singular_name'      => _x( 'Maginate Resources', 'post type singular name' ),
        'add_new'            => _x( 'Add New', 'Maginate Resources' ),
        'add_new_item'       => __( 'Add New' ),
        'edit_item'          => __( 'Edit' ),
        'new_item'           => __( 'New' ),
        'all_items'          => __( 'All Maginate Resources' ),
        'view_item'          => __( 'View' ),
        'search_items'       => __( 'Search' ),
        'not_found'          => __( 'No Maginate Resources found' ),
        'not_found_in_trash' => __( 'No Maginate Resources found in the Trash' ), 
        'parent_item_colon'  => __( 'Parent' ),
        'menu_name'          => 'Maginate Resources'
    );
    $supports = array( 'title','editor','custom-fields','comments','revisions','page-attributes','thumbnail'); 
    $details = array(
        'labels'        => $labels,
        'description'   => 'Everything you want to know about a Maginate Resources!',
        'public'        => true,
        'menu_position' => 6,
        'supports'      => $supports,
        'rewrite' => array("slug" => "maginate_resource", 'with_front' => false),
		'show_ui'             => true,  // Still show in admin interface
		'public'              => false, // Not publicly accessible
        'publicly_queryable'  => false, // No permalinks generated
        'has_archive'   => true,
        'show_in_rest' => true,
    );
    register_post_type( 'maginate_resource', $details ); 
}
add_action( 'init', 'custom_post_maginate_Resource' );

function taxonomies_custom_post_maginate_resource() {
    $labels = array(
        'name'              => _x( 'Categories', 'taxonomy general name' ),
        'singular_name'     => _x( 'Categories', 'taxonomy singular name' ),
        'search_items'      => __( 'Search' ),
        'all_items'         => __( 'All' ),
        'parent_item'       => __( 'Parent' ),
        'parent_item_colon' => __( 'Parent:' ),
        'edit_item'         => __( 'Edit' ), 
        'update_item'       => __( 'Update' ),
        'add_new_item'      => __( 'Add New' ),
        'new_item_name'     => __( 'New' ),
        'menu_name'         => __( 'Categories' ),
    );
    $args = array('labels' => $labels,'hierarchical' => true,);
    register_taxonomy( 'maginateResource_category', 'maginate_resource', $args );
}
add_action( 'init', 'taxonomies_custom_post_maginate_resource', 0 );
/* asset template update*/
add_action('wp_ajax_template_asset_load', 'template_asset_load');
add_action('wp_ajax_nopriv_template_asset_load', 'template_asset_load');
function template_asset_load(){

		global $post;
	static $count = 0;
	$term_slug=$_REQUEST['term_slug'];

if($_REQUEST['term_slug'] == 'premium_email')
{
	$term_slug=array('email-templates-premium-ecommerce','   email-templates-premium-healthcare','email-templates-premium-it','email-templates-premium-manufacturing');
}
if($_REQUEST['term_slug'] == 'premium_landing')
{
	$term_slug=array('landing-pages-premium-ecommerce','   landing-pages-premium-healthcare','landing-pages-premium-it','landing-pages-premium-manufacturing');
}	
if($_REQUEST['free_temp'])
{
	$term_slug=$_REQUEST['free_temp'];
}
else
{
	 if($_REQUEST['blank_val'] == 'premium_landing'){
	$term_slug=array('landing-pages-premium-ecommerce','   landing-pages-premium-healthcare','landing-pages-premium-it','landing-pages-premium-manufacturing');
	 }
	  if($_REQUEST['blank_val'] == 'premium_email'){
	$term_slug=array('email-templates-premium-ecommerce','   email-templates-premium-healthcare','email-templates-premium-it','email-templates-premium-manufacturing');
	 }
}	
	$platformExcel_customers_customIntegration_html = '<div class="platformExcel_customers_customIntegration_carousel owl-carousel owl-theme">';
	$platformExcel_customers_customIntegrationArgs = array('post_type' => 'carousel-slider','order' => 'DESC','post_status' => 'publish','posts_per_page' => -1,'tax_query' => array(array('taxonomy' => 'carousel_slider_category','field' => 'slug', 'terms' => $term_slug),)); 
	$get_logos = new WP_Query($platformExcel_customers_customIntegrationArgs); 
	if ( $get_logos->have_posts() ) {
		 while($get_logos->have_posts()) : $get_logos->the_post();
			$platformExcel_customers_customIntegration_html .= '<div class="platform-item-container"><div class="platform-item">'.get_field('short_description').'</div></div>';
		 endwhile;
		 $count++; 
	}//RESET YOUR QUERY VARS
	wp_reset_query();		
	$platformExcel_customers_customIntegration_html .= '</div>';
	echo $platformExcel_customers_customIntegration_html;	
	wp_die();   
} 
 // Replace all <b> and </b> with <strong> and </strong>
function replace_b_with_strongCustom($content) {
    $content = str_replace(['<b>', '</b>'], ['<strong>', '</strong>'], $content);
    return $content;
}
add_filter('content_save_pre', 'replace_b_with_strongCustom');

function multi_cust_post_type_404_redirect() {
    // Define an array of custom post type slugs you want to disable
    $disabled_post_types = array( 'team_testimonial', 'flip', 'faq-details','client','carousel-slider','services-platforms' );
    
    // Check if the current post type is in the array
    if ( is_singular( $disabled_post_types ) ) {
        global $wp_query;
        $wp_query->set_404();
        status_header( 404 );
        nocache_headers();
        include( get_query_template( '404' ) );
        exit;
    }
}
add_action( 'template_redirect', 'multi_cust_post_type_404_redirect' );

// Resource Hub component call
add_action('add_meta_boxes', 'add_pages_meta_box_resource');

function add_pages_meta_box_resource() {
    add_meta_box(
        'pages_meta_box',           // Unique ID
        'Select Pages',             // Box title
        'render_pages_meta_box_resource',    // Content callback
        'resource',                 // Post type
        'normal',                   // Context
        'high'                      // Priority
    );
}

// Render the meta box content as checkboxes
function render_pages_meta_box_resource($post) {
    // Retrieve the existing value of the 'pages' meta field (as an array)
    $selected_pages = get_post_meta($post->ID, 'pages', true) ?: [];

    // Get all pages
    $pages = get_posts([
        'post_type'   => 'page',
        'post_status' => 'publish',
        'numberposts' => -1 // Get all pages
    ]);

    // Display checkboxes for each page
    echo '<div>';
    foreach ($pages as $page) {
        // Check if this page is selected
        $checked = in_array($page->ID, $selected_pages) ? 'checked' : '';
        echo '<label>';
        echo '<input type="checkbox" name="pages[]" value="' . esc_attr($page->ID) . '" ' . $checked . '>';
        echo esc_html(get_the_title($page->ID));
        echo '</label><br>';
    }
    echo '</div>';
}

// Save the selected pages when the post is saved
add_action('save_post', 'save_pages_meta_resource');

function save_pages_meta_resource($post_id) {
    // Check if the current user is allowed to edit the post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check if the pages field is set in the POST data
    if (isset($_POST['pages'])) {
        // Sanitize and update the meta field with selected pages (handle multiple values)
        $selected_pages = array_map('intval', $_POST['pages']);
        update_post_meta($post_id, 'pages', $selected_pages);
    } else {
        // If no pages are selected, delete the meta field
        delete_post_meta($post_id, 'pages');
    }
}

require_once get_stylesheet_directory() . '/new-template/components/resource-hub/resource-posts-design.php';
require_once get_stylesheet_directory() . '/new-template/components/resource-hub/casestuyd-resource.php';
require_once get_stylesheet_directory() . '/new-template/components/flip-card/flipcard.php';
require_once get_stylesheet_directory() . '/new-template/components/salesforce-slider/salesforce-slider.php';

function add_post_template_support() {
    add_post_type_support('post', 'page-attributes'); // Enables "Template" dropdown for posts
	add_post_type_support('resource', 'page-attributes'); // Enables "Template" dropdown for posts  is line k baad
}
add_action('init', 'add_post_template_support');

function preload_banner_slider_shortcode() {
    $output = '';

    $args = array(
        'post_type'      => 'banner-slider',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );

    $slider_posts = get_posts($args);

    foreach ($slider_posts as $post) {
        $image_url = get_the_post_thumbnail_url($post->ID, 'full');
        if ($image_url) {
            // Append ver=1 to the image URL
            $versioned_url = $image_url . (strpos($image_url, '?') === false ? '?ver=1' : '&ver=1');
            $output .= '<link rel="preload" as="image" fetchpriority="high" href="' . esc_url($versioned_url) . '">' . "\n";
        }
    }

    return $output;
}
add_shortcode('preload_banners', 'preload_banner_slider_shortcode');

// You tube streambox code 
// Add admin menu
add_action('admin_menu', 'custom_html_footer_menu');

function custom_html_footer_menu() {
    add_options_page(
        'Custom Footer Code',
        'Custom Footer Code',
        'manage_options',
        'custom-html-footer',
        'custom_html_footer_page'
    );
}

// Enqueue CodeMirror for the admin page
add_action('admin_enqueue_scripts', 'custom_html_footer_admin_scripts');

function custom_html_footer_admin_scripts($hook) {
    // Only load on our settings page
    if ($hook != 'settings_page_custom-html-footer') {
        return;
    }
    
    // Enqueue CodeMirror
    wp_enqueue_script('code-editor');
    wp_enqueue_style('code-editor');
    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_style('wp-codemirror');
}

// Create the settings page

// ------------------ Robust event-based YouTube widget ------------------

// Helper: parse incoming datetime-local or space-separated datetime to DateTime (Asia/Kolkata)
function cfw_parse_ist_datetime($raw) {
    if (empty($raw)) return false;

    // Normalize: browsers send "YYYY-MM-DDTHH:MM". Convert T -> space.
    $normalized = str_replace('T', ' ', trim($raw));

    // Try common formats
    $formats = ['Y-m-d H:i:s', 'Y-m-d H:i'];
    foreach ($formats as $fmt) {
        $dt = DateTime::createFromFormat($fmt, $normalized, new DateTimeZone('Asia/Kolkata'));
        if ($dt && $dt->getLastErrors()['warning_count'] === 0 && $dt->getLastErrors()['error_count'] === 0) {
            return $dt;
        }
    }

    // As a last resort, try constructor (may interpret timezone)
    try {
        $dt = new DateTime($normalized, new DateTimeZone('Asia/Kolkata'));
        return $dt;
    } catch (Exception $e) {
        return false;
    }
}

// 1) Auto-disable checkbox if event passed
add_action('init', function() {
    $enabled = get_option('custom_html_footer_enabled', 0);
    $event_raw = get_option('custom_event_datetime', '');

    if ($enabled && !empty($event_raw)) {
        $dt = cfw_parse_ist_datetime($event_raw);
        if ($dt) {
            $event_timestamp_utc = $dt->setTimezone(new DateTimeZone('UTC'))->getTimestamp();
            if (time() >= $event_timestamp_utc) {
                update_option('custom_html_footer_enabled', 0);
            }
        }
    }
});

// 2) Admin Menu
add_action('admin_menu', function() {
    add_menu_page(
        'Custom Footer Settings',
        'Custom Footer',
        'manage_options',
        'custom-footer-settings',
        'custom_html_footer_page',
        'dashicons-editor-code',
        80
    );
});

// 3) Admin Page — normalize and store datetime consistently, show confirmation
function custom_html_footer_page() {
    if (isset($_POST['submit'])) {
        // sanitize
        $enabled = isset($_POST['custom_html_footer_enabled']) ? 1 : 0;
        $youtube = isset($_POST['custom_youtube_link']) ? sanitize_text_field($_POST['custom_youtube_link']) : '';
        $button = isset($_POST['custom_button_link']) ? sanitize_text_field($_POST['custom_button_link']) : '';
        $raw_dt = isset($_POST['custom_event_datetime']) ? trim($_POST['custom_event_datetime']) : '';

        // Parse and normalize to "Y-m-d H:i" in IST before saving
        $dt = cfw_parse_ist_datetime($raw_dt);
        if ($raw_dt !== '' && !$dt) {
            echo '<div class="notice notice-error"><p>Invalid event date/time format. Use the picker in admin (browser will produce correct format).</p></div>';
        } else {
            update_option('custom_html_footer_enabled', $enabled);
            update_option('custom_youtube_link', $youtube);
            update_option('custom_button_link', $button);

            if ($dt) {
                // store as "Y-m-d H:i" (IST)
                $store = $dt->setTimezone(new DateTimeZone('Asia/Kolkata'))->format('Y-m-d H:i');
                update_option('custom_event_datetime', $store);
            } else {
                // empty - clear
                update_option('custom_event_datetime', '');
            }

            echo '<div class="notice notice-success"><p>Settings saved!</p></div>';

            // Show saved times for verification
            if ($dt) {
                $ist_display = $dt->setTimezone(new DateTimeZone('Asia/Kolkata'))->format('Y-m-d H:i T');
                $utc_display = $dt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i T');
                echo '<div class="notice notice-info"><p>Saved event (IST): <strong>'.esc_html($ist_display).'</strong> — which is (UTC): <strong>'.esc_html($utc_display).'</strong></p></div>';
            }
        }
    }

    $enabled = get_option('custom_html_footer_enabled', 0);
    $youtube_link = get_option('custom_youtube_link', '');
    $button_link = get_option('custom_button_link', '');
    $event_datetime = get_option('custom_event_datetime', ''); // stored as "Y-m-d H:i" IST
    ?>
    <div class="wrap">
        <h1>Custom Footer Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('custom_html_footer_save', 'custom_html_footer_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Enable Widget</th>
                    <td>
                        <label>
                            <input type="checkbox" name="custom_html_footer_enabled" value="1" <?php checked($enabled, 1); ?> />
                            Enable YouTube widget site-wide
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">YouTube URL</th>
                    <td>
                        <input type="url" name="custom_youtube_link" value="<?php echo esc_attr($youtube_link); ?>" class="regular-text" placeholder="https://www.youtube.com/embed/VIDEOID" />
                        <p class="description">Enter the full YouTube embed URL to show in the widget.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Button Link</th>
                    <td>
                        <input type="url" name="custom_button_link" value="<?php echo esc_attr($button_link); ?>" class="regular-text" placeholder="https://example.com/" />
                        <p class="description">Optional: Add a link for the “Register Now” button.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Event Date & Time (IST)</th>
                    <td>
                        <!-- If we have stored "Y-m-d H:i", convert to datetime-local's expected format "Y-m-d\TH:i" -->
                        <?php
                        $dt_local_val = '';
                        if (!empty($event_datetime)) {
                            $dt_obj = DateTime::createFromFormat('Y-m-d H:i', $event_datetime, new DateTimeZone('Asia/Kolkata'));
                            if ($dt_obj) $dt_local_val = $dt_obj->format('Y-m-d\TH:i');
                        }
                        ?>
                        <input type="datetime-local" name="custom_event_datetime" value="<?php echo esc_attr($dt_local_val); ?>" class="regular-text" />
                        <p class="description">Select the event date & time in IST (India Standard Time). Widget will display until this time.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
    <?php
}
// 4) Frontend widget injection — robust parsing and server-side check
add_action('wp_footer', function() {
    $enabled = get_option('custom_html_footer_enabled', 0);
    $youtube_link = get_option('custom_youtube_link', '');
    $button_link = get_option('custom_button_link', '');
    $event_raw = get_option('custom_event_datetime', '');

    // Basic checks
    if (!$enabled || empty($youtube_link)) return;
    if (empty($event_raw)) return;

    $dt = cfw_parse_ist_datetime($event_raw);
    if (!$dt) return;

    // Convert to UTC timestamp for comparisons and client-side JS
    $event_timestamp_utc = $dt->setTimezone(new DateTimeZone('UTC'))->getTimestamp();

    // If event already passed on server, uncheck and do not display
    if (time() >= $event_timestamp_utc) {
        update_option('custom_html_footer_enabled', 0);
        return;
    }
    ?>
    <!-- YouTube Widget -->
    <div id="video-widget" class="video-widget minimized" style="position:fixed; bottom:100px; right:20px; width:320px; background:#000; border-radius:8px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.2); z-index:99999; transition: width 0.3s,height 0.3s;">
        <div class="video-header" style="padding:6px 8px; background:#111; color:#fff; display:flex; justify-content:space-between; align-items:center; height:40px;">
            <span style="font-weight:600;">🎥 Live Webinar</span>
            <div class="video-controls" style="display:flex; gap:6px;">
                <button id="expand-btn" aria-label="Expand" style="background:transparent;border:0;color:#fff;cursor:pointer;">⤢</button>
                <button id="minimize-btn" aria-label="Minimize" style="background:transparent;border:0;color:#fff;cursor:pointer;">_</button>
                <button id="close-btn" aria-label="Close" style="background:transparent;border:0;color:#fff;cursor:pointer;">×</button>
            </div>
        </div>
        <div class="video-body" style="height:calc(100% - 40px);">
            <iframe id="video-iframe" src="<?php echo esc_url($youtube_link); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="width:100%; height:100%; display:block;"></iframe>
        </div>
    </div>

    <?php if (!empty($button_link)): ?>
    <a id="register-btn-container" href="<?php echo esc_url($button_link); ?>" target="_blank" style="position:fixed; bottom:50px; right:20px; z-index:99999;">
        <button id="register-btn" style="padding:12px 20px; background:#e53935; color:white; border:none; cursor:pointer; border-radius:6px; font-size:15px; font-weight:700; box-shadow:0 4px 10px rgba(0,0,0,0.18);">Register Now</button>
    </a>
    <?php endif; ?>

    <script>
    (function(){
        // eventTimestampUTC injected by server (ms)
        const eventTimestamp = <?php echo intval($event_timestamp_utc * 1000); ?>;
        document.addEventListener('DOMContentLoaded', function(){
            const widget = document.getElementById('video-widget');
            const expandBtn = document.getElementById('expand-btn');
            const minimizeBtn = document.getElementById('minimize-btn');
            const closeBtn = document.getElementById('close-btn');
            const iframe = document.getElementById('video-iframe');
            const registerContainer = document.getElementById('register-btn-container');

            function setCookie(name,value,days){
                let expires="";
                if(days){ const d=new Date(); d.setTime(d.getTime()+(days*24*60*60*1000)); expires="; expires="+d.toUTCString(); }
                document.cookie=name+"="+(value||"")+expires+"; path=/";
            }
            function getCookie(name){
                const nameEQ=name+"=";
                const ca=document.cookie.split(';');
                for(let i=0;i<ca.length;i++){ let c=ca[i].trim(); if(c.indexOf(nameEQ)===0) return c.substring(nameEQ.length); }
                return null;
            }

            // If user previously closed -> hide
            if (getCookie('videoWidgetClosed') === 'true') {
                if(widget) widget.style.display = 'none';
                if(registerContainer) registerContainer.style.display = 'none';
                return;
            }

            if (registerContainer) registerContainer.style.display = 'block';

            if (expandBtn) expandBtn.addEventListener('click', function(){
                widget.classList.remove('minimized');
                widget.classList.add('expanded');
                widget.style.width = '640px';
                widget.style.height = '360px';
            });
            if (minimizeBtn) minimizeBtn.addEventListener('click', function(){
                widget.classList.remove('expanded');
                widget.classList.add('minimized');
                widget.style.width = '320px';
                widget.style.height = 'auto';
            });
            if (closeBtn) closeBtn.addEventListener('click', function(){
                if (iframe) iframe.src = '';
                if (widget) widget.style.display = 'none';
                if (registerContainer) registerContainer.style.display = 'none';
                setCookie('videoWidgetClosed','true',7);
            });

            // Client-side safety: hide when event passed (using UTC)
            const nowUTC = Date.now();
            if (nowUTC >= eventTimestamp) {
                if (widget) widget.style.display = 'none';
                if (registerContainer) registerContainer.style.display = 'none';
                setCookie('videoWidgetClosed','true',7);
            }
        });
    })();
    </script>

    <style>
    @media screen and (max-width:480px){
        #video-widget, #register-btn-container { display:none !important; }
    }
    </style>
    <?php
});
// function expose_aioseo_meta_to_rest() {
//     $meta_keys = [
//         '_aioseo_title',
//         '_aioseo_description',
//         '_aioseo_keywords'
//     ];

//     foreach ($meta_keys as $key) {
//         register_post_meta('post', $key, [
//             'show_in_rest' => true,
//             'single' => true,
//             'type' => 'string',
//         ]);
//     }
// }
// add_action('init', 'expose_aioseo_meta_to_rest');
add_filter( 'wxr_export_skip_postmeta', function( $skip, $meta_key, $meta ) {

    // Only during export
    if (
        is_admin() &&
        isset($_GET['download']) &&
        isset($_GET['content'])
    ) {
        // Skip NULL values only
        if ( is_null( $meta->meta_value ) ) {
            return true;
        }
    }

    return $skip;

}, 10, 3 );




/**
 * Exclude specific resource posts from the AIOSEO XML sitemap.
 * URLs are resolved to post IDs once per request via a static cache.
 */
add_filter( 'aioseo_sitemap_exclude_posts', 'gz_sitemap_exclude_resource_posts', 10, 2 );
function gz_sitemap_exclude_resource_posts( $ids, $type ) {
    static $excluded_ids = null;

    if ( null === $excluded_ids ) {
        $slugs = [
            'the-unabbreviated-guide-to-ideaspro-and-how-it-reforms-ideas-management-in-salesforce',
            'a-definitive-guide-to-mobile-app-development',
            'the-ultimate-guide-to-tableau-cloud-migration-step-by-step-checklist-for-success',
            'managing-and-optimizing-subscription-module-in-magento-2',
            'cheat-sheet-ai-tools-for-boosting-marketing-roi',
            'enhancing-salesforce-cpq-with-advanced-features-for-a-leading-technology-company-thanks',
            'expert-qa-services-tailored-to-your-business-goals',
            'revamping-community-design-for-a-stellar-customer-experience',
            'delivering-a-personalized-cx-by-implementing-integrating-adobe-platforms',
            'salesforce-revenue-cloud-advanced-rca-services',
            'the-future-of-marketing-automation-in-australia-new-zealand',
            'the-rise-of-marketing-automation-in-the-healthcare-industry',
            'syncing-caspio-database-to-moodle-cms-seamlessly-with-webhook',
            'ai-adoption-framework-for-design-in-2026-and-beyond',
            'boosting-marketing-campaign-efficiency-by-42-with-m-cleans-data-deduplication',
            'executing-a-seamless-migration-from-salesforce-to-ms-dynamics-with-marketo-sync',
            'the-all-encompassing-guide-to-moving-your-ecommerce-business-to-salesforce-commerce-cloud-2',
            'creating-an-engaging-experience-for-non-tech-users-on-an-online-community',
            'zendesk-vs-salesforce-service-cloud-a-comprehensive-guide-to-choosing-the-right-solution',
            'increasing-engagement-and-boosting-traffic-with-interactive-ui-ux',
            'online-community-engagement-trends-in-2026-signals-shifts-and-whats-next',
            'a-comprehensive-guide-to-ai-deployment',
            'salesforce-professional-services',
            'customized-community-gamification-for-an-identity-access-management-company',
            'ai-powered-communities-your-gateway-to-delivering-superlative-connected-experiences',
            'salesforce-data-cloud-implementation-why-it-matters-and-how-to-do-it-right',
            'migrating-customer-community-from-jive-x-to-lithium-now-khoros-and-ensuring-a-smooth-onboarding',
            'enabling-effective-cross-functional-collaboration-for-a-leading-it-company',
            'the-dynamics-365-azure-integration-playbook-for-ai-ready-enterprises',
            'machine-learning-and-deep-learning-in-python-using-keras-and-tensorflow-2',
            'lets-understand-salesforce-service-cloud-and-jira-integration',
            'acing-online-success-with-e-commerce-personalization',
            'providing-it-help-desk-support-for-an-ai-search-company',
            'moderate-community-engagement-effectively-with-community-management',
            'email-to-case-advance-agent',
            'top-8-cybersecurity-predictions-2024-preparing-for-an-evolving-threatscape',
            'optimize-and-maintain-wordpress-website-for-car-racing-event',
            'why-is-community-analytics-vital-for-community-managers',
            'how-to-keep-your-crm-squeaky-clean',
            'agentforce-services',
            'a-tell-all-guide-to-shopify',
            'case-studies-case-studies-100-10-d3-seamless-migration-of-customer-community-from-khoros-to-higher-logic-to-enable-screen-reader-support-press-ctrlaltz-to-learn-about-keyboard-shortcuts-press-ctr',
            '7-easy-steps-to-implement-hubspot-successfully',
            'ai-powered-case-management-with-email-to-case-advance-e2ca-agent-delivering-connected-context-rich-efficient-service',
            'amplify-community-engagement-with-effective-gamification-elements-2',
            'automating-processes-using-selenium-to-streamline-the-marketing-campaigns-of-a-cloud-computing-company',
            'human-resource-analytics',
            'the-ultimate-guide-to-oracle-eloqua-implementation',
            'creating-aem-content-fragments-to-maintain-a-common-database-for-a-direct-to-home-broadcasting-service',
            'integrating-adobe-analytics-with-aem-using-adobe-experience-platform-launch',
            'complete-guide-to-tableau-next-the-executive-playbook-for-ai-driven-analytics',
            'tell-your-brand-story-with-engaging-videos',
            'shrinking-shopping-cart-abandonment-the-what-why-and-how',
            'empowering-marketing-strategy-with-an-omnichannel-dashboard-solution',
            'digital-marketing-trends-and-predictions-2024',
            'maximizing-salesforce-roi-10-actionable-ways-for-success',
            'building-cutting-edge-online-communities-for-delivering-superlative-customer-experiences',
            'your-ultimate-guide-to-navigating-top-ppc-advertising-networks',
            'marketing-trends-and-predictions-2026',
            'salesforce-service-cloud-transforming-call-centers',
            'marketing-trends-and-predictions-2025',
            'the-champion-of-software-performance-testing',
            'aws-web-application-and-tableau-integration-for-data-visibility-2',
            'optimizing-p2p-and-o2c-processes-on-netsuite-for-a-cloud-based-automation-testing-platform-provider',
            'the-12-step-blueprint-to-privacy-and-personalization',
            'leveraging-adobe-commerce-to-create-exceptional-shopping-experiences-and-maximize-roi',
            'transform-your-business-with-microsoft-copilot-the-future-of-productivity-and-innovation',
            'khoros-salesforce-connector',
            'how-to-drive-business-growth-with-online-communities',
            'automating-test-suites-to-reduce-execution-time-improve-quality-of-a-time-management-system',
            'from-learning-center-to-lead-magnet-how-wordpress-marketo-connector-converted-users-into-paying-customers',
            'ai-trends-and-predictions-2024',
            'salesforce-community-cloud-set-up-for-an-american-multinational-personal-finance-company',
            'the-snowflake-playbook-expert-insights-on-implementation-best-practices-and-security',
            'interactive-video-strategy-turn-viewers-into-buyers',
            'a-complete-career-guide-for-a-community-manager-aspirant',
            'ais-role-in-smarter-software-testing',
            'salesforce-marketing-cloud-success',
            'streamlining-hr-operations-and-enhancing-decision-making-with-a-feature-rich-hrms',
            'the-qa-shift-rethinking-software-quality-for-a-high-risk-ai-driven-world-in-2026',
            'integrating-wordpress-with-marketo-the-complete-guide',
            'the-ultimate-guide-to-navigating-the-cybersecurity-threat-landscape-this-year-beyond',
            'ds-web-development',
            'website-redesign-aws-migration-for-a-leading-ecommerce-website',
            'feature-enrichment-and-maintenance-of-a-sitecore-website-for-a-leading-montessori-society',
            'quality-assurance-services',
            'project-management-2024-mastering-the-trends-for-enhanced-efficiency-and-innovation',
            'building-a-security-operations-center-enhancing-threat-detection-capabilities-for-a-global-digital-services-provider',
            'simplify-salesforce-with-autopex-conversational-ai-for-smarter-operations',
            'navigating-the-modern-marketing-ecosystem-a-marketing-leaders-playbook',
            'improved-marketing-campaign-efficiency-by-54-with-m-cleans-data-standardization',
            'setting-up-salesforce-voice-with-amazon-connect-for-superior-support-operations',
            'omni-channel-analytics',
            'the-comprehensive-salesforce-data-cloud-playbook-industry-impact-security-principles-and-implementation-steps',
            'how-digital-transformation-is-revolutionising-australian-businesses',
            'ecommerce-marketing-strategies-the-ultimate-guide-to-growing-your-business',
            'building-seamless-community-experiences-with-smooth-migration-from-jive-to-salesforce',
            'ignite-your-brands-success-strategies-for-crafting-a-memorable-identity-in-2024',
            'testing-web-applications-migration-from-on-premise-to-aws-servers-for-a-retail-giant',
            'levergaging',
            'the-essentials-of-online-community-moderation-migration',
            'the-complete-guide-to-adobe-analytics',
            'a-10-step-checklist-to-5x-your-marketing-roi',
            'magento-marketo-connector',
            'a-comprehensive-guide-to-demystifying-it-service-management',
            'enabling-user-tracking-for-multiple-events-with-google-analytics-and-gtm',
            'enhancing-efficiency-and-streamlining-translation-process-for-a-global-standards-consortium-2',
            'the-ultimate-dynamics-365-implementation-checklist',
            'improving-end-to-end-user-experience-and-streamlining-customer-onboarding-process-for-a-fitness-club',
            'enabling-efficient-tier-2-and-3-support-for-a-leading-supplier-of-world-class-educational-resources',
            'influencer-alchemy-your-roadmap-to-b2b-marketing-success',
            'designing-a-world-for-everyone-why-inclusive-design-matters',
            'the-role-of-predictive-analytics-in-reinventing-marketing-success',
            'the-qa-maturity-model-building-a-culture-of-quality-across-the-software-lifecycle',
            'the-community-outlook-trends-predictions-for-2024',
            'clinical-and-population-health-analytics',
            'conversational-marketing-marketing-automation-the-perfect-duo-to-humanize-cx',
            'the-101-of-salesforce-integration-everything-that-you-need-to-know',
            'helping-a-leading-mobile-telecommunicationion-company-migrate-their-website-from-aem-6-5-to-ams',
            'migrating-from-legacy-cpq-to-revenue-cloud-advanced-a-strategic-guide',
            'spearheading-into-the-future-with-community-based-marketing-cbm',
            'revenue-cycle-managment',
            'hubspot-services-and-solutions',
            'the-future-of-marketing-automation-in-the-gaming-industry-a-tell-all-guide',
            '10-ecommerce-trends-to-watch-out-for-in-2022',
            'your-go-to-guide-for-all-things-salesforce-commerce-cloud',
            'successful-migration-from-jive-to-salesforce-lightning-community-cloud',
            'boomi-services',
            'a-guide-to-data-engineering-excellence-in-2025-and-beyond',
            'top-7-quality-assurance-trends-to-watch-out-for-in-2023',
            'increasing-community-engagement-by-55-with-a-customized-vanilla-ui',
            'reducing-revenue-leakage-and-claim-denials-for-a-community-health-center',
            'zendesks-2025-strategic-roadmap',
            'understanding-the-fundamentals-of-vulnerability-assessment-and-penetration-testing-vapt',
            'emerging-web-dev-trends-for-2022',
            'accelerating-mlops-platform-performance-with-an-ai-enhanced-analytics-chatbot',
            'implementing-salesforce-cdp-for-building-better-customer-relationships-a-tell-all-guide',
            'gamer-community-practices-to-grow-your-non-gamer-community',
            'customizing-madcap-themes-for-a-community-knowledge-base',
            'ai-in-marketing-leveraging-artificial-intelligence-for-business-success',
            'developing-inventory-management-system-and-redesigning-shopify-website-for-a-leading-wine-company',
            'move-your-existing-salesforce-and-jira-integration-to-sinergify',
            'healthcare-analytics-outlook-2025',
            'optimizing-sales-service-cloud-by-migrating-to-flow-for-a-saas-company',
            'salesforce-kb-khoros-connector',
            'helping-a-global-consulting-firm-deliver-an-enhanced-cx',
            'healthcare-analytics-outlook-2024',
            'omnichannel-analytics',
            'enhancing-user-experience-with-swift-migration-from-jive-to-salesforce',
            'code-quality-the-secret-behind-successful-software-testing',
            'healthcare-analytics',
            'healthcare-analytics-outlook-2023',
            'how-zendesk-will-reshape-customer-service-in-2023-beyond',
            'a-peek-into-salesforces-ai-evolution-elevating-business-success-with-ai-cloud',
            'the-power-of-short-form-videos-your-key-to-driving-conversions',
            'support-analytics',
            'the-ultimate-account-engagement-implementation-guide-for-marketing-success',
            'how-to-make-most-out-of-your-eloqua-investment-a-tell-all-guide',
            'transforming-user-training-how-interactive-modules-transformed-engagement-for-a-prominent-software-company',
            'implementing-the-right-marketing-solutions-a-tell-all-guide',
            'data-analytics-in-healthcare-overcoming-challenges-embracing-ai',
            'solving-unstructured-data-management-challenges-with-ai-and-ml',
            'the-ultimate-guide-to-revenue-operations',
            'optimizing-wordpress-website-for-a-major-real-estate-firm-to-enhance-user-experience',
            'setting-up-adobe-campaign-standard-for-a-leading-entertainment-company',
            'a-tell-all-guide-to-revolutionize-your-customer-support-with-ai',
            'migration-from-marketo-to-hubspot',
            'creating-seamless-data-sync-between-wordpress-and-marketo-for-exceptional-marketing',
            'tell-all-guide-for-recruiting-a-competent-community-manager',
            'unveiled-how-to-get-your-boomi-project-off-to-a-successful-start',
            'qa-software-testing-trends-2024',
            'new-google-analytics-4-the-definitive-guide',
            'google-ads-for-businesses',
            'changing-sales-landscape-evolution-of-salesforce-sales-cloud',
            'salesforce-manufacturing-cloud',
            'the-future-of-the-subscription-economy-a-glimpse-into-2024-and-beyond-with-zuora',
            'healthcare-analytics-outlook-2026',
            'achieve-30-revenue-growth-with-hubspot-and-netsuite-integration',
            'migration-of-data-from-khoros-to-insided',
            'delving-deeper-into-data-warehouses',
            'project-management-support-in-financial-systems-set-up-and-collateral-development',
            'optimize-wordpress-website-for-a-real-estate-firm',
            'improving-user-engagement-and-traffic-on-social-media',
            'seamlessly-migrating-user-community-from-khoros-to-zendesk',
            'redesigning-an-online-community-of-restaurant-reservation-service-company-to-enhance-ux-reduce-support-costs',
            'performing-vapt-assessment-for-a-leading-salesforce-powered-online-community',
            'elevate-your-content-marketing-strategy-with-a-high-converting-funnel',
            'migrating-to-ga4-fixing-gtm-for-a-premium-replacement-tires-company',
            'welcome-to-the-modern-day-era-of-ideas-management',
            'delivering-a-user-friendly-cx-with-custom-community-solutions',
            'navigating-the-hyperconnected-ecosystem',
            'the-all-encompassing-guide-to-do-market-research',
            'the-design-playbook-all-you-need-to-know-about-designing-engaging-digital-marketing-campaigns',
            'how-marketing-automation-is-changing-the-face-of-the-bfsi-industry-2',
            'decoding-the-101-of-online-community-data-protection',
            'top-8-future-predictions-that-will-reshape-the-community-landscape',
            'revenue-cloud-salesforce-cpq',
            'the-essential-guide-to-integrating-salesforce-with-your-ecommerce-platform',
            'unleashing-the-power-of-engaging-content-for-a-gate-automation-company',
            'the-future-of-azure-cloud-computing',
            'the-leaders-playbook-for-salesforce-data-cloud-now-data-360',
            'helping-a-fortune-500-technology-company-manage-their-1-5-million-dollar-advertising-spend',
            'accelerate-your-it-service-management-with-intelligent-automation-solutions',
            'understanding-salesforce-data-cloud-in-action-powering-real-time-insights-across-multi-cloud-environments',
            'thriving-in-2024-a-proven-game-plan-for-scaling-your-digital-commerce-operations',
            'migrate-from-jive-to-salesforce-lightning-cloud',
            'salesforce-sales-cloud-services',
            'delivering-superlative-customer-experiences-with-magento-crm-integration',
            'ai-driven-information-extraction-for-enhanced-decision-making',
            'navigating-projects-with-speed-a-complete-guide-to-agile-project-management',
            'the-future-of-marketing-automation-trends-to-look-out-for-in-2023-beyond',
            'reducing-manual-efforts-and-speeding-processing-time-by-30-with-ai-enabled-data-enrichment',
            'design-services-intuitive-modern-and-on-brand-designs-that-leave-a-lasting-impression',
            'the-complete-guide-to-content-marketing-from-creation-to-conversion',
            'analytics-outlook-2026',
            'enabling-the-idea-component-in-salesforce-lightning-for-a-leading-it-provider-2',
            'inspiring-brand-advocacy-by-creating-dynamic-online-communities',
            'applying-data-analytics-to-address-social-determinants-of-health-for-health-management',
            'salesforce-community-cloud-services',
            'implementing-chatgpt-insights-for-building-svelte-landing-pages',
            'fast-track-your-business-growth-with-marketing-cloud-account-engagement',
            'improving-email-deliverability-and-enhancing-customer-experience-with-marketo-for-a-business-analytics-platform-provider',
            'a-complete-guide-to-search-engine-marketing',
            'mastering-salesforce-content-builder-a-step-by-step-guide-to-dynamic-content-creation',
            'vulnerability-assessment-and-penetration',
            'velocity-scripting',
            'marketing-automation-checklist-9-steps-for-better-campaign-performance',
            'analytics-outlook-2023',
            'customer-support-analytics',
            'summarizing-text-from-learning-material-using-machine-learning',
            'analytics-outlook-2024',
            'improving-user-experience-with-a-customized-and-integrated-lithium-community',
            'the-ultimate-guide-to-grazittis-salesforce-products',
            'customer-experience-in-2023-and-beyond-top-trends-predictions-to-watch',
            'lightning-community',
            'improving-agent-case-management-by-30-with-email-to-case-advance-for-a-cybersecurity-company',
            'harness-ai-to-create-smarter-more-efficient-self-serve-websites',
            'analytics-outlook-2025',
            'building-a-custom-search-community-on-vanilla-forums',
            'improving-bulk-order-processing-with-salesforce-for-a-telecommunications-company',
            'mastering-ecommerce-growth-key-strategies-to-scale-with-salesforce-commerce-cloud',
            'improved-efficiency-by-optimizing-adobe-solutions-for-a-leading-ecommerce-platform',
            'project-managment-in-digital-era',
            'building-dynamic-communities-with-community-management-as-a-service',
            'predictive-analytics-2',
            'the-mops-playbook-for-fixing-marketo-salesforce-data-sync-challenges',
            'salesforce-data-migration-made-simple-the-complete-guide',
            'optimized-technical-seo-and-increased-traffic-for-a-premier-sports-equipment-retailer',
            'microsoft-dynamics-365-sales-the-definitive-guide-to-smarter-selling-and-seamless-crm-integration',
            'developing-hybrid-mobile-app-to-amplify-a-lifestyle-brands-digital-presence',
            'a-definitive-guide-to-a-successful-ppc-campaign',
            'the-adobe-marketo-engage-playbook-expert-insights-tips-and-strategies-for-marketers',
            'setting-up-a-customized-search-community-from-scratch-on-vanilla-forums-to-connect-family-members-globally',
            'streamlining-partner-management-process-for-a-leading-financial-services-provider',
            'how-ai-is-shaping-the-future-of-the-elearning-industry',
            'enabling-a-tech-service-provider-to-automate-sales-processes-with-ms-dynamics-2',
            'emerging-threats-in-cyber-space-in-2022',
            'redefining-marketing-with-ai-powered-marketing-automation',
            'microsoft-365-copilot-your-enterprise-readiness-checklist',
            'leveraged-ideas-and-jira-integration-to-streamline-customer-ideas-management',
            'the-ultimate-guide-to-creating-and-managing-forms-in-eloqua',
            'interactive-content-a-comprehensive-guide-to-engaging-your-audience',
            'engage-connect-convert-the-ultimate-guide-to-interactive-video-marketing',
            'creating-your-first-ai-agent-with-agentforce-a-beginners-blueprint',
            'salesforce-service-cloud-voice-services',
            'migration-to-salesforce-lightning',
            'a-definitive-guide-to-streamlining-your-ecommerce-operations-with-shopify-marketo-integration',
            'website-security-101',
            'unlock-your-digital-potential-with-the-right-web-development-partner',
            'restructuring-learning-material-for-optimal-engagement-knowledge-retention',
            'strategic-guide-to-salesforce-ai-adoption',
            'reimagining-the-future-with-digital-marketing-trends-predictions-for-2023',
            'improving-digital-marketing-across-apac-for-a-fintech-company',
            'redesigning-online-community-to-increase-customer-engagement-for-a-leading-it-firm',
            'integrating-salesforce-crm-platform-and-jira-service-desk-with-sinergify',
            'a-guide-to-adobe-commerce',
            'oster-innovation-to-drive-value-from-your-salesforce-community-download',
            'salesforce-services',
            'vanilla-online-community-services',
            'managing-the-risk-of-generative-ai-a-comprehensive-guide',
            'your-go-to-guide-for-migration-to-drupal-9',
            'how-our-khoros-community-redesign-led-to-49-more-engagement',
            'streamlining-case-management-on-salesforce-service-cloud',
            'niche-marketing-an-essential-guide-to-creating-content-that-connects',
            '12-b2b-conversion-rate-optimization-hacks-for-turning-interest-into-pipeline',
            'ua-sunset-to-ga4-sunrise-deciphering-the-importance-of-historical-data',
            'tap-into-the-power-of-search-with-google-ads',
            'enhance-ux-and-boost-engagement-rates-with-hubspots-responsive-emails',
            'creating-dynamic-web-experiences-in-shopify-to-increase-user-engagement',
            'creating-and-connecting-aem-content-fragments-with-spa-to-maintain-a-common-database-for-service',
            'hubspot-onboarding-services-a-tell-all-guide',
            'the-definitive-guide-to-digital-adoption-on-salesforce-communities',
            'madcap-flare-your-all-in-one-solution-for-efficient-content-authoring',
            'healthcare-analytics-made-simple',
            'the-all-encompassing-guide-to-moving-your-ecommerce-business-to-salesforce-commerce-cloud',
            'maximizing-productivity-with-hubspots-ai-content-assistant-tool',
            'unlocking-true-community-potential-with-a-two-way-knowledge-exchange-2',
            'generative-ai-in-marketing-practical-use-cases-that-matter',
            'ecommerce-customer-experience',
            'pioneering-patient-centric-healthcare-with-data',
            'why-is-third-party-risk-management-lifecycle-important',
            'drupal-marketo-connector',
            'enabling-the-idea-component-in-salesforce-lightning-for-a-leading-it-provider',
            'a-comprehensive-guide-to-ai-powered-personalization-with-salesforce-marketing-cloud',
            'how-to-create-a-high-octane-marketing-and-sales-alignment-2',
            'simplifying-community-interactions-and-driving-results-for-an-ecommerce-leader',
            'the-ultimate-salesforce-integration-handbook-navigating-challenges-top-tools-and-how-to-make-the-right-choice',
            'implementing-and-advanced-email-to-case-solution-in-salesforce-for-simplified-case-management',
            'pipeline-management-the-art-of-bridging-lead-to-conversion-gap',
            'increasing-user-engagement-for-an-internet-communication-pioneer',
            'react-native-mobile-app-upgradation-and-maintenance-for-a-leading-car-racing-event',
            'the-transformative-power-of-ai-and-ml-for-business-intelligence',
            'drupal-services',
            'redesigning-and-optimizing-customer-community-for-better-case-management-and-improved-cx',
            'simplifying-omnichannel-analytics-for-business-growth',
            'building-interactive-sharepoint-intranet-portal-for-a-multinational-computer-software-company',
            'revenue-cycle-management-analytics',
            'marketo-migration-simplified-a-detailed-guide',
            'executing-retail-software-testing-services-for-a-leading-loyalty-solutions-provider',
            'headless-commerce',
            'ecommerce-trends-2023',
            'wordpress-marketo-integration-connector',
            'netsuite-agentforce-integration-simplified-unlocking-benefits-integration-steps-and-overcoming-challenges',
            'a-comprehensive-guide-to-incident-and-problem-management',
            'how-netsuite-will-reshape-businesses-in-2023-beyond',
            'boosting-a-fitness-brands-engagement-by-75-with-a-custom-mobile-app-and-admin-panel',
            'salesforce-marketing-cloud-redefining-marketing-in-the-healthcare-industry',
            'digital-experience-orchestration-in-2024-creating-user-centric-interactions-across-platforms',
            'the-ultimate-playbook-for-your-martech-stack-integrating-salesforce-with-marketing-automation-tools',
            'an-ultimate-guide-to-implementing-zero-trust-security-framework',
            'an-ultimate-guide-to-marketing-automation-integrations',
            'building-a-custom-community-leaderboard-for-an-antivirus-software-company',
            'effectively-enabling-the-idea-component-in-salesforce-lightning',
            'transforming-a-cybersecurity-leaders-online-community-for-improved-engagement',
            'marketo-implementation-checklist-a-step-by-step-guide',
            'best-practices-for-netsuite-implementation-and-customization',
            'boosting-engagement-and-innovation-for-a-global-infrastructure-leader',
            'building-cross-platform-react-native-mobile-app-for-a-home-utility-services-company',
            'an-all-encompassing-guide-to-mastering-micro-moment-marketing-with-marketing-automation',
            'building-a-sentiment-analysis-dashboard-for-a-us-based-cloud-computing-platform-provider',
            'choosing-a-deduplication-tool-11-essential-features-to-look-for-before-you-buy',
            'ai-ml-trends-outlook-2026-next-generation-innovations-transforming-industries',
            'enhanced-responsiveness-and-ux-of-hubspot-landing-pages-for-a-digital-marketing-agency',
            'streamlining-financial-reporting-in-netsuite-for-a-g2-devops-leader',
            'step-by-step-guide-to-improve-your-video-marketing-strategy',
            'magento-case-studies-100-a7-enhancing-site-performance-to-85-and-traffic-by-125-for-construction-material-firm-enhancing-site-performance-to-85-and-traffic-by-125-for-construction-material-fir',
            'healthcare-analytics-2',
            'magento-case-studies-100-a9-headless-magento-enhancing-user-experience-and-performance-for-an-international-fashion-brand-website-headless-magento-enhancing-user-experience-and-performance-f',
            'automated-business-operations-quickened-case-resolution-with-cases-and-jira-integration',
            'set-up-an-effective-lead-lifecycle-in-marketo',
            'madcap-flare-services',
            'building-four-salesforce-communities-testing-custom-objects-for-a-fintech-company',
            'marketo-services-and-solutions',
            'augmenting-salesforce-standard-features-with-grazittis-custom-solutions',
            'the-complete-guide-to-mastering-ga4-and-google-ads-integration',
            'manage-secure-and-optimize-enterprise-ai-with-grigo',
            'leverage-the-power-of-intuitive-ui-ux-design-for-business-success',
            'wordpress-marketo-connector',
            'video-services',
            'hassle-free-data-synchronization-from-zendesk-to-salesforce',
            'an-integrated-microsoft-ecosystem-for-tech-driven-digital-transformation-2',
            'magento-vs-shopify-which-one-should-you-go-for',
            'consumer-psychology-in-ecommerce',
            'the-voice-revolution-how-salesforce-service-cloud-voice-is-leading-the-charge',
            'a-tell-all-guide-to-direct-to-consumer-business-model',
            'the-ai-powered-ecommerce-playbook-maximizing-digital-success-with-salesforce-commerce-cloud',
            '7-key-cyber-security-trends-that-will-rule-in-2023',
            'drive-business-growth-with-paid-ads',
            'migration-from-existing-hubspot-platform-to-new-hubspot-platform',
            'navigating-the-world-of-data-engineering-a-guide-to-business-success',
            'ai-first-product-discovery-a-complete-guide-to-llm-optimization-vector-search-and-conversational-commerce',
            'data-driven-decision-making-leveraging-prescriptive-analytics-for-business-success-in-2024',
            'generating-shopify-discount-coupons-via-custom-slack-app-for-a-premium-footwear-brand',
            'implementing-integrating-utm-tracking-to-improve-lead-management-in-marketing-cloud-account-engagement',
            'enhancing-user-experience-a-breakdown-of-10-heuristic-principles',
            'optimizing-salesforce-testing-with-ai-a-comprehensive-guide',
            'enabling-effective-community-moderation-for-a-health-fitness-product-manufacturer',
            'community-manager',
            'why-companies-are-evaluating-zuora-cpq-a-2026-guide-to-modern-subscription-quoting',
            'salesforce-predictions-report-2026',
            'salesforce-data-cloud-mulesoft-the-winning-formula-for-business-success',
            'embracing-ai-analytics-to-maximize-marketing-impact',
            'streamlining-incident-resolution-to-enable-a-10-faster-response-time-for-a-cybersecurity-company',
            'developing-an-inventory-management-system-and-redesigning-the-shopify-website',
            'cheat-sheet-the-ultimate-online-reputation-management-plan',
            'migrating-and-optimizing-a-research-and-development-portal-in-aem-for-a-leading-multinational-technology-company',
            'enhancing-user-experience-in-shopify-by-enabling-multiple-customizations',
            'a-comprehensive-guide-on-progressive-web-apps',
            'seo-fundamentals-unlock-the-secrets',
            'leveraging-salesforce-integration',
            'modern-marketers-guide-to-using-the-right-tech-stack-3',
            'building-a-robust-internal-risk-management-framework-a-step-by-step-guide',
            'harnessing-the-power-of-generative-ai-a-comprehensive-guide-for-businesses',
            'building-an-umbraco-8-website-with-dynamic-components-and-complex-designs-for-a-quantum-computing-company',
            'mastering-service-cloud-voice-a-complete-guide-to-optimizing-your-salesforce-contact-center',
            'marketo-to-marketo-instance-migration-for-80-higher-efficiency-for-a-talent-management-software-company',
            'streamlining-provider-search-and-referral-campaign-management-with-an-advanced-finder-app-2',
            'ai-and-analytics-in-motion-transforming-care-delivery-at-health-centers',
            'building-online-tours-and-pop-ups-in-a-salesforce-community',
            'transform-your-website-with-hubspot-for-enhanced-ux-engagement',
            'powering-business-growth-with-ai-driven-leadership-strategies',
            'all-you-need-to-know-about-conversational-analytics',
            'optimize-your-marketing-strategy-a-deep-dive-into-eloquas-ai-powered-features',
            'customizing-survey-forms-and-website-components-in-aem-6-5-for-a-global-organizational-consulting-firm',
            'a-comprehensive-guide-to-idea-management-to-foster-innovation-for-business-growth',
            'a-complete-guide-to-microsoft-dynamics-365-copilot',
            'website-overhaul-for-a-leading-newspaper-to-enhance-performance',
            '7-ways-to-select-the-right-salesforce-and-jira-connector',
            'seamless-shopify-marketo-integration-for-smarter-ecommerce-marketing',
            'community-management-as-a-service-cmaas',
            'a-comprehensive-guide-to-harness-oracle-eloqua-for-marketing-excellence',
            'alteryx-custom-connectors',
            'migrate-to-ms-dynamics-365-unlock-growth-efficiency-and-roi',
            'salesforce-commerce-cloud-services',
            'maximizing-marketing-impact-through-integrated-data-solutions-for-a-digital-marketing-agency',
            'community-migration-101-a-guide-to-migrating-to-vanilla',
            'streamlining-case-refund-process-for-enhanced-customer-satisfaction',
            'improving-traffic-and-search-results-of-a-khoros-community',
            'effective-marketing-strategies-for-your-b2b-ecommerce-business',
            'm-hive-your-go-to-asset-back-up-solution-for-marketo',
            'react-native-mobile-app-upgrade-and-maintenance-for-car-racing-event',
            'empowering-decision-making-and-enhancing-ui-ux-with-feature-rich-dynamic-charts',
            'healthcare-analytics-a-guide-to-improving-patient-outcomes',
            'how-email-markeitng-automation-manages',
            'building-a-dynamic-umbraco-website-for-a-leading-home-loans-company',
            'streamlining-case-management-advanced-e2c-integration-enhanced-recipient-tracking-in-salesforce',
            'mastering-automation-testing-strategies-tools-and-best-practices',
            'creating-knowledge-base-in-madcap-flare-and-syncing-it-with-a-salesforce-powered-community-for-a-consistent-brand-experience',
            'revamping-community-design-for-an-education-industry-leader',
            'improved-communication-collaboration-and-transparency-between-salesforce-and-jira-users',
            'transforming-workplace-collaboration-with-grazittis-online-community',
            'reimagine-how-you-manage-ideas-in-salesforce-classic-and-lightning-experience-2',
            'the-synergistic-power-of-ai-ml-and-data-visualization-for-business-intelligence',
            'unlocking-the-power-of-jira-your-essential-playbook-for-team-success',
            'revamping-customer-interaction-migrating-from-khoros-to-bettermode',
            'eloqua-landing-pages-the-key-to-unlocking-higher-conversions-and-roi',
            'amplifying-community-user-retention-by-25-with-dynamic-pagination-advocacy-integration',
            'why-are-businesses-choosing-oracle-eloqua-as-their-marketing-automation-tool',
            'shopify-pos-a-tell-all-guide-to-supercharge-your-sales',
            'analyzing-provider-productivity-for-a-community-behavioural-health-clinic',
            'enhancing-ux-in-shopify-by-enabling-multiple-customizations',
            'how-to-take-your-b2b-business-online',
            'sinergify-a-salesforce-jira-connector',
            'developing-video-conferencing-setup-for-a-leading-experience-design-studio-by-optimizing-vue-js-web-applications',
            'standardizing-email-marketing-operations-in-oracle-eloqua-for-a-leading-telecommunications-company-2',
            'salesforce-integration-services-2',
            'elevating-community-with-peer-match-integration-and-empowerment-centre',
            'the-ultimate-guide-to-a-successful-ga4-and-salesforce-integration',
            'dreamforce-2024-recap-insights-from-grazittis-lens',
            'the-ultimate-guide-to-headless-cms',
            'establishing-shopify-marketo-integration-through-cartiveo-to-boost-conversions',
            'streamlining-marketo-wrike-operations-for-better-project-management',
            'top-10-web-development-trends-for-2021',
            'ecommerce-services',
            'enabling-salesforce-cpq-and-billing-flow-testing-for-a-low-code-development-platform',
            'implementing-adobe-tagging-on-website-elements-for-a-u-s-based-tax-firm',
            'data-synergy-in-healthcare-bridging-gaps-through-interoperability',
            'customer-experience-in-2023-and-beyond',
            'the-complete-guide-to-microsoft-fabric-for-unified-analytics',
            'strengthening-the-product-security-of-a-leading-search-solutions-provider-with-vapt',
            'building-a-robust-internal-risk-management-framework-a-step-by-step-guide-2',
            '`customizing-the-partner-community-portal-to-streamline-sales-proc`esses',
            'build-future-ready-web-apps-with-scalable-frameworks',
            'salesforce-cpq-services',
            'fixing-seo-increasing-organic-visibility-for-a-data-security-company',
            'streamlining-manufacturing-operations-for-faster-time-to-market',
            'elevate-your-marketing-game-with-oracle-eloquas-data-tools',
            '2025-and-beyond-trends-shaping-the-future-of-digital-experiences',
            'achieve-operational-excellence-with-modern-erp-solutions',
            'implementing-webcam-proctoring-in-moodle-for-a-leading-e-learning-organization',
            'accelerating-data-migration-and-validation-process-for-a-leading-business-communications-provider',
            'salesforce-service-cloud-services-2',
            'migrate-from-jive-x-to-salesforce-lightning-community-cloud',
            'from-manual-to-intelligent-revolutionizing-testing-with-ai-automation',
            'how-community-led-growth-can-accelerate-business-growth',
            'sitegenesis-to-storefront-reference-architecture-sfra-migration',
            'efficient-it-help-desk-support-management-for-a-leading-voip-provider',
            'enchancements',
            'increasing-user-engagement-by-10x-with-interactive-training-modules',
            'empowering-a-new-era-of-customer-service-with-ai-agents',
            'geo-for-winning-brand-visibility-in-generative-ai-search',
            'a-seamless-zendesk-to-salesforce-migration-for-an-identity-verification-company',
            'facilitating-smooth-community-migration-from-jive-to-salesforce',
            'digital-experience-shift-in-2026-scaling-ux-architecture-and-digital-commerce-for-intelligent-experiences',
            'm-clean',
            'how-communities-are-supporting-successful-brands-and-why-you-might-be-missing-out',
            'the-agentic-enterprise-playbook-how-leaders-govern-scale-and-monetize-digital-labor',
            'your-10-step-guide-to-merge-salesforce-org-seamlessly',
            'the-all-encompassing-guide-to-madcap-flare',
            'a-step-forward-in-customer-service-transformation',
            'redefining-marketing-success-the-ultimate-guide-to-amplifying-marketos-performance',
            'delivering-stellar-ui-ux-with-custom-community-solutions',
            'support-analytics-services',
            'migrating-to-hubspot-your-go-to-checklist-guide-for-a-seamless-transition',
            'marketo-integration',
            'b2b-e-commerce-platform-analysis-adobe-commerce-magento-bigcommerce-and-shopify',
            'gdpr-compliance',
            'salesforce-qa-test-automation-services',
            'a-holistic-guide-to-a-winning-website-strategy',
            'ais-impact-on-online-communities-a-comprehensive-analysis',
            'swift-migration-of-massive-data-from-vbulletin-to-insided-platform',
            'salesforce-service-cloud-and-jira-use-cases',
            'the-marketers-playbook-for-website-redesign',
            'maximizing-marketing-roi-streamlining-operations-for-a-leading-computer-software-firm',
            'building-a-connected-scalable-tech-ecosystem-the-ultimate-guide-to-marketing-automation-integrations',
            'move-from-data-rich-to-insight-driven',
            'achieving-30-growth-in-user-engagement-impact-of-gamification-in-community-revitalization',
            'the-ultimate-guide-to-social-media-marketing-for-businesses',
            'design-system-vs-style-guide-what-leaders-need-to-know-now',
            'achieving-a-full-website-redesign-in-just-one-month-for-a-leading-3d-printing-company',
            'the-strategic-advantage-of-omnichannel-analytics-to-maximize-marketing-impact',
            'streamline-business-operations-with-project-management-services',
            'integrating-aem-dam-assets-with-wordpress-media-library-for-unified-asset-management-and-access-across-platforms',
            'engineering-connected-intelligent-commerce',
            'predictive-analytics',
            'aem-development-services',
            'marketing-automation-simplifying-business-operations-in-the-recruitment-industry',
            'migration-from-an-existing-client-crm-to-hubspot-crm',
            'facilitating-seamless-community-content-migration-from-higher-logic-vanilla-to-bettermode',
            'linkedin-lead-generation-for-b2b-businesses-eguide',
            'the-essential-guide-to-salesforce-revenue-cloud-from-concept-to-execution',
            'your-roadmap-to-roi-driven-analytics-and-ai-success',
            'delineating-the-secret-of-successful-integration-of-a-custom-salesforce-object-and-jira',
            'salesforce-financial-services-cloud-services',
            'multiplying-community-traffic-with-innovative-design-thinking',
            'integrating-salesforce-and-jira-a-complete-guide',
            'all-in-one-guide-for-big-data-analytics',
            'content-marketing-trends-lookbook-for-2024-beyond',
            'grow-scale-and-win-with-lead-scoring-and-grading',
            'guidein-maximizing-digital-adoption-on-salesforce-communities',
            'a-practical-guide-to-revenue-cycle-management-population-health-analytics',
            'ideaspro-for-salesforce-enabling-personalized-scalable-and-gamified-idea-management',
            'unlocking-ecommerce-success-your-complete-integrations-guide',
            'navigating-inter-platform-migration-to-turbocharge-your-marketing-automation',
            'content-marketing-trends-to-look-out-for-in-2023',
            'migrating-from-jive-to-sharepoint-online-for-increased-scalability-and-user-adoption-2',
            'making-your-business-black-friday-ready-an-all-encompassing-guide',
            'how-covid-19-is-accelerating-the-move-to-digital',
            'navigating-the-metaverse-transforming-online-communities-for-the-future',
            'fixing-gtm-in-google-analytics-for-a-public-sector-community',
            'streamlining-interdepartmental-collaboration-enhancing-team-productivity-for-a-government-entity',
            'a-marketers-guide-to-optimizing-marketing-cloud-account-engagement-for-better-results',
            'the-evolution-ahead-2025-insights-for-online-communities',
            'a-guide-to-dynamic-content-in-marketing-cloud-account-engagement',
            'building-customer-oriented-communities-with-effective-personalization',
            'getting-started-with-marketing-attribution',
            'operations-and-supply-chain-analytics',
            'email-to-case-advance',
            'text-to-image-using-stable-diffusion-for-a-marketing-company',
            'spearheading-community-engagement-with-influencer-marketing',
            'optimize-zendesk-for-superior-customer-support-a-tell-all-guide',
            'developing-a-campaign-manager-for-a-global-customer-engagement-agency',
            'inbound-marketing',
            'the-only-predictive-analytics-guide-you-need',
            'debunking-common-misconceptions-about-elearning-and-how-it-builds-interactive-journeys',
            'beyond-the-buzzwords-digital-commerce-trends-and-predictions-for-2025',
            'enhancing-community-safety-and-engagement-with-ai-powered-moderation-2',
            'migrating-a-website-from-drupal-to-wordpress-while-maintaining-100-data-integrity',
            'streamline-partner-management-process-for-financial-services-provider',
            'integrating-zendesk-support-and-jira-with-sinergify',
            'streamline-your-b2b-and-b2c-journey-with-hybrid-ecommerce-platforms',
            '15-critical-ecommerce-mistakes-you-should-avoid',
            'driving-lead-generation-engagement-using-powerful-ui-ux-designs',
            'ai-horizons-2024-emerging-trends-and-best-practices',
            'the-ultimate-guide-for-choosing-the-right-erp-system',
            '10-practical-use-cases-of-salesforce-agentforce-you-can-implement-today',
            'start-smart-scale-fast-the-definitive-guide-to-dynamics-365-implementation',
            'optimizing-paid-ads-to-generate-3-million-in-revenue-for-an-it-company',
            'a-comprehensive-guide-to-leveraging-tableau-pulse-for-real-time-intelligence',
            'ccpa-all-you-need-to-know',
        ];

        $query = new WP_Query( [
            'post_type'              => 'resource',
            'post_status'            => 'publish',
            'posts_per_page'         => count( $slugs ),
            'post_name__in'          => $slugs,
            'fields'                 => 'ids',
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ] );

        $excluded_ids = $query->posts ?: [];
    }

    return array_unique( array_merge( (array) $ids, $excluded_ids ) );
}


//  Exclude specific pages from the AIOSEO sitemap
add_filter( 'aioseo_sitemap_exclude_posts', 'gz_exclude_pages_from_sitemap', 10, 2 );
function gz_exclude_pages_from_sitemap( $ids, $type ) {
    static $excluded_ids = null;

    if ( null === $excluded_ids ) {

        // --- Flat top-level pages by slug ---
        $slugs = [
            'services',
            'solutions',
            'zendesk-professional-services',
            'ettiq-product-demo',
            'sitemap',
            'autopex-product-demo',
            'autotestiq-product-demo',
        ];

        $slug_query = new WP_Query( [
            'post_type'              => 'page',
            'post_status'            => 'publish',
            'posts_per_page'         => count( $slugs ),
            'post_name__in'          => $slugs,
            'fields'                 => 'ids',
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ] );

        $excluded_ids = $slug_query->posts ?: [];

        // --- Nested/hierarchical pages by full URL path ---
        // Use url_to_postid() — resolves full permalink correctly for nested pages.
        $nested_paths = [
            '/services/artificial-intelligence-solutions/platforms/',
            '/services/artificial-intelligence-solutions/solutions/',
            '/services/digital-marketing/platforms/',
            '/services/digital-marketing/services/',
            '/services/enterprise-services/',
            '/services/enterprise-services/platforms/',
            '/services/information-security/solutions/',
            '/services/itsm/solutions/',
            '/services/microsoft-services/platforms/',
            '/services/microsoft-services/solutions/',
            '/services/design/solutions/elearning/',
            '/services/online-communities/platforms/vanilla-community-service-provider/',
            '/services/crm/solutions/salesforce-cpq/',
            '/products/m-clean-marketo-data-deduplication/product-demo/',
            '/solutions/',
            '/services/',
        ];

        $nested_ids = [];
        foreach ( $nested_paths as $path ) {
            $full_url = home_url( $path );
            $post_id  = url_to_postid( $full_url );

            if ( $post_id ) {
                $nested_ids[] = $post_id;
            }
        }

        $excluded_ids = array_unique( array_merge( $excluded_ids, $nested_ids ) );
    }

    return array_unique( array_merge( (array) $ids, $excluded_ids ) );
}



/**
 * DYNAMIC INTERNAL NOFOLLOW REMOVER & CLEANER
 * Works for wp_nav_menu, post content, custom PHP templates, and hardcoded menus.
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. CORE HTML CLEANER FUNCTION
 * Scans HTML content for <a> tags, checks if they point to internal URLs,
 * and strips 'nofollow' as well as empty rel="" attributes.
 */
function clean_internal_nofollow_html( $content ) {
    if ( empty( $content ) ) {
        return $content;
    }

    $site_domain = str_replace( 'www.', '', wp_parse_url( home_url(), PHP_URL_HOST ) );
    if ( empty( $site_domain ) ) {
        return $content;
    }

    // Match all <a> tags
    $pattern = '/<a\s+[^>]*?>/i';

    return preg_replace_callback( $pattern, function( $matches ) use ( $site_domain ) {
        $tag = $matches[0];

        // Extract href attribute value
        if ( ! preg_match( '/href=["\']([^"\']*)["\']/i', $tag, $href_match ) ) {
            return $tag;
        }

        $href = trim( $href_match[1] );

        // Determine if link is internal (relative path, anchor link, or site domain)
        $is_internal = ( strpos( $href, '/' ) === 0 ) 
                     || ( strpos( $href, '#' ) === 0 ) 
                     || ( strpos( $href, $site_domain ) !== false );

        if ( $is_internal ) {
            if ( preg_match( '/rel=["\']([^"\']*)["\']/i', $tag, $rel_match ) ) {
                $rels = preg_split( '/\s+/', trim( $rel_match[1] ) );
                // Remove 'nofollow' and empty string elements
                $filtered_rels = array_diff( $rels, array( 'nofollow', '' ) );

                if ( ! empty( $filtered_rels ) ) {
                    // Rebuild rel attribute if other values remain (e.g., rel="noopener")
                    $tag = preg_replace( '/rel=["\'][^"\']*["\']/i', 'rel="' . implode( ' ', $filtered_rels ) . '"', $tag );
                } else {
                    // Entirely remove the rel attribute if empty or contained only 'nofollow'
                    $tag = preg_replace( '/\s*rel=["\'][^"\']*["\']/i', '', $tag );
                }
            }
        }

        // Clean up redundant trailing or inner spaces
        $tag = preg_replace( '/\s+>/', '>', $tag );
        return preg_replace( '/\s+/', ' ', $tag );
    }, $content );
}

/**
 * 2. NAV MENU FILTER (For menus rendered via wp_nav_menu)
 */
function fix_nav_menu_internal_nofollow( $atts, $item, $args ) {
    if ( ! empty( $atts['rel'] ) ) {
        $site_domain = str_replace( 'www.', '', wp_parse_url( home_url(), PHP_URL_HOST ) );
        $url = ! empty( $atts['href'] ) ? $atts['href'] : '';

        $is_internal = ( strpos( $url, '/' ) === 0 ) 
                     || ( strpos( $url, '#' ) === 0 ) 
                     || ( ! empty( $site_domain ) && strpos( $url, $site_domain ) !== false );

        if ( $is_internal ) {
            $rels = array_diff( preg_split( '/\s+/', trim( $atts['rel'] ) ), array( 'nofollow', '' ) );
            if ( ! empty( $rels ) ) {
                $atts['rel'] = implode( ' ', $rels );
            } else {
                unset( $atts['rel'] );
            }
        }
    }
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'fix_nav_menu_internal_nofollow', 99, 3 );

/**
 * 3. POST CONTENT FILTER
 */
add_filter( 'the_content', 'clean_internal_nofollow_html', 99 );

/**
 * 4. GLOBAL OUTPUT BUFFER
 * Catches custom header/footer HTML templates, hardcoded submenus, and page builders.
 */
function start_global_nofollow_cleaner_buffer() {
    if ( is_admin() ) {
        return;
    }
    ob_start( 'clean_internal_nofollow_html' );
}
add_action( 'template_redirect', 'start_global_nofollow_cleaner_buffer', 0 );

/**
 * 5. INSTANT CLIENT-SIDE SCRIPT (DOM Cleanup Fallback)
 */
function enqueue_internal_nofollow_remover_script() {
    ?>
    <script>
    (function () {
        function cleanInternalLinks() {
            var currentDomain = window.location.hostname.replace(/^www\./, '');
            var links = document.querySelectorAll('a[rel]');

            links.forEach(function (link) {
                var href = link.getAttribute('href');
                var relAttr = link.getAttribute('rel');

                // Strip empty rel="" attributes immediately
                if (relAttr === '' || relAttr === null) {
                    link.removeAttribute('rel');
                    return;
                }

                if (!href) return;

                var isInternal = href.startsWith('/') || href.startsWith('#') || href.includes(currentDomain);

                if (isInternal && relAttr.toLowerCase().includes('nofollow')) {
                    var updatedRel = relAttr
                        .split(/\s+/)
                        .filter(function (val) {
                            return val.toLowerCase() !== 'nofollow' && val.trim() !== '';
                        })
                        .join(' ')
                        .trim();

                    if (updatedRel.length > 0) {
                        link.setAttribute('rel', updatedRel);
                    } else {
                        link.removeAttribute('rel');
                    }
                }
            });
        }

        cleanInternalLinks();
        document.addEventListener('DOMContentLoaded', cleanInternalLinks);
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'enqueue_internal_nofollow_remover_script', 999 );






