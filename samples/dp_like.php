<?php
class DP_Like {
    public $like_id;
    public $user_id;
    public $post_id;
    public $post;
    public $item_type;

    function __construct($data = array()) {
        foreach($data as $key => $value) {
            $this->$key = $value;
        }
    }
}

class DP_Like_Meta {
    public static $column_formats = array(
        'like_id'=> '%d',
        'user_id'=> '%d',
        'post_id'=> '%d',
        'item_type'=>'%s'
        );
    public static function get_table_name() {
        return 'wp_dp_user_post_like';
    }
    public static function create_table() {
        global $wpdb;
        global $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql_create_table = "CREATE TABLE " . self::get_table_name() . " (
            like_id bigint(20) unsigned NOT NULL auto_increment,
            user_id bigint(20) unsigned NOT NULL default '0',
            post_id bigint(20) unsigned NOT NULL default '0',
            item_type varchar(255) default NULL,
            PRIMARY KEY  (like_id),
            KEY user_id (user_id),
            KEY item_type (item_type),
            KEY post_id (post_id)
            ) $charset_collate";

        dbDelta($sql_create_table);        
    }
}

register_activation_hook( __FILE__, array( 'DP_Like_Meta', 'create_table' ) );
