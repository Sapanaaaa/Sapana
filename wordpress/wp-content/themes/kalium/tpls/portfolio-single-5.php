<?php
/**
 *    Kalium WordPress Theme
 *
 *    Laborator.co
 *    www.laborator.co
 *
 * @deprecated 3.0 This template file will be removed or replaced with new one in templates/ folder.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

include locate_template( 'tpls/portfolio-single-item-details.php' );

$sharing_allowed = $portfolio_share_item || $portfolio_likes;

$portfolio_type_full_bg = true;

$show_project_info_text   = kalium_get_field( 'show_project_info_text' );
$hide_project_description = kalium_get_field( 'hide_project_description' );

add_filter( 'kalium_show_footer', '__return_false' );

do_action( 'kalium_portfolio_item_before', 'type-5' );

?>
<ul class="portfolio-full-bg-slider" data-autoswitch="<?php echo intval( kalium_get_field( 'auto_play' ) ); ?>">
	<?php

	$gallery_items_count = 0;

	foreach ( $gallery_items as $i => $gallery_item ) :

		if ( empty( $gallery_item['acf_fc_layout'] ) ) {
			continue;
		}

		// Image Type
		if ( $gallery_item['acf_fc_layout'] == 'image' ) :

			$img = $gallery_item['image'];

			// If image doesn't exists
			if ( ! $img ) {
				continue;
			}

			$image_class = array( 'image-entry' );
			?>
            <li class="image-entry" data-load="<?php echo esc_url( $img['url'] ); ?>"></li>
			<?php

			$gallery_items_count ++;

		// Video Type
        elseif ( 'video' === $gallery_item['acf_fc_layout'] ) :

			$classes = [
				'image-entry',
			];

			// Video Information
			$video_type     = kalium_get_array_key( $gallery_item, 'video_type' );
			$video_src      = kalium_get_array_key( $gallery_item, 'video_src' );
			$video_youtube  = kalium_get_array_key( $gallery_item, 'youtube_url' );
			$video_poster   = kalium_get_array_key( $gallery_item, 'video_poster' );
			$video_autoplay = kalium_get_array_key( $gallery_item, 'autoplay' );
			$video_mute     = kalium_get_array_key( $gallery_item, 'mute' );
			$video_loop     = kalium_get_array_key( $gallery_item, 'loop' );
			$video_controls = kalium_get_array_key( $gallery_item, 'controls' );
			$video_fitview  = kalium_get_array_key( $gallery_item, 'viewport_fit' );

			// YouTube video
			if ( $video_youtube && 'youtube' === $video_type ) {
				$video_src = $video_youtube;
			}

			// Fit to view
			if ( $video_fitview ) {
				$classes[] = 'fit-video-to-viewport';
			}

			// Autoplay video
			if ( $video_autoplay ) {
				$classes[] = 'autoplay-video';
			}
			?>
            <li <?php kalium_class_attr( $classes ); ?> data-video="true">
				<?php
				/**
				 * Show video element.
				 */
				kalium_render_video_element( $video_src, [
					'poster'      => $video_poster,
					'poster_size' => 'full',
					'autoplay'    => false,
					'loop'        => $video_loop,
					'controls'    => $video_controls,
					'muted'       => $video_autoplay ? true : $video_mute,
					'posterplay'  => ! $video_controls && ! $video_autoplay,
					'echo'        => true,
				] );
				?>
            </li>
			<?php
			$gallery_items_count ++;
		endif;

	endforeach;

	?>
</ul>

<div class="portfolio-full-bg-loader loading-spinner-1"></div>

<div class="container">

    <div class="page-container no-bottom-margin">

        <div class="single-portfolio-holder portfolio-type-5">

            <div class="portfolio-slider-nav">
				<?php for ( $i = 1; $i <= $gallery_items_count; $i ++ ) : ?>
                    <a href="#" data-index="<?php echo esc_attr( $i - 1 ); ?>" class="<?php echo when_match( $i == 1, 'current' ); ?>">
                        <span><?php echo esc_html( $i ); ?></span>
                    </a>
				<?php endfor; ?>
            </div>

			<?php include locate_template( 'tpls/portfolio-single-prevnext.php' ); ?>

			<?php if ( ! $hide_project_description ) : ?>
                <div class="portfolio-description-container<?php when_match( kalium_get_field( 'item_description_visibility' ) == 'collapsed', 'is-collapsed' ); ?>">

                    <div class="portfolio-description-showinfo">
                        <h3><?php the_title(); ?></h3>

						<?php if ( ! empty( $show_project_info_text ) ) : ?>
                            <p><?php echo $show_project_info_text; ?></p>
						<?php else: ?>
                            <p><?php _e( 'Click here to show project info', 'kalium' ); ?></p>
						<?php endif; ?>

                        <a href="#" class="expand-project-info">
							<?php echo kalium_get_svg_file( 'assets/images/icons/arrow-upright.svg' ); ?>
                        </a>
                    </div>

                    <div class="portfolio-description-fullinfo details">
						<?php
						do_action( 'kalium_portfolio_type_fullscreen_before_title' );
						?>

                        <div class="title section-title">
                            <h1><?php the_title(); ?></h1>

							<?php if ( $sub_title ) : ?>
                                <p><?php echo wp_kses_post( $sub_title ); ?></p>
							<?php endif; ?>
                        </div>

                        <div class="project-description">
                            <div class="post-formatting">
								<?php the_content(); ?>
                            </div>
                        </div>

						<?php include locate_template( 'tpls/portfolio-launch-project.php' ); ?>

						<?php if ( $checklists || $sharing_allowed ): ?>
                            <div class="row">
								<?php if ( $checklists ): ?>
                                    <div class="<?php echo $sharing_allowed ? 'col-md-6' : 'col-md-12'; ?>">
										<?php include locate_template( 'tpls/portfolio-checklists.php' ); ?>
                                    </div>
								<?php endif; ?>

								<?php if ( $sharing_allowed ): ?>
                                    <div class="<?php echo $checklists ? 'col-md-6' : 'col-md-12'; ?> portfolio-sharing-container">
										<?php include locate_template( 'tpls/portfolio-single-like-share.php' ); ?>
                                    </div>
								<?php endif; ?>
                            </div>
						<?php endif; ?>

                        <a href="#" class="collapse-project-info">
							<?php echo kalium_get_svg_file( 'assets/images/icons/arrow-upright.svg' ); ?>
                        </a>
                    </div>

                </div>
			<?php endif; ?>

        </div>

    </div>

</div>