<?php

namespace News\PublicApi\Class;


class Class_DB {

    /**
     * Database table name constant
     * This constant defines the name of the database table used by the plugin.
     * It is used to create, update, and retrieve data from the database.
     */
    private const TB_NAME = "news_api_data";
    private $table_name;

    /**
     * Class constructor
     * This function initializes the database table name.
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . self::TB_NAME;
    }


    /**
     * Store API data in the database
     * This function will insert or update the API data in the database table created by the plugin.
     * It checks if the API URL already exists and updates it if it does, otherwise it inserts a new record.
     *
     * @param array $args {
     *     @type string $api_url The URL of the API.
     *     @type int $api_count The count of API calls.
     *     @type array $api_value The value returned from the API.
     * }
     * @return bool|\WP_Error Returns true on success or WP_Error on failure.
     */
    public function store_api_data($args) {
        global $wpdb;

        $data = [
            'api_url' => $args['api_url'],
            'api_count' => $args['api_count'],
            'api_value' => json_encode($args['api_value'])
        ];

        // Insert or Update data check if api_url already exists
        $existing_data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE api_url = %s",
            $args['api_url']
        ));

        if ($existing_data) {
            // Update existing record
            $result = $wpdb->update(
                $this->table_name,
                $data,
                ['api_url' => $args['api_url']]
            );
        } else {
            // Insert new record
            $result = $wpdb->insert($this->table_name, $data);
        }
        
        if ($result === false) {
            return new \WP_Error('db_insert_error', __('Failed to store API data in the database.'));
        }

        return true;
    }


    /**
     * Fetch stored data from the database
     * This function retrieves data from the database table created by the plugin.
     * It returns an array of results or an error if no data is found.
     *
     * @return array|\WP_Error
     */
    public function get_stored_data() {
        // fetch particular data from the database
        global $wpdb;
        $results = $wpdb->get_results($wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE api_id = %d", 1 ), ARRAY_A);
        if (empty($results)) {
            return new \WP_Error('no_data_found', __('No data found in the database.'));
        }
        return $results;
    }
    
}