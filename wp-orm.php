<?php
class WPORM {
    
    public static function insert($entity) {
        global $wpdb;
        $meta_class = get_class($entity) . '_Meta';
        $data = array();
        $column_formats = $meta_class::$column_formats;

        foreach ( get_object_vars( $entity ) as $key => $value ) {
            $data[$key] = is_array($value) ? serialize($value) : $value;
        }

        $data = array_intersect_key($data, $column_formats);
        $data_keys = array_keys($data);
        $column_formats = array_merge(array_flip($data_keys), $column_formats);

        // $wpdb->insert($meta_class::get_table_name(), $data, $column_formats);
        if ($wpdb->insert($meta_class::get_table_name(), $data) == false)
            $wpdb->print_error();

        return $wpdb->insert_id;
    }

    public static function get($entity) {
        global $wpdb;

        $class = get_class($entity);
        $meta_class = $class . '_Meta';

        $select_sql = 'SELECT * FROM ' . $meta_class::get_table_name();

        $where_sql = 'WHERE 1=1';
        $data = array();
        $column_formats = $meta_class::$column_formats;
        foreach ( get_object_vars( $entity ) as $key => $value )
            if (isset($column_formats[$key]) && !is_null($value))
                $where_sql .=  $wpdb->prepare(' AND ' . $key . '=' . $column_formats[$key], $value);

        $sql = "$select_sql $where_sql";
        $results = $wpdb->get_row($sql, ARRAY_A);
        if (!empty($results))
            $results = new $class($results);

        return $results;
    }

    public static function select($entity, $orderby = '') {
        global $wpdb;

        $class = get_class($entity);
        $meta_class = $class . '_Meta';

        $select_sql = 'SELECT * FROM ' . $meta_class::get_table_name();

        $where_sql = 'WHERE 1=1';
        $data = array();
        $column_formats = $meta_class::$column_formats;
        foreach ( get_object_vars( $entity ) as $key => $value )
            if (isset($column_formats[$key]) && !is_null($value))
                $where_sql .=  $wpdb->prepare(' AND ' . $key . '=' . $column_formats[$key], $value);

        $order_sql = $orderby == '' ? '' : 'ORDER BY ' . $orderby;

        $sql = "$select_sql $where_sql $order_sql";

        $results = $wpdb->get_results($sql, ARRAY_A);
        if (!empty($results)) {
            $list = array();
            foreach($results as $row) {
                $list[] = new $class($row);
            }
            $results = $list;
        }

        return $results;
    }

    public static function delete($entity) {
        global $wpdb;

        $meta_class = get_class($entity) . '_Meta';

        $column_formats = $meta_class::$column_formats;
        $where = array();
        $where_format = array();
        foreach ( get_object_vars( $entity ) as $key => $value )
            if (isset($column_formats[$key]) && !is_null($value)) {
                $where[$key] = $value;
                $where_format[] = $column_formats[$key];
            }

        $wpdb->delete($meta_class::get_table_name(), $where, $where_format);
    }

    public static function update($entity) {
        global $wpdb;

        $meta_class = get_class($entity) . '_Meta';
        $column_formats = $meta_class::$column_formats;

        $data = array();
        $where = array();
        $format = array();
        $where_format = array();
        foreach ( get_object_vars( $entity ) as $key => $value )
            if (isset($column_formats[$key]) && !is_null($value)) {
                if ($key == $meta_class::$primary_key) {
                    $where[$key] = $value;
                    $where_format[] = $column_formats[$key];
                } else {
                    $data[$key] = is_array($value) ? serialize($value) : $value;
                    $format[] = $column_formats[$key];
                }
            }

        return $wpdb->update($meta_class::get_table_name(), $data, $where, $format, $where_format);
        // return $wpdb->update($meta_class::get_table_name(), $data, $where);
    }
}

class WP_Entity {

    function __construct($data = array()) {
        foreach($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {
        $this->$property = $value;
        return $value;
    }    
}
