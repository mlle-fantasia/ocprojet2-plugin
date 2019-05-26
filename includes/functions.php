<?php
/**
 * Various functions used by the plugin.
 *
 * @package    Recent_Posts_Widget_Extended
 * @since      0.9.4
 * @author     Satrya
 * @copyright  Copyright (c) 2014, Satrya
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Sets up the default arguments.
 *
 * @since  0.9.4
 */
function rpwe_get_default_args() {

	$defaults = array(
		'title'             => esc_attr__( 'Recent Posts', 'rpwe' ),
		'title_url'         => '',

		'limit'            => 5,
		'offset'           => 0,
		'order'            => 'DESC',
		'orderby'          => 'date',
		'cat'              => array(),
		'tag'              => array(),
		'taxonomy'         => '',
		'post_type'        => array( 'post' ),
		'post_status'      => 'publish',
		'ignore_sticky'    => 1,
		'exclude_current'  => 1,

		'excerpt'          => false,
		'length'           => 10,
		'thumb'            => true,
		'thumb_height'     => 45,
		'thumb_width'      => 45,
		'thumb_default'    => 'http://placehold.it/45x45/f0f0f0/ccc',
		'thumb_align'      => 'rpwe-alignleft',
		'date'             => true,
		'date_relative'    => false,
		'date_modified'    => false,
		'readmore'         => false,
		'readmore_text'    => __( 'Read More &raquo;', 'recent-posts-widget-extended' ),
		'comment_count'    => false,
        'color'            =>'',
		'styles_default'   => true,
		'cssID'            => '',
		'css_class'        => '',
		'before'           => '',
		'after'            => ''
	);

	// Allow plugins/themes developer to filter the default arguments.
	return apply_filters( 'rpwe_default_args', $defaults );

}

/**
 * Outputs the recent posts.
 *
 * @since  0.9.4
 */
function rpwe_recent_posts( $args = array() ) {
	echo rpwe_get_recent_posts( $args );
}

/**
 * Generates the posts markup.
 *
 * @since  0.9.4
 * @param  array  $args
 * @return string|array The HTML for the random posts.
 */
function rpwe_get_recent_posts( $args = array() ) {

	// Set up a default, empty variable.
	$html = '';

	// Merge the input arguments and the defaults.
	$args = wp_parse_args( $args, rpwe_get_default_args() );

	// Extract the array to allow easy use of variables.
	extract( $args );

	// Allow devs to hook in stuff before the loop.
	do_action( 'rpwe_before_loop' );

    // Toujours utiliser le default style du plugin que j'ai modifier en bas du présent fichier.
    $args['styles_default'] = true ;
    $args['css'] = "";
	if ( $args['styles_default'] === true ) {
		rpwe_custom_styles();
	}
	// If the default style is disabled then use the custom css if it's not empty.
//	if ( $args['styles_default'] === false && ! empty( $args['css'] ) ) {
//		echo '<style>' . $args['css'] . '</style>';
//	}
    //pour les couleurs d'arrière plan

    $tabColor = explode(" ", $args['color']);

	// Get the posts query.
	$posts = rpwe_get_posts( $args );
	$compteurFlex = 1;
    $compteurColor = 0;
	if ( $posts->have_posts() ) :

		// Recent posts wrapper
		$html = '<div ' . ( ! empty( $args['cssID'] ) ? 'id="' . sanitize_html_class( $args['cssID'] ) . '"' : '' ) . ' class="rpwe-block ' . ( ! empty( $args['css_class'] ) ? '' . sanitize_html_class( $args['css_class'] ) . '' : '' ) . '">';

			$html .= '<ul class="rpwe-ul">';

				while ( $posts->have_posts() ) : $posts->the_post();

					// Thumbnails

					$thumb_id = get_post_thumbnail_id(); // Get the featured image id.
					//$img_url  = wp_get_attachment_url( $thumb_id ); // Get img URL.


					// Display the image url and crop using the resizer.
                    $image    = wp_get_attachment_image_src( $thumb_id,'medium_large', true ); //return un tableau
                    //$image2   = rpwe_resize( $img_url, $args['thumb_width'], $args['thumb_height'], true );

					// Start recent posts markup.
					$html .= '<li class="rpwe-li rpwe-clearfix d-flex flexRow'.$compteurFlex.'">';

						if ( $args['thumb'] ) :

							// Check if post has post thumbnail.
							if ( has_post_thumbnail() ) :
								$html .= '<div class="img flex-even"><a class="rpwe-img " href="' . esc_url( get_permalink() ) . '"  rel="bookmark">';
									if ( $image[0] ) :

										$html .= '<img class=" rpwe-thumb" src="' . esc_url( $image[0] ) . '" alt="' . esc_attr( get_the_title() ) . '">';
									else :
										$html .= get_the_post_thumbnail( get_the_ID(),
											array( $args['thumb_width'], $args['thumb_height'] ),
											array(
												'class' => ' rpwe-thumb the-post-thumbnail',
												'alt'   => esc_attr( get_the_title() )
											)
										);
									endif;
								$html .= '</a></div>';

							// If no post thumbnail found, check if Get The Image plugin exist and display the image.
							elseif ( function_exists( 'get_the_image' ) ) :
								$html .= get_the_image( array(
									'height'        => (int) $args['thumb_height'],
									'width'         => (int) $args['thumb_width'],
									'image_class'   => ' rpwe-thumb get-the-image',
									'image_scan'    => true,
									'echo'          => false,
									'default_image' => esc_url( $args['thumb_default'] )
								) );

							// Display default image.
							elseif ( ! empty( $args['thumb_default'] ) ) :
								$html .= sprintf( '<a class="rpwe-img" href="%1$s" rel="bookmark"><img class="%2$s rpwe-thumb rpwe-default-thumb" src="%3$s" alt="%4$s" width="%5$s" height="%6$s"></a>',
									esc_url( get_permalink() ),
								
									esc_url( $args['thumb_default'] ),
									esc_attr( get_the_title() ),
									(int) $args['thumb_width'],
									(int) $args['thumb_height']
								);

							endif;

						endif;

                        $tabColor[$compteurColor] ? $backgroundColor = '#35435d' : $backgroundColor = '#35435d';
						$html .= '<div class="infoPosts flex-even" style="background-color:'.$backgroundColor.';">
                                    <h3 class="rpwe-title">
                                    <a href="' . esc_url( get_permalink() ) . '" title="' . sprintf( esc_attr__( 'Permalink to %s', 'recent-posts-widget-extended' ), the_title_attribute( 'echo=0' ) ) . '" rel="bookmark">' . esc_attr( get_the_title() ) . '</a>
                                    </h3>
                                    <div class="hr"></div>';

						if ( $args['date'] ) :
							$date = get_the_date();
							if ( $args['date_relative'] ) :
								$date = sprintf( __( '%s ago', 'recent-posts-widget-extended' ), human_time_diff( get_the_date( 'U' ), current_time( 'timestamp' ) ) );
							endif;
							$html .= '<time class="rpwe-time published" datetime="' . esc_html( get_the_date( 'c' ) ) . '">' . esc_html( $date ) . '</time>';
						elseif ( $args['date_modified'] ) : // if both date functions are provided, we use date to be backwards compatible
							$date = get_the_modified_date();
							if ( $args['date_relative'] ) :
								$date = sprintf( __( '%s ago', 'recent-posts-widget-extended' ), human_time_diff( get_the_modified_date( 'U' ), current_time( 'timestamp' ) ) );
							endif;
							$html .= '<time class="rpwe-time modfied" datetime="' . esc_html( get_the_modified_date( 'c' ) ) . '">' . esc_html( $date ) . '</time>';
						endif;

						if ( $args['comment_count'] ) :
							if ( get_comments_number() == 0 ) {
									$comments = __( 'No Comments', 'recent-posts-widget-extended' );
								} elseif ( get_comments_number() > 1 ) {
									$comments = sprintf( __( '%s Comments', 'recent-posts-widget-extended' ), get_comments_number() );
								} else {
									$comments = __( '1 Comment', 'recent-posts-widget-extended' );
								}
							$html .= '<a class="rpwe-comment comment-count" href="' . get_comments_link() . '">' . $comments . '</a>';
						endif;

						if ( $args['excerpt'] ) :
							$html .= '<div class="rpwe-summary">';
								$html .= wp_trim_words( apply_filters( 'rpwe_excerpt', get_the_excerpt() ), $args['length'], ' &hellip;' );
								if ( $args['readmore'] ) :
									$html .= '<a href="' . esc_url( get_permalink() ) . '" class="more-link">' . $args['readmore_text'] . '</a>';
								endif;
							$html .= '</div></div>';
						endif;

					$html .= '</li>';
                    $compteurFlex === 1 ? $compteurFlex ++ : $compteurFlex =1;
                    $compteurColor ++;
				endwhile;

			$html .= '</ul>';

		$html .= '</div><!-- Generated by http://wordpress.org/plugins/recent-posts-widget-extended/ -->';

	endif;

	// Restore original Post Data.
	wp_reset_postdata();

	// Allow devs to hook in stuff after the loop.
	do_action( 'rpwe_after_loop' );

	// Return the  posts markup.
	return wp_kses_post( $args['before'] ) . apply_filters( 'rpwe_markup', $html ) . wp_kses_post( $args['after'] );

}

/**
 * The posts query.
 *
 * @since  0.0.1
 * @param  array  $args
 * @return array
 */
function rpwe_get_posts( $args = array() ) {

	// Query arguments.
	$query = array(
		'offset'              => $args['offset'],
		'posts_per_page'      => $args['limit'],
		'orderby'             => $args['orderby'],
		'order'               => $args['order'],
		'post_type'           => $args['post_type'],
		'post_status'         => $args['post_status'],
		'ignore_sticky_posts' => $args['ignore_sticky'],
	);

	// Exclude current post
	if ( $args['exclude_current'] ) {
		$query['post__not_in'] = array( get_the_ID() );
	}

	// Limit posts based on category.
	if ( ! empty( $args['cat'] ) ) {
		$query['category__in'] = $args['cat'];
	}

	// Limit posts based on post tag.
	if ( ! empty( $args['tag'] ) ) {
		$query['tag__in'] = $args['tag'];
	}

	/**
	 * Taxonomy query.
	 * Prop Miniloop plugin by Kailey Lampert.
	 */
	if ( ! empty( $args['taxonomy'] ) ) {

		parse_str( $args['taxonomy'], $taxes );

		$operator  = 'IN';
		$tax_query = array();
		foreach( array_keys( $taxes ) as $k => $slug ) {
			$ids = explode( ',', $taxes[$slug] );
			if ( count( $ids ) == 1 && $ids['0'] < 0 ) {
				// If there is only one id given, and it's negative
				// Let's treat it as 'posts not in'
				$ids['0'] = $ids['0'] * -1;
				$operator = 'NOT IN';
			}
			$tax_query[] = array(
				'taxonomy' => $slug,
				'field'    => 'id',
				'terms'    => $ids,
				'operator' => $operator
			);
		}

		$query['tax_query'] = $tax_query;

	}

	// Allow plugins/themes developer to filter the default query.
	$query = apply_filters( 'rpwe_default_query_arguments', $query );

	// Perform the query.
	$posts = new WP_Query( $query );

	return $posts;

}

/**
 * Custom Styles.
 *
 * @since  0.8
 */
function rpwe_custom_styles() {
	?>
<style>
.rpwe-block ul{
    list-style:none;
    margin-left:0;
    padding-left:0;
    margin-bottom: 0;
}
.rpwe-block li {
    margin-bottom:0;
    padding-bottom:0;
    list-style-type: none;
}
.infoPosts{
    text-align: center;
}
.rpwe-block a{
    display:inline!important;
    text-decoration:none;
    color: white;
}
.rpwe-block h3{
    clear:none;
    margin-bottom:0;
    margin-top:0;
    font-weight:400;
    font-size:18px;
    line-height:1.5em;
    padding: 20px 0 20px 0;
}

.rpwe-thumb{
    box-shadow:none!important;
}
.rpwe-summary{
    font-size:12px;
    color: white;
    padding: 10px 10px 20px 10px;
}
.rpwe-block h3 {
    font-size: 38px;
}
.rpwe-time{
    color:#bbb;
    font-size:11px;
}.rpwe-comment{
     color:#bbb;
     font-size:11px;
     padding-left:5px;
 }
.rpwe-alignleft{
       display:inline;
    float:left;
}
.rpwe-alignright{
    display:inline;
    float:right;
}
.rpwe-aligncenter{
     display:block;
    margin-left: auto;
    margin-right: auto;
}
.rpwe-clearfix:before,.rpwe-clearfix:after{
    content:"";display:table !important;
}
.rpwe-clearfix:after{
    clear:both;
}
.rpwe-clearfix{
    zoom:1;
}
h5 {
    display: none;
}
.flexRow2 , .flexRow1{
    flex-direction: column;
}
.chaletsRecents li {
    padding: 0;
    margin: 0;
    border-bottom: 0;
}
.flex-even {
    flex: 1;
}
.rpwe-block li img {
    margin: 0;
    width: 100%;
    height: 100%;
}
.rpwe-block h3 {
    padding: 30px 0 20px 0;
}
.flexRow2 .infoPosts, .flexRow1 .img{
    border-right: solid 5px black;
}
.flexRow1 .infoPosts, .flexRow2 .img{
    border-left: solid 5px black;
}
.hr {
    width: 85%;
    border-bottom: solid 4px black;
}
.flexRow2 .hr{
    margin-left: auto;
}

@media screen and (min-width: 425px) {
    .rpwe-summary{
        font-size:15px;
        padding: 20px 10px 20px 10px;
    }
}
@media screen and (min-width: 576px) {
    .flexRow2 {
        flex-direction: row-reverse;
    }
    .flexRow1{
        flex-direction: row;
    }
    .rpwe-summary{
        font-size:15px;
        padding: 30px 20px ;
    }
}
@media screen and (min-width: 1024px) {
    .rpwe-summary{
        font-size:16px;
        padding: 40px 70px 20px 70px;
    }
}

</style>
	<?php
}
