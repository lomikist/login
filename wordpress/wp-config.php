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
define( 'DB_NAME', 'wp_plugins' );

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
define( 'AUTH_KEY',         'ASiKNdX`fH$##[E__FAXXZ(L:.e+?_3-*F1[xvdz3pm%Mzu,?B_Hd0vn;{2YY7V7' );
define( 'SECURE_AUTH_KEY',  'L7W)nbf$8V6Caota(tih}ra>D6{4>`9lFSco2!:AX^Vtm+YfGoS%Y3q/`@y%,~My' );
define( 'LOGGED_IN_KEY',    ' 9j*HG&c^sNM1A?-?u{%nTPQwU&js|/Pik)4`jXS8[>rns!iUeIBbYts5=_EiN&m' );
define( 'NONCE_KEY',        'X&f2[9?eC|v=Dcxg>2pB(&6rJh/7 _Nl>@QWtM*g#D@RtPE#mC#Tl#`YxI$ ?[6R' );
define( 'AUTH_SALT',        ' yj=&Oom|`.ZBWc.|Ff87wh)Lr!?H^-5k5GQEVRJ!%FgtBJ=__xDU;YQsLFV,MFs' );
define( 'SECURE_AUTH_SALT', 'fBv6|ak1R~GWB!-%_#;XJ<$RqLXSQv$0`_gc*eS]3xc{+,8Y  /}R1cfgwS3*dKf' );
define( 'LOGGED_IN_SALT',   'T<b1$NyYn>.!59lUU)Q@VL{l0^7Q5CwOg|Q[c@B=.j k~}ExZA^9`f`h6*W|yQLL' );
define( 'NONCE_SALT',       '-f7YL,P8tU{7SjV[uW* {U2N,ET$Ru[^dz:7QfZS^VlPR.nJj%e1DJ,z+PFLN$g4' );

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
