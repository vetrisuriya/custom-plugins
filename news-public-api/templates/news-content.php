<?php
$get_stored_data_cls = new News\PublicApi\Class\Class_Api();
$stored_data = $get_stored_data_cls->get_api_data();

$fetched_datas = json_decode($stored_data[0]['api_value'], true);
if ($fetched_datas['status'] === 'error') {
    echo '<div class="notice notice-error"><p>' . esc_html($fetched_datas['message']) . '</p></div>';
    return;
}

if($fetched_datas['status'] === 'success' && $fetched_datas['data']['status'] !== 'OK') {
    echo '<div class="notice notice-error"><p>Request Not Satisfied!</p></div>';
    return;
}

$all_datas = $fetched_datas['data']['data'] ?? [];
if (empty($all_datas)) {
    echo '<div class="notice notice-warning"><p>No data found.</p></div>';
    return;
}
?>