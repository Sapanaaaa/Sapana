<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'sapana' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '6kkyf)]dM,$pzkQW/y%5.Pg 4?of<p?ioQyJAb)(}ao]noytl0Ed^9kFj?dmmy~H' );
define( 'SECURE_AUTH_KEY',  '*(<Uy9?%prn%NO3|K1kBdML5E:H2Qd]WIjXl*!@oplQR3VMYP>pOzw`}w@1nt9x0' );
define( 'LOGGED_IN_KEY',    'c(/fivZVO0q-1}Bi|VfLo:HdC&_Z=ljy<1+O%PUU59-HU7)(PbCT5f^)HQv}^e9J' );
define( 'NONCE_KEY',        '!Srr49i`|acV$+HlVc?%hhulc@WrghQk!|7lAf3P,Wm>s.q%#WoB} mCk@,a{A(j' );
define( 'AUTH_SALT',        'R|nU!XC$)V2i=KuZOd<c{ISk#M:<O6oUwf)6} 2vaS}aTp=QD_38IpkU(re/D?Ra' );
define( 'SECURE_AUTH_SALT', 'N)GD>y$OQQMuybu7#dx =UO^_*qst5Qucf{mgH9qkK,Z)!&81`0cO|pj)w-2V^6j' );
define( 'LOGGED_IN_SALT',   ':^m[vN`!#e#F~KVG<eDhXRXoR:{z>0:TWe,<Is8lv7:7.$xVD=oHw)Bnc@_C W_G' );
define( 'NONCE_SALT',       'GUDz%&Ix}(&p5t1)4ul(I>8N|D5*OFe|10O$|9C&$!6%Q;U6;dZZY,eWnM?_3aP ' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
