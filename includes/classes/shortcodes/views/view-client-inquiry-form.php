<?php
if (!current_user_can('administrator') && !current_user_can('secretary')) {
    echo '<p>You do not have permission to use this form.</p>';
    return;
}

// Handle 'Complete' action
if (isset($_GET['complete_request']) && current_user_can('administrator')) {
    $complete_id = intval($_GET['complete_request']);
    $post = get_post($complete_id);
    if ($post && $post->post_type === 'request') {
        wp_update_post([
            'ID' => $complete_id,
            'post_status' => 'draft',
        ]);
        echo '<div class="ci-notice ci-success">✅ Request marked as complete.</div>';
    }
}

$editing = false;
$edit_request_id = isset($_GET['edit_request']) ? intval($_GET['edit_request']) : 0;
$edit_data = [];

if ($edit_request_id) {
    $post = get_post($edit_request_id);
    if ($post && $post->post_type === 'request') {
        $editing = true;
        $edit_data = [
            'client_name'     => $post->post_title,
            'client_email'    => get_post_meta($post->ID, 'es_request_email', true),
            'client_phone'    => get_post_meta($post->ID, 'es_request_tel', true)['tel'] ?? '',
            'message'         => $post->post_content,
            'property_id'     => get_post_meta($post->ID, 'es_request_post_id', true),
            'assigned_agent'  => get_post_field('post_author', $post->ID),
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['client_inquiry_nonce']) && wp_verify_nonce($_POST['client_inquiry_nonce'], 'save_client_inquiry')) {
    $client_name      = sanitize_text_field($_POST['client_name']);
    $client_email     = sanitize_email($_POST['client_email']);
    $client_phone     = sanitize_text_field($_POST['client_phone']);
    $message          = sanitize_textarea_field($_POST['notes']);
    $property_id      = intval($_POST['property_id']);
    $assigned_user_id = intval($_POST['assigned_agent']);

    $post_data = [
        'post_type'    => 'request',
        'post_status'  => 'publish',
        'post_title'   => $client_name,
        'post_author'  => $assigned_user_id,
        'post_content' => $message,
    ];

    $post_id = $editing ? wp_update_post(array_merge($post_data, ['ID' => $edit_request_id])) : wp_insert_post($post_data);

    if (!is_wp_error($post_id)) {
        $agent_entity = get_posts([
            'post_type'   => 'agent',
            'meta_key'    => 'es_agent_user_id',
            'meta_value'  => $assigned_user_id,
            'numberposts' => 1,
        ]);

        if (!empty($agent_entity)) {
            $agent_post_id = $agent_entity[0]->ID;

            update_post_meta($post_id, 'es_request_post_id', $property_id);
            update_post_meta($post_id, 'es_request_recipient_entity_id', $agent_post_id);
            update_post_meta($post_id, 'es_request_email', $client_email);
            update_post_meta($post_id, 'es_request_is_viewed', 0);
            update_post_meta($post_id, 'es_request_tel', ['code' => '', 'tel' => $client_phone]);

            if (!$editing) {
                foreach ([$message, $client_name, $client_phone, $client_email] as $kw) {
                    add_post_meta($post_id, 'es_request_keywords', $kw);
                }
            }

            echo '<div class="ci-notice ci-success">✅ Request ' . ($editing ? 'updated' : 'submitted') . ' successfully.</div>';
        } else {
            echo '<div class="ci-notice ci-error">❌ Agent not found or not properly linked.</div>';
        }
    } else {
        echo '<div class="ci-notice ci-error">❌ Failed to ' . ($editing ? 'update' : 'create') . ' request.</div>';
    }
}

$agent_posts = get_posts([
    'post_type'   => 'agent',
    'numberposts' => -1,
    'meta_key'    => 'es_agent_user_id',
]);

$agents = array_filter(array_map(function ($post) {
    $user_id = get_post_meta($post->ID, 'es_agent_user_id', true);
    $user = $user_id ? get_userdata($user_id) : null;
    return $user ? (object)['user_id' => $user->ID, 'name' => $user->display_name] : null;
}, $agent_posts));

$properties = get_posts([
    'post_type'      => 'properties',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
]);

?>
<style>
.client-inquiry-form {
    max-width: 800px; margin: 20px auto; padding: 20px;
    background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px;
}
.client-inquiry-form label {
    font-weight: bold; display: block; margin-bottom: 5px;
}
.client-inquiry-form input, .client-inquiry-form textarea, .client-inquiry-form select {
    width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;
}
.client-inquiry-form input[type="submit"] {
    background: #0073aa; color: white; cursor: pointer;
}
.ci-notice { max-width: 800px; margin: 10px auto; padding: 12px; text-align: center; font-weight: bold; border-radius: 5px; }
.ci-success { background: #d4edda; color: #155724; }
.ci-error { background: #f8d7da; color: #721c24; }
.inquiries-table { width: 100%; border-collapse: collapse; margin-top: 40px; }
.inquiries-table th, .inquiries-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
.inquiries-table th { background-color: #f2f2f2; }
.inquiries-table tr:nth-child(even) { background-color: #fafafa; }
.inquiries-table td a { margin-right: 8px; text-decoration: none; color: #0073aa; }
.inquiries-table td a:hover { text-decoration: underline; }
</style>

<form method="post" class="client-inquiry-form">
    <?php wp_nonce_field('save_client_inquiry', 'client_inquiry_nonce'); ?>
    <label>Client Name</label>
    <input type="text" name="client_name" value="<?php echo esc_attr($edit_data['client_name'] ?? ''); ?>" required>

    <label>Client Email</label>
    <input type="email" name="client_email" value="<?php echo esc_attr($edit_data['client_email'] ?? ''); ?>" required>

    <label>Client Phone</label>
    <input type="text" name="client_phone" value="<?php echo esc_attr($edit_data['client_phone'] ?? ''); ?>" required>

    <label>Message</label>
    <textarea name="notes" rows="4" required><?php echo esc_textarea($edit_data['message'] ?? ''); ?></textarea>

    <label>Property</label>
    <select name="property_id" required>
        <option value="">Select a property</option>
        <?php foreach ($properties as $prop):
            $ref_number = get_post_meta($prop->ID, 'es_property_ref-number', true);
            $title_with_ref = ($ref_number ? $ref_number . ' - ' : '') . $prop->post_title;
        ?>
            <option value="<?php echo $prop->ID; ?>" <?php selected((int)($edit_data['property_id'] ?? 0), (int)$prop->ID); ?>>
                <?php echo esc_html($title_with_ref); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Assign to Agent</label>
    <select name="assigned_agent" required>
        <option value="">Select an agent</option>
        <?php foreach ($agents as $agent): ?>
            <option value="<?php echo $agent->user_id; ?>" <?php selected($edit_data['assigned_agent'] ?? '', $agent->user_id); ?>>
                <?php echo esc_html($agent->name); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="submit" value="<?php echo $editing ? 'Update Request' : 'Submit Request'; ?>">
    <?php if ($editing): ?>
        <a href="<?php echo esc_url(remove_query_arg('edit_request')); ?>" 
            style="display: block; width: 100%; text-align: center; padding: 10px; background: #0073aa; color: white; border: none; border-radius: 4px; text-decoration: none; margin-top: 10px;">
            Cancel Edit
        </a>
    <?php endif; ?>
</form>

<?php
$query = new WP_Query([
    'post_type'        => 'request',
    'post_status'      => 'publish',
    'posts_per_page'   => -1,
    'orderby'          => 'date',
    'order'            => 'DESC',
]);

if ($query->have_posts()) {
    echo '<h3>Submitted Inquiries</h3>';
    echo '<table class="inquiries-table">';
    echo '<tr><th>Date</th><th>Client</th><th>Agent</th><th>Property</th><th>Message</th><th>Actions</th></tr>';

    while ($query->have_posts()) {
        $query->the_post();
        $id = get_the_ID();
        $client_name = get_the_title();
        $agent_id = get_post_meta($id, 'es_request_recipient_entity_id', true);
        $agent_name = $agent_id ? get_the_title($agent_id) : '-';

        $prop_id = get_post_meta($id, 'es_request_post_id', true);
        $ref_number = get_post_meta($prop_id, 'es_property_ref-number', true);
        $prop_title = $prop_id ? get_the_title($prop_id) : '-';
        $full_property = $ref_number ? "$prop_title <br><small style='color:gray;'>Ref: $ref_number</small>" : $prop_title;

        $message = get_the_content();
        $date = get_the_date('Y/m/d H:i', $id);

        $edit_link = add_query_arg('edit_request', $id);
        $complete_link = add_query_arg('complete_request', $id);

        echo "<tr>
            <td>{$date}</td>
            <td>{$client_name}</td>
            <td>{$agent_name}</td>
            <td>{$full_property}</td>
            <td>{$message}</td>
            <td>
                <a href='{$edit_link}'>Edit</a>
                <a href='{$complete_link}' onclick=\"return confirm('Mark this request as complete?');\">Complete</a>
            </td>
        </tr>";
    }
    echo '</table>';
    wp_reset_postdata();
}