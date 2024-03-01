<?php
/**
 * Kalium WordPress Theme
 *
 * What's new page.
 *
 * @var string $version
 * @var array  $changelog
 *
 * @author Laborator
 * @link   https://kaliumtheme.com
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

?>
    <div class="row">
        <div class="col col-xs-12 col-auto">
            <div class="about-kalium__version-num">
				<?php echo esc_html( $version ); ?>
            </div>
        </div>
        <div class="col">
            <div class="about-kalium__heading no-top-margin">
                <h2>What&rsquo;s new in Kalium</h2>
                <p>
                    Kalium continuously expands with new features, bug fixes and other adjustments to provide a smoother
                    experience for everyone.
                    Scroll down to see what&rsquo;s new in this version. For a complete list of changes
                    <a href="#changelog">read the full changelog</a>.</p>
            </div>
        </div>
    </div>

    <div class="about-kalium__whats-new row">
        <div class="col col-6 col-md-6 col-xs-12">

            <div class="about-kalium__whats-new-item">
                <a href="#" target="_blank" rel="noreferrer noopener" class="about-kalium__whats-new-item-link disabled">
                    <img src="<?php echo kalium()->assets_url( 'admin/images/whats-new/wordpress-60.jpg' ); ?>" width="330" height="200" loading="lazy" alt="wordpress-compatibility">
                </a>

                <h4 class="about-kalium__whats-new-item-title">WordPress 6.0 Compatibility</h4>

                <p class="about-kalium__whats-new-item-description">
                    Improvements to WordPress are never at a standstill and as always Kalium supports the latest WordPress features and techniques.
                </p>
            </div>

        </div>
        <div class="col col-6 col-md-6 col-xs-12">

            <div class="about-kalium__whats-new-item">
                <a href="#" target="_blank" rel="noreferrer noopener" class="about-kalium__whats-new-item-link disabled">
                    <img src="<?php echo kalium()->assets_url( 'admin/images/whats-new/woocommerce-latest.jpg' ); ?>" width="330" height="200" loading="lazy" alt="woocommerce-compatibility">
                </a>

                <h4 class="about-kalium__whats-new-item-title">Latest WooCommerce Compatibility</h4>

                <p class="about-kalium__whats-new-item-description">
                    Better shopping experience with the new update from WooCommerce which is compatible with this
                    version of Kalium.
                </p>
            </div>

        </div>
        <div class="col col-6 col-md-6 col-xs-12">

            <div class="about-kalium__whats-new-item">
                <a href="#" target="_blank" rel="noreferrer noopener" class="about-kalium__whats-new-item-link disabled">
                    <img src="<?php echo kalium()->assets_url( 'admin/images/whats-new/plugin-updates.jpg' ); ?>" width="330" height="200" loading="lazy" alt="speed-improvements">
                </a>

                <h4 class="about-kalium__whats-new-item-title">Premium Plugin Updates</h4>

                <p class="about-kalium__whats-new-item-description">
                    Updating plugins is an important thing as updating your theme, as always Kalium offers the latest plugin updates for the included premium plugins.
                </p>
            </div>

        </div>
        <div class="col col-6 col-md-6 col-xs-12">

            <div class="about-kalium__whats-new-item">
                <a href="#" target="_blank" rel="noreferrer noopener" class="about-kalium__whats-new-item-link disabled">
                    <img src="<?php echo kalium()->assets_url( 'admin/images/whats-new/bug-fixes.jpg' ); ?>" width="330" height="200" loading="lazy" alt="bug-fixes">
                </a>

                <h4 class="about-kalium__whats-new-item-title">Bug Fixes</h4>

                <p class="about-kalium__whats-new-item-description">
                    Numerous bugs which have been reported by our users have been fixed by our team, we cannot count
                    them.
                </p>
            </div>

        </div>
    </div>

<?php
// Show changelog
if ( ! empty( $changelog ) ) :

	// Changelog date format
	$date_format = 'F d, Y';
	?>
    <a id="changelog"></a>

	<?php
	foreach ( $changelog as $changelog_entry ) :
		if ( ! kalium_get_array_key( $changelog_entry, 'expand' ) ) {
			continue;
		}
		?>
        <div class="about-kalium__changelog">
            <h3 class="about-kalium__changelog-title"><?php echo sprintf( 'Changelog &ndash; Version %s (%s)', esc_html( $changelog_entry['version'] ), esc_html( date_i18n( $date_format, strtotime( $changelog_entry['date'] ) ) ) ); ?></h3>

			<?php
			// Change type
			foreach ( $changelog_entry['changes'] as $type => $changes ) {

				if ( empty( $changes ) ) {
					continue;
				}

				?>
                <div class="about-kalium__changelog-type">
                    <div class="about-kalium__changelog-type-title about-kalium__changelog-type-title-<?php echo sanitize_title( $type ); ?>"><?php echo esc_html( $type ); ?></div>
                    <ul>
						<?php foreach ( $changes as $title ) : ?>
                            <li><?php echo links_add_target( make_clickable( esc_html( $title ) ) ); ?></li>
						<?php endforeach; ?>
                    </ul>
                </div>
				<?php
			}
			?>
        </div>
	<?php endforeach; ?>

    <div class="about-kalium__changelog-previous-title row align-items-center">
        <div class="col">
            <h3 class="no-top-margin">Previous versions</h3>
        </div>
        <div class="col text-align-right">
            <a href="https://documentation.laborator.co/kb/kalium/kalium-changelog/" target="_blank" rel="noreferrer noopener">View
                all changelogs</a>
        </div>
    </div>

    <ul class="about-kalium__changelog-previous">
		<?php
		$i = 0;
		foreach ( $changelog as $changelog_entry ) :
			if ( kalium_get_array_key( $changelog_entry, 'expand' ) || $i >= 3 ) {
				continue;
			}
			?>
            <li class="about-kalium__changelog-previous-entry">
                <div class="about-kalium__changelog-previous-entry-date"><?php echo esc_html( date_i18n( $date_format, strtotime( $changelog_entry['date'] ) ) ); ?></div>
                <div class="about-kalium__changelog-previous-entry-version"><?php echo esc_html( $changelog_entry['version'] ); ?></div>
                <div class="about-kalium__changelog-previous-entry-link">
                    <a href="https://documentation.laborator.co/kb/kalium/kalium-changelog/#version-<?php echo esc_attr( $changelog_entry['version'] ); ?>" target="_blank" rel="noreferrer noopener">
                        Read changelog
                    </a>
                </div>
            </li>
			<?php
			$i ++;
		endforeach;
		?>
    </ul>

<?php
endif;
?>