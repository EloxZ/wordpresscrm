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
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Clavederoot123$' );

/** MySQL hostname */
define( 'DB_HOST', 'SG-Eloy-5577-mysql-master.servers.mongodirector.com' );

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
define( 'AUTH_KEY',         'W{kL> Yy!RT-:RjD7T{H:nZX?e(?3hA[U*Umyfu)/vh|4}E^LERVNL:,W^V0rfyZ' );
define( 'SECURE_AUTH_KEY',  'M)#.@|4 kk(Nwe-~npX);htyZhmO@+v2_uw;x|)s%+gk[[5FvRfBmDW0YqVoI!*;' );
define( 'LOGGED_IN_KEY',    '+0kq{[-z~1Wfz;bm+tF;~mOGTAQcCpRK-Yzavd!DIK(1d.Q0Nd<k/6|E1mBU*6Kh' );
define( 'NONCE_KEY',        '|/-E`tOjYixtQhn|AV5dcclc]j|VQ<OsK44e)2Ac/MLf+)Gd!vekyvEyny(<CevW' );
define( 'AUTH_SALT',        '3Bh$YMLv}{(:d_4,|A,Hhij[CvKUn{,~Rkhz8)oI[}X4Nomz++Tc5;Fe|dVg!pMD' );
define( 'SECURE_AUTH_SALT', 'IrH#&&Dz6DwZqnF]87#^gOdoje#,25{K?{6WKeai;*EoZ8.]pn6F`DR)k*b#ABB{' );
define( 'LOGGED_IN_SALT',   '%*[2Re} Hh*<(3E#:_~oh(V#7pPc#e[JN!.eXaM3@TnU^dAW}py(Tk!.@TlD.0f(' );
define( 'NONCE_SALT',       '@?u.L$JWtfF#$3c-r;T#XE%&<U.@PD%7epjKa~y4A>MkOU+;+Or.q943,!uexQ.)' );

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
