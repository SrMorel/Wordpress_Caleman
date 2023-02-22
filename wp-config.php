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
define( 'DB_NAME', 'wordpress_pruebas' );

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
define( 'AUTH_KEY',         'I[I:R/4)J*qcL3u9hUdG-m.,l_K_Q-=-[LE_wM@24T ]>Ny )7a:]4*(+zvaS>Qp' );
define( 'SECURE_AUTH_KEY',  ']pN^)Y4oi8R)2gZ+>8o@(h84{H-S()d$,qYC369!2L&KjX4)T!Yode%^30TGNb^N' );
define( 'LOGGED_IN_KEY',    'drH8Hp+b#0cWe)1_?4]3awb:gs7g4OH~_@(Y2E^@l-:-Ww`Y~Un|3rWm-kH]s,*@' );
define( 'NONCE_KEY',        '^7_X2:^KY0Fe+T:`>m;;Mg9yDHBj!]95YmTKT R[>BxtW.Va?T93-O7~_Vc3E#|?' );
define( 'AUTH_SALT',        'J:!v[xTj/>yt1{U6bP< njK=U+m7^_%ukUe3;nIe:V8zX8q[MHD|:N)av/i3!cfd' );
define( 'SECURE_AUTH_SALT', 'Gl%M~?r7ekf!A|~yS8co_LZ3b;`He&.Cxtfv)6iH{;,N-Ewhz21Nkx) U6@19IoB' );
define( 'LOGGED_IN_SALT',   '&u+rp&J$u{{$=RJGwaX@1U(:v%%O$%,NwI~7n ]JIo+TTDuxUMxPb0TdGik+}OB&' );
define( 'NONCE_SALT',       '!?J!Tu+_Qv$r~ !9FbuKc=!d3N5xT=QG#J^S`J5W[XhT~7rP@8g[vURi6ULrEiDC' );

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

define( 'WP_ALLOW_MULTISITE', true );

