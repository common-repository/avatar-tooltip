<?php 
/*
Plugin Name: Avatar Tooltip
Plugin URI: 
Description: To show tooltip with user/author info on avatar mouseover/click
Version: 1.0.2
Author: Axenso
Author URI: http://www.axenso.com
*/

/*  Copyright 2013

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/




/**
 * Plugin costants
 */
define( "AXE_AT_PLUGIN_DIR", basename( dirname(__FILE__) ) );
define( "AXE_AT_PLUGIN_URL", untrailingslashit( plugin_dir_url(__FILE__) ) );
define( "AXE_AT_PLUGIN_ABS", untrailingslashit( plugin_dir_path(__FILE__) ) );



// Core files
require_once( 'php/hooks.php' ); // must be the first
require_once( 'php/admin-menu-and-bar.php' );
require_once( 'php/ajax.php' );
require_once( 'php/functions.php' );



/* EOF */
