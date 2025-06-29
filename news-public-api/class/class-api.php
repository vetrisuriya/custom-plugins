<?php

namespace News\PublicApi\Class;


require_once(PLUGIN_CLASS_PATH."class-db.php");


class Class_Api {

    /**
     * API URL and arguments
     * These constants define the API endpoint and the arguments required to make a request.
     * The API URL is set to fetch world news headlines for India in English.
     * The API arguments include headers for authentication and other request parameters.
     */
    private const API_URL = "https://real-time-news-data.p.rapidapi.com/topic-headlines?topic=WORLD&limit=5&country=IN&lang=en";
    private const API_ARGS = [
        'headers' => [
            "x-rapidapi-host" => "real-time-news-data.p.rapidapi.com",
            "x-rapidapi-key" => "8024b55398msh33ea5f95933470ap1c0ba1jsn77892742148d"
        ],
        'timeout' => 30,
        'request' => 'GET',
        'sslverify' => false,
        'httpversion' => '1.1',
        'redirection' => 10,
        'encode' => 'json',
    ];

    /**
     * API data and error
     * These properties will store the data fetched from the API and any error that may occur during the request.
     * The data will be processed and stored in a structured format for later retrieval.
     */
    private $api_data;
    private $api_error;

    /**
     * Class_DB instance to handle database operations
     * This instance will be used to store and retrieve API data from the database.
     */
    private Class_DB $db_cls;


    public function __construct() {
        $this->db_cls = new \News\PublicApi\Class\Class_DB();
    }

    /**
     * Fetch data from the API
     * This method will make a GET request to the API URL defined in the class constant.
     * It will handle any errors that may occur during the request and store the data in the database.
     * The data will be processed and stored in a structured format for later retrieval.
     */
    public function fetch_data_from_api() {

        // wp_remote_get( string $url, array $args = array() ): array|WP_Error
        $get_api_data = wp_remote_get(self::API_URL, self::API_ARGS);
        if (is_wp_error($get_api_data)) {
            $this->api_error = $get_api_data->get_error_message();
            return;
        }  

        $response_body = wp_remote_retrieve_body($get_api_data);
        $this->api_data = json_decode($response_body, true);

        $this->process_api_data();
    }

    /**
     * Process the API data
     * This method will check if there is an error in the API response.
     * If there is an error, it will store the error message in the database.
     * If the data is successfully retrieved, it will store the data in a structured format.
     */
    public function process_api_data() {
        if($this->api_error) {
            $data = [
                'status' => 'error',
                'message' => $this->api_error
            ];
        }
        
        $data = [
            'status' => 'success',
            'data' => $this->api_data
        ];


        $this->db_cls->store_api_data([
            'api_url' => self::API_URL,
            'api_count' => 5,
            'api_value' => $data,
        ]);
    }


    /**
     * Get stored API data
     * This method will retrieve the stored API data from the database.
     * It will return the data in a structured format for further processing or display.
     *
     * @return array The stored API data
     */
    public function get_api_data() {
        return $this->db_cls->get_stored_data();
    }

}