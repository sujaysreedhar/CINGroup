<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'cingroup' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '1_d^!2<v<#l}Je]DKZX-[j/r?Y{IDGpIYV3(=LV9ro=m/&2sGR{jhYh2: ~* ]K5' );
define( 'SECURE_AUTH_KEY',  'RN{%%E7R$t;e%K=u9riNj$uSxH8^24X6QI<3zqU=g]68,+H9NJO~phHbF07ipIsu' );
define( 'LOGGED_IN_KEY',    '(KU1J5^eObyV|( *&i{o9U;f8vsX5/|r2Uh@e5~?g5pij+-OT>_#r.[G]dPe:M9<' );
define( 'NONCE_KEY',        'b&p|O]:2vU@$2Zo{qbrclat,_V7;5yI4!r=xLB7f$0,N;FO1TcWj;)Ig?wg&cBFX' );
define( 'AUTH_SALT',        'bj5uUQ3(Nz:9E#_^Fk=y-(:DSnnRDq-PONagp<7rvUdtA+v-ZVz7~z@Mr-z&D>7@' );
define( 'SECURE_AUTH_SALT', '|du[]v2Td8r#@]m[ }S[HJl8a6#0R$(oA4bbd$f.l1rmA;Y<=OjE(~lL_/<%tL~~' );
define( 'LOGGED_IN_SALT',   '6^a*?$dT72duRr-Iphl:Zs3!4k5(OmeHWYz|I_+23RF[&rU~W|4#[.<kM> lh8[Y' );
define( 'NONCE_SALT',       'y)ZByyHD;i[ph}NQr@J%wIu2Q%+z~{MrM{,]iX,<payX~GL?1;WApW( 5]8pk>|(' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );
define('FS_METHOD','direct');

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
