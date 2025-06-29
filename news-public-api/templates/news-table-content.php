<table class="news-content-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Thumbnail</th>
            <th>Title</th>
            <th>Link</th>
            <th>Published</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($all_datas as $dk => $dv) {
        ?>
            <tr>
                <td><?php echo esc_html($dk + 1); ?></td>
                <td><img src="<?php echo esc_url($dv['photo_url']); ?>" alt="<?php echo esc_html($dv['title']); ?>" width="150px"></td>
                <td><?php echo esc_html($dv['title']); ?></td>
                <td><a href="<?php echo esc_url($dv['link']); ?>" target="_blank"><?php echo esc_html($dv['link']); ?></a></td>
                <td><?php echo esc_html(date('h:i:a d-m-Y', strtotime($dv['published_datetime_utc']))); ?></td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>