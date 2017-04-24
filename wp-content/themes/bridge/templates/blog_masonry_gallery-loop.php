<?php 
global $qode_options_proya;
$blog_enable_social_share = "";
if(isset($qode_options_proya['enable_social_share'])){
	$blog_enable_social_share = $qode_options_proya['enable_social_share'];
}
?>
<?php
$_post_format = get_post_format();
$thumb_size = 'portfolio_masonry_regular';
$thumb_size_temp = get_post_meta(get_the_ID(),"qode_post_style_masonry_gallery",true);
$post_size_class = 'default';
switch ($thumb_size_temp) {
	case 'large-height':
		$thumb_size = 'portfolio_masonry_tall';
        $post_size_class = $thumb_size_temp;
		break;
	case 'large-width':
		$thumb_size = 'portfolio_masonry_wide';
        $post_size_class = $thumb_size_temp;
		break;
	case 'large-width-height':
		$thumb_size = 'portfolio_masonry_large';
        $post_size_class = $thumb_size_temp;
		break;
	default:
		$thumb_size = 'portfolio_masonry_regular';
		break;
}
?>
<?php
	switch ($_post_format) {
		case "link":
?>
			<article id="post-<?php the_ID(); ?>" <?php post_class($post_size_class); ?>>
                <a class="ql_full_link" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"></a>
                <div class="post_content_holder">
					<div class="post_text">
						<div class="post_text_inner">
							<i class="link_mark fa fa-link pull-left"></i>
							<div class="post_title">
								<p><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></p>
							</div>
							<div class="post_info">
								<?php if($blog_enable_social_share == "yes"){
									echo do_shortcode('[social_share_list]');
								} ?>
							</div>	
						</div>
					</div>
				</div>
			</article>
<?php
		break;
		case "gallery":
?>
			<article id="post-<?php the_ID(); ?>" <?php post_class($post_size_class); ?>>
                <div class="post_content_holder">
                    <div class="post_image">
                        <div class="flexslider">
                            <ul class="slides">
                                <?php
                                    $post_content = get_the_content();
                                    preg_match('/\[gallery.*ids=.(.*).\]/', $post_content, $ids);
                                    $array_id = explode(",", $ids[1]);

                                    foreach($array_id as $img_id){ ?>
                                        <li><a target="_self" href="<?php the_permalink(); ?>"><?php echo wp_get_attachment_image( $img_id, $thumb_size ); ?></a></li>
                                    <?php } ?>
                            </ul>
                        </div>
                        <div class="time">
                            <span class="time_day"><?php the_time('d'); ?></span>
                            <span class="time_month"><?php the_time('M'); ?></span>
                        </div>
                    </div>
                    <div class="post_text">
                        <div class="post_text_inner">
                            <h5><a href="<?php the_permalink(); ?>" target="_self" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h5>
                            <div class="post_info">
                                <?php if($blog_enable_social_share == "yes"){
                                    echo do_shortcode('[social_share_list]');
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
			</article>
<?php
		break;
		case "quote":
?>
		<article id="post-<?php the_ID(); ?>" <?php post_class($post_size_class); ?>>
            <a class="ql_full_link" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"></a>
            <div class="post_content_holder">
				<div class="post_text">
					<div class="post_text_inner">
						<i class="qoute_mark fa fa-quote-right pull-left"></i>
						<div class="post_title">
							<p><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo get_post_meta(get_the_ID(), "quote_format", true); ?></a></p>
							<span class="quote_author">&mdash; <?php the_title(); ?></span>
						</div>
						<div class="post_info">
							<?php if($blog_enable_social_share == "yes"){
								echo do_shortcode('[social_share_list]');
							} ?>
						</div>	
					</div>
				</div>
			</div>
		</article>
<?php
		break;
		default:
?>
		<article id="post-<?php the_ID(); ?>" <?php post_class($post_size_class); ?>>
            <div class="post_content_holder">
                <?php if ( has_post_thumbnail() ) { ?>
                    <div class="post_image">
                        <a href="<?php the_permalink(); ?>" target="_self" title="<?php the_title_attribute(); ?>">
                            <?php the_post_thumbnail($thumb_size); ?>
                        </a>
                        <div class="time">
                            <span class="time_day"><?php the_time('d'); ?></span>
                            <span class="time_month"><?php the_time('M'); ?></span>
                        </div>
                    </div>
                <?php } ?>
                <div class="post_text">
                    <div class="post_text_inner">
                        <h5><a href="<?php the_permalink(); ?>" target="_self" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h5>
                        <div class="post_info">
                            <?php if($blog_enable_social_share == "yes"){
                                echo do_shortcode('[social_share_list]');
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
		</article>
<?php
}
?>		

