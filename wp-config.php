<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'proyecto4' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'admin' );

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
define( 'AUTH_KEY',         ' ;)2Pnea=};0cR7*3Ym6~4F4+MmwRD};?I2y2oH+p``n^89S%_X`HF}DKbc[dp*q' );
define( 'SECURE_AUTH_KEY',  ':O$w>;a5[avn5x%hH5Aw&M r)g*-2aLA}MB(Zm!*;vI7D(LUZ_/Fv!wOd,E2$xVb' );
define( 'LOGGED_IN_KEY',    'qhrHh0sBlT]U#_TFG{&gRWC]!F-)h5j/H.r[#-=su`PmpB6Jy-iV%nZZM3I9xo#`' );
define( 'NONCE_KEY',        'pRON#xSC-VkQFe8-sa=p>RVO^`V;Yv0fKU6-ANh5A<0{gSLaQT@.0Jd6Pbqf$3m,' );
define( 'AUTH_SALT',        '.2[tFg~p8P){)nd>k}xur13`4%6UEcf}![J~~!Uozucy^UGhE@^m3a5{VVL#FMNu' );
define( 'SECURE_AUTH_SALT', '|HA2GeM]Wr(dPI~Oa:3{uaIVDD83pKJF4H)j4%j`e3^2Y(5ij;=30KO3V-O1xBX?' );
define( 'LOGGED_IN_SALT',   '^<@l#x*[z*];Vz]DE9jeS0~3&BLz2U4MOh`wVZ7X~%3A ke?Id9&!T_7Hj+t$A06' );
define( 'NONCE_SALT',       'f[b(Q|685|&<x7r5.CvTe;t.I*ISQy>0o&&-bf@HEI6ENDX4#bO-q/*^_CWQX4)K' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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
