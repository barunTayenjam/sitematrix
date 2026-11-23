<?php 
/*
Template Name: GZ Blog Listing
*/
get_header('gzInner'); 

global $post;
$attachment_id = get_post_thumbnail_id( $post->ID );

$alt_name = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
$title_name = get_the_title($post->ID);
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
<div class="wrap_1280">
    <div class="main-content">       
        <div class="container-fluid nt-blog-template blog-archive">
            <div class="left-page-content">
                <div class="container"> <div class="blog_search_loading" style="display:none;">Loading&#8230;</div>
				<div class="parent-select-inner">
				<h1 class="blog-listing-title">Insights by Grazitti</h1>
				<div class="sort-blog-select-sim form-group">
						<select class="sort-container form-control" onchange="fetchorder('order')" name="postshorting" id="check" aria-label="Default select">
							<option value="" selected>Sort By</option>
							<option value="asc">Old to New</option>
							<option value="desc">New to Old</option>
						</select>
					</div>
				</div>	
					<div class="conditional-container"> <?php 
					$offset = 0; $posts_per_page = 16;
                    $loop = new WP_Query(
						array(
							'post_status' => 'publish',
							'offset' => $offset,
							'posts_per_page' => $posts_per_page,
							'post__not_in' => gz_blog_excluded_post_ids(),
						)
					);			
					
                    $row = 1;
                    if ( $loop->have_posts() ) :
                    while ( $loop->have_posts() ) : 
                    $loop->the_post(); 
					
					$post_categories = get_the_category( $post->ID );
					$custom_link = '';	
					$query_str = do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] ');			
					
					if (!empty(get_field('custom_link')) && ($post_categories[0]->slug=='digital-marketing' || $post_categories[0]->slug=='salesforce-crm')) {						
						 $custom_link = get_field('custom_link');															 
					}
					else{
						$custom_link = get_the_permalink();
					}										
					
                    if($row >=1 && $row <= 3){ 
						if($row ==1){?>
							<div class="row ms-container"><?php
						}?>
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="post3 h-100">
                                <div class="blog-page-content h-100" id=<?php echo $post->ID;?>><?php 
									if (has_post_thumbnail()) { ?>
									<figure class="article-preview-image"><a href="<?php echo $custom_link; ?>"><?php the_post_thumbnail(array(415, 270),$post_thumbnail_attr); ?></a></figure><?php 
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
											<h6 class="time-read"><?php if(!empty(get_field('custom_read_time')) &&  $post_categories[0]->slug=='digital-marketing' || $post_categories[0]->slug=='salesforce-crm') {?><span class="span-reading-time rt-reading-time"><span class="rt-label rt-postfix"><?php echo get_field('custom_read_time');?></span></span><?php }else{ echo do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] '); } ?></h6>
                                        </div>
                                        <!-- END CATEGORY POST -->
                                    </div>
                                </div>
                                <!-- END EACH BLOG-->
                            </div>
                        </div>  <?php
							if($row == 3){?>
                    </div>  <?php }
					}
					
					if($row >=4 && $row <= 6){ 
						if($row ==4){?>
						<div class="row ms-container d-none"><?php
						}?>
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="post3 h-100">
                                <div class="blog-page-content h-100" id=<?php echo $post->ID;?>><?php 
									if (has_post_thumbnail()) { ?>
									<figure class="article-preview-image"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(array(415, 270),$post_thumbnail_attr); ?></a></figure><?php 
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
											<h6 class="time-read"><?php echo do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] '); ?></h6>
                                        </div>
                                        <!-- END CATEGORY POST -->
                                    </div>
                                </div>
                                <!-- END EACH BLOG-->
                            </div>
                        </div>  <?php
							if($row == 6){?>
                    </div>  <?php }
					}
                    if($row == 7){ ?>
                    <div class="row" id="ms-container">
                        <div class="col-xl-8 col-md-7">
                            <div class="blog-page-content post1 h-100" id=<?php echo $post->ID;?>>   <?php 
								if (has_post_thumbnail()) { ?>
                                <figure class="article-preview-image"><a href="<?php echo $custom_link; ?>"> <?php the_post_thumbnail('large',$post_thumbnail_attr); ?></a></figure> <?php 
                                } ?>
                                <div class="nt-blog-card-body">
                                    <h2><a href="<?php echo $custom_link ?>" class="category-title-link"><?php the_title(); ?></a></h2>
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
                                        <h6 class="time-read"><?php echo do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] '); ?></h6>
                                    </div>
                                    <!-- END CATEGORY POST -->
                                </div>
                            </div>
                            <!-- END EACH BLOG-->
                        </div>
                        <div class="col-xl-4 col-md-5">
                            <div class="side-bar h-100">
                                <div class="blog-listing">
                                    <div class="popular-post"><p>Popular Blog Posts</p></div>
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
															<h6 class="time-read"><?php echo do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] '); ?></h6>					
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
												$title_name = get_the_title($post->ID);
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
										<figure class="article-preview-image"><a href="<?php echo $custom_link; ?>"><?php the_post_thumbnail(array(415, 270), $post_thumbnail_attr); ?></a></figure><?php
										} ?>
                                                <div class="nt-blog-card-body">
                                                    <h2><a href="<?php echo $custom_link; ?>" class="category-title-link">  <?php the_title(); ?> </a></h2>
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
														<h6 class="time-read"><?php echo do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] '); ?></h6>
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
													<li data-target="#carouselExampleIndicators" data-slide-to="<?php echo $i;?>" class="<?php echo $class;?>"></li><?php 
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
										<!-- start webinar code -->
								<div class="webinar-code">
									<div class="blog-ads-bottom-webinar">
								<div class="nt-blog-slider-sm-webinar text-center">
									<div id="myCarouselWebinar" class="carousel slide" data-ride="carousel"><?php
										$today = date('Ymd');
										$args = array(
											'post_type'      => 'event',
											'posts_per_page' => 1,
											'orderby'        => 'meta_value',
											'meta_key'       => 'start_date',
											'order'          => 'ASC',
											'meta_query'     => array(
												array(
													'key'     => 'start_date',
													'value'   => $today,
													'compare' => '>=',
													'type'    => 'DATE',
												),
											),
										);
										$getWebinarSlide = new WP_Query( $args );?>

										<!-- Wrapper for slides -->
										<div class="carousel-inner sidebar-bottom-carousal h-100"><?php
											if ( $getWebinarSlide->have_posts() ) {
												while ( $getWebinarSlide->have_posts() ) : $getWebinarSlide->the_post();
												$webinar_link = get_field('read_more_link_for_blog_page');?>
												<div class="carousel-item h-100 active">
													<div class="nt-blog-bottom-carousel h-100 d-flex align-items-center justify-content-center">
														<div class="nt-blog-carousel-sm-inner">
															<h3><?php echo get_the_title(); ?></h3>
															<?php
																if ( $webinar_link ) {
																	$webinar_link_url    = $webinar_link['url'];
																	$webinar_link_target = $webinar_link['target'] ? $webinar_link['target'] : '_self';?>
																<a href="<?php echo $webinar_link_url; ?>"
																	target="<?php echo esc_attr($webinar_link_target); ?>"
																	class="btn btn-bottomCarousel btn-sideCarousel text-uppercase">Join the Webinar</a><?php
																} else {
																	echo '<a href="/company/events/" class="btn btn-bottomCarousel btn-sideCarousel text-uppercase">Join the Webinar</a>';
																}?>
														</div>
													</div>
												</div><?php
												endwhile;
											} else {
												echo "<h5 class='no_found_data'>No Upcoming Webinar</h5>";
											}
											wp_reset_postdata();?>
										</div>
									</div>
								</div>
							</div>	
							</div>
						<!-- end of webinar code -->
							
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
										<figure class="article-preview-image"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(array(415, 270),$post_thumbnail_attr); ?></a></figure><?php 
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
											<h6 class="time-read"><?php echo do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] '); ?></h6>
                                        </div>
                                        <!-- END CATEGORY POST -->
                                    </div>
                                </div>
                                <!-- END EACH BLOG-->
                            </div>
                        </div><?php
							if($row == 14){?>
						</div>  <?php }
						} 
						if($row >=15 && $row <= 16){
							if($row == 15){ ?>
						<div class="row ads-slider">
                        <div class="col-md-8 mb-4 mb-md-0">
                            <div class="post1">
                                <div class="blog-page-content h-100" id=<?php echo $post->ID;?>>
                                    <?php if (has_post_thumbnail()) : ?>
                                    <figure class="article-preview-image"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large',$post_thumbnail_attr); ?></a></figure>
                                    <?php else : ?>
                                    <?php endif; ?>
                                    <div class="nt-blog-card-body">
                                        <h2><a href="<?php the_permalink(); ?>" class="category-title-link">  <?php the_title(); ?> </a></h2>
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
                                            <h6 class="time-read"><?php echo do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] '); ?></h6>
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
                                    <figure class="article-preview-image"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(array(415, 270),$post_thumbnail_attr); ?></a></figure>
                                    <?php else : ?>
                                    <?php endif; ?>
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
                                            <h6 class="time-read"><?php echo do_shortcode('[rt_reading_time label="" postfix="min Read" postfix_singular="min Read"] '); ?></h6>
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
										'tax_query' => array(
										array(
											'taxonomy' => 'events',
											'field' => 'slug',
											'terms' => 'event-webinar', 'operator' => 'NOT IN',
										   )
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
												<li data-target="#myCarousel2" data-slide-to="<?php echo $i;?>" class="<?php echo $class;?>"></li><?php
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
																	<a href="<?php echo $event_link_url; ?>" target="<?php echo esc_attr($event_link_target); ?>" class="btn btn-bottomCarousel btn-sideCarousel text-uppercase">View Our Event <i class="fa fa-angle-double-right"></i></a><?php 
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
                        <article class="no-posts"><h1>No Blogs Found.</h1></article>
                        <?php endif; ?>                        
                        <div class="row blog_post_row"></div>
                        <div class="row statewise_scroll"></div>
                        <div class="load_more text-center" id="loaderOnScroll"><img src='/wp-content/uploads/2019/10/giphy.gif' alt='Loading'></div>
					</div>
                </div>
				</div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <!-- END MAIN-CONTENT -->
</div>
<div id="overlay" class="overlay-spiner" style="display: none;">
	<div class="cv-spinner">
		<span class="spinner"></span>
	</div>
</div>
<!-- END wrap_1280 -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/new-template/js/jquery.inview.js"></script>
<!-- <script>
jQuery( document ).ready(function() {
	
    jQuery("#check").val("");
});
</script> -->
<script>
var offset = 10;
var isScroll = true;
/******* START BLOG SEARCH *******/
var searchOffset = 0;
jQuery('#blogSearch button').click(function() {	var getInput = jQuery('#blogSearch input').val();
	if(getInput!=""){
		searchBlog(getInput);
		if(jQuery('.hamburgerBlog').hasClass('slideBlogBurger')){ jQuery('.hamburgerBlog').removeClass('slideBlogBurger'); }
	}
	else{
		window.location.href= "/blog";
	}
});
/*START Blog Search on Enter*/
jQuery("#blogSearch").submit(function(e){	e.preventDefault();
	var getInput = jQuery('#blogSearch input').val();
	if(getInput!=""){
		searchBlog(getInput);
		if(jQuery('.hamburgerBlog').hasClass('slideBlogBurger')){ jQuery('.hamburgerBlog').removeClass('slideBlogBurger'); }
	}else{
		window.location.href= "/blog";
	}

});
/*END Blog Search on Enter*/
	
function searchBlog(input){
	 data = {'action': 'get_blog_search_listing', 'offset': searchOffset, 'post_per_page': 9, 'input' :input };		
	jQuery.ajax({
		url: '<?php echo admin_url('admin-ajax.php'); ?>',	type: 'POST',
		beforeSend: function(){	jQuery('.blog_search_loading').show();	},
		data: data, 
		success: function(response) {
			jQuery('.conditional-container').html(response); jQuery('.blog_search_loading').fadeOut("slow");	jQuery('.blog_search_loading').hide();
		}
	});
}
/****** END BLOG SEARCH *******/

function load_more_postsdata() {	
	var idArr = []
	$('.blog-page-content').map(function() {
  		id = $(this).attr('id');
		if(id != undefined)  
		idArr.push(id);	  
	});
	
	
	var order = jQuery('#check').val();
		if (order != "" && order != "undefined") {
			order = jQuery('#check').val();

		} else {
			order = "no-order";
		}
	data = {
		'action': 'get_blog_load_more_post', 'offset': offset, // that's how we get params from wp_localize_script() function
		'post_per_page': 9,
		'data' : idArr,
		'orderVal': order
	};			
	jQuery.ajax({
		url: '<?php echo admin_url('admin-ajax.php'); ?>',	type: 'POST',	data: data,		
		success: function(response) {
			if (response!='') {
				setTimeout( function() { jQuery('.blog_post_row').append(response);	offset = offset + 9;}, 1000)				
			}
			else{ jQuery("#loaderOnScroll").html(''); }
		} 
	});
}
jQuery(document).on('inview', '#loaderOnScroll', function(event, isInView) {	
	if (isInView) {	load_more_postsdata();
		jQuery('html, body').animate({'scrollTop' : jQuery(".statewise_scroll").position().top});
	}
});

/* Sidebar menu displays or hide for mobile devices  */
jQuery('.hamburgerBlog').click(function(){jQuery('.hamburgerBlog').toggleClass('slideBlogBurger');});
function fetchorder(cat, tab_set = null) {
		offset = 10;
		var order = "Sort By";
		if (cat == 'order') {
			var order = jQuery('#check').val();
		}
		$('#overlay').css('display', 'block');
		jQuery.ajax({
			url: '<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>',
			type: 'post',
			data: {
				action: 'data_fetch',
				tab_set: tab_set,
				order: order,
			},

			success: function(data) {
			
				jQuery('.conditional-container').html(data);
               $('#overlay').css('display', 'none');

			}
		})
		.done(function() {
			/* setTimeout(function() {
				$("#overlay").fadeOut(300);
				$('html, body').animate({
					scrollTop: $(".datafetch").offset().top - 220
				}, 1000);
			}, 500); */
		});

	}

</script>
<?php get_footer('gzInner'); ?>
