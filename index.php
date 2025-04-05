<?php
/*
Plugin Name: WP Reviews
Description: Reviews that can be created by users and responded to by administrators with an image.
Version: 1.0
Requires at least: 5.2
Requires PHP:      7.2
Author: Flonik
Author URI: https://flonik.de
License: GPL v2 or later 
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: wp_reviews
*/

// Create Table

global $wpdb;
$table_name = $wpdb->prefix . 'wp_reviews';
$wpdb->query( "CREATE TABLE IF NOT EXISTS $table_name (
    id INT(11) NOT NULL AUTO_INCREMENT,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    visible VARCHAR(100) NOT NULL,
    response_to VARCHAR(100) NOT NULL,
    image_id VARCHAR(100) NOT NULL,
    message TEXT,
    PRIMARY KEY  (id)
);" );

function process_review_form() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wp_reviews';
    if ( isset( $_POST['submit'] ) ) {
        $firstname = $_POST['firstname'] ?? '';
        $key       = $_POST['key'] ?? '';
        $lastname  = $_POST['lastname'] ?? '';
        $email     = $_POST['email'] ?? '';
        $message   = $_POST['message'] ?? '';
        if ($key == 'fbctx13'){
            $wpdb->insert(
                $table_name,
                array(
                    'firstname'   => $firstname,
                    'lastname'    => $lastname,
                    'email'       => $email,
                    'visible'     => 0,
                    'response_to' => '',
                    'image_id'    => 0,
                    'message'     => $message,
                )
            );
            if ( $wpdb->insert_id ) {
                echo '<div class="form-success">Thank you! Your data has been successfully saved.</div>';
            }
        }
    }
}

function review_shortcode() {
    ob_start();
    global $wpdb;
    $table_name = $wpdb->prefix . 'wp_reviews';
    $entries = $wpdb->get_results( "SELECT * FROM $table_name WHERE visible = 1" );
    ?>
    <?php if ( $entries ) { ?>
        <div class="review-output">
            <?php foreach ( $entries as $entry ) { 
                $response = $entry->response_to;
                $image    = $entry->image_id;
                $image_size = 'full';
                $image      = wp_get_attachment_image( $image, $image_size );
                ?>
                <div class="review-box">
                    <span class="review-name"><?php echo esc_html( $entry->firstname ); ?> <?php echo esc_html( $entry->lastname ); ?></span>
                    <span class="review-message"><?php echo esc_html( $entry->message ); ?></span>
                    <?php 
                    if ( ! empty( $response ) ) {
                        echo '<div class="review-admin">';
                        echo '<span class="admin-name">Admin</span>';
                        echo '<span class="review-message">' . esc_html( $response ) . '</span>';
                        echo '<div class="review-image">' . $image . '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p>No entries available.</p>
    <?php }
    return ob_get_clean();
}
add_shortcode( 'show_reviews', 'review_shortcode' );

function review_form_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wp_reviews';
    if ( isset( $_POST['submit'] ) ) {
        $firstname = $_POST['firstname'] ?? '';
        $key       = $_POST['key'] ?? '';
        $lastname  = $_POST['lastname'] ?? '';
        $email     = $_POST['email'] ?? '';
        $message   = $_POST['message'] ?? '';
        if ($key == 'fbctx13'){
            $wpdb->insert(
                $table_name,
                array(
                    'firstname'   => $firstname,
                    'lastname'    => $lastname,
                    'email'       => $email,
                    'visible'     => 0,
                    'response_to' => '',
                    'message'     => $message,
                )
            );
            if ( $wpdb->insert_id ) {
                echo '<div class="form-success" autofocus>Thank you! Your data has been successfully saved.</div>';
            }
        }
    }
    ob_start();
    ?>
    <form method="post">
        <div class="review-form">
            <div class="review-col-30">
                <label for="firstname">First Name:</label><br>
                <input type="text" name="firstname" id="firstname">
                <input type="hidden" name="key" id="key" value="fbctx13">
            </div>
            <div class="review-col-30">
                <label for="lastname">Last Name:</label><br>
                <input type="text" name="lastname" id="lastname">
            </div>
            <div class="review-col-30">
                <label for="email">Email:</label><br>
                <input type="email" name="email" id="email">
            </div>
            <div class="review-col-100">
                <label for="message">Message:</label>
                <textarea name="message" id="message"></textarea>
            </div>
            <input type="submit" name="submit" class="submit" value="Submit">
        </div>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'review_form', 'review_form_shortcode' );

function review_admin_menu() {
    add_menu_page( 'Reviews', 'Reviews', 'manage_options', 'reviews', 'review_admin' );
}
add_action( 'admin_menu', 'review_admin_menu' );

function review_admin() {
    ?>
    <style>
        .widefat th {
            font-weight: bold;
            background-color: #f7f7f7;
            border: 1px solid #eaeaea;
            text-align: left;
            padding: 0.5em;
        }
        .widefat td {
            border: 1px solid #eaeaea;
            padding: 0.5em;
        }
        .review-success {
            margin: 1em 0;
            padding: 1em;
            background-color: #d9edf7;
            border: 1px solid #bce8f1;
            color: #31708f;
        }
        .review-error {
            margin: 1em 0;
            padding: 1em;
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
        }
        .submit {
            background-color: #3481B8;
            color: white;
            border: none;
            padding: 5px 10px;
            font-size: 0.8em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .submit:hover {
            background-color: #1088F8;
        }
        .button {
            text-decoration: none;
            margin-right: 5px;
        }
    </style>
    <div class="wrap">
        <h2>Reviews</h2>
        <?php
        // Hole und sichere GET-Parameter:
        $step         = $_GET['step'] ?? '';
        $id           = $_GET['id'] ?? '';
        $action       = $_GET['action'] ?? '';
        $func         = $_POST['func'] ?? '';
        $func_id      = $_POST['id'] ?? '';
        $antwort_auf  = $_POST['antwort_auf'] ?? '';
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'wp_reviews';
        $message    = ''; // Variable initialisieren

        // Lösch-Funktion: Wird ausgeführt, wenn im URL-Parameter action=delete gesetzt ist
        if ( 'delete' === $action && ! empty( $id ) ) {
            if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'review_delete_entry_' . $id ) ) {
                $wpdb->delete( $table_name, array( 'id' => intval( $id ) ), array( '%d' ) );
                $message = '<div class="review-success">Eintrag wurde erfolgreich gelöscht.</div>';
            } else {
                $message = '<div class="review-error">Sicherheitsüberprüfung fehlgeschlagen.</div>';
            }
        }
        
        if ( isset( $_POST['submit'] ) && isset( $_POST['review_admin_key'] ) && wp_verify_nonce( $_POST['review_admin_key'], 'review_admin_nonce' ) ) {
            $wpdb->update(
                $table_name,
                array(
                    'firstname'   => $_POST['firstname'] ?? '',
                    'lastname'    => $_POST['lastname'] ?? '',
                    'email'        => $_POST['email'] ?? '',
                    'message'     => $_POST['message'] ?? '',
                    'visible'     => $_POST['visible'] ?? 0,
                ),
                array( 'id' => $_POST['id'] ?? 0 )
            );
            $message = '<div class="review-success">Die Einträge wurden erfolgreich aktualisiert.</div>';
        }
        echo $message;
        
        if ( empty( $step ) ) {
            ?>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Visible</th>
                        <!--<th>Response to</th>-->
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $wpdb->get_results( "SELECT * FROM $table_name" ) as $entry ) { ?>
                        <tr>
                            <form method="post">
                                <input type="hidden" name="review_admin_key" value="<?php echo wp_create_nonce( 'review_admin_nonce' ); ?>">
                                <input type="hidden" name="id" value="<?php echo esc_attr( $entry->id ); ?>">
                                <td><?php echo esc_html( $entry->id ); ?></td>
                                <td><input type="text" name="firstname" value="<?php echo esc_attr( $entry->firstname ); ?>"></td>
                                <td><input type="text" name="lastname" value="<?php echo esc_attr( $entry->lastname ); ?>"></td>
                                <td><input type="email" name="email" class="txt-area" value="<?php echo esc_attr( $entry->email ); ?>"></td>
                                <td><textarea name="message" class="txt-area"><?php echo esc_html( $entry->message ); ?></textarea></td>
                                <td><input type="checkbox" name="visible" value="1" <?php checked( $entry->visible, 1 ); ?>></td>
                                <!--<td><input type="number" name="response_to" value="<?php echo esc_attr( $entry->response_to ); ?>"></td>-->
                                <td>
                                    <input type="submit" name="submit" class="button" value="Aktualisieren">
                                    <a href="admin.php?page=reviews&step=respond&id=<?php echo esc_attr( $entry->id ); ?>" class="button" data-entry-id="<?php echo esc_attr( $entry->id ); ?>">Respond</a>
                                    <a href="<?php echo wp_nonce_url( 'admin.php?page=reviews&action=delete&id=' . esc_attr( $entry->id ), 'review_delete_entry_' . esc_attr( $entry->id ) ); ?>" class="button" onclick="return confirm('Möchten Sie diesen Eintrag wirklich löschen?');">Löschen</a>
                                </td>
                            </form>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { 
            if ( $func == 'respond' ) {
                // Notwendige Dateien einbinden
                if ( ! function_exists( 'wp_handle_upload' ) ) {
                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                }
                if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
                    require_once( ABSPATH . 'wp-admin/includes/image.php' );
                }
                
                // Datei-Upload prüfen
                if ( ! empty( $_FILES['image']['name'] ) ) {
                    $uploaded_file   = $_FILES['image'];
                    $upload_overrides = array( 'test_form' => false );
                    $movefile        = wp_handle_upload( $uploaded_file, $upload_overrides );
                    
                    if ( $movefile && ! isset( $movefile['error'] ) ) {
                        $filetype      = wp_check_filetype( basename( $movefile['file'] ), null );
                        $wp_upload_dir = wp_upload_dir();
                        
                        $attachment = array(
                            'guid'           => $wp_upload_dir['url'] . '/' . basename( $movefile['file'] ),
                            'post_mime_type' => $filetype['type'],
                            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $movefile['file'] ) ),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        );
                        
                        $attach_id   = wp_insert_attachment( $attachment, $movefile['file'] );
                        $attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
                        wp_update_attachment_metadata( $attach_id, $attach_data );
                        
                        $wpdb->update(
                            $table_name,
                            array(
                                'image_id'    => $attach_id,
                                'response_to' => $antwort_auf,
                            ),
                            array( 'id' => $_POST['id'] ?? 0 )
                        );
                    } else {
                        echo $movefile['error'];
                    }
                } else {
                    $wpdb->update(
                        $table_name,
                        array(
                            'response_to' => $antwort_auf,
                        ),
                        array( 'id' => $_POST['id'] ?? 0 )
                    );
                }
            }
            
            $entry = $wpdb->get_results( "SELECT * FROM $table_name WHERE id = " . intval( $id ) );
            foreach ( $entry as $eintrag ) {
                $m_id            = $eintrag->id;
                $m_firstname     = $eintrag->firstname;
                $m_lastname      = $eintrag->lastname;
                $m_email         = $eintrag->email;
                $m_message       = $eintrag->message;
                $m_image         = $eintrag->image_id;
                $m_response_to   = $eintrag->response_to;
            }
            $image_size = 'thumbnail';
            $image      = wp_get_attachment_image( $m_image, $image_size );
            ?>
            <a href="admin.php?page=reviews" class="button">Zurück</a>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="func" value="respond">
                <input type="hidden" name="id" value="<?php echo esc_attr( $m_id ); ?>">
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label><?php echo esc_html( $m_firstname . ' ' . $m_lastname ); ?></label></th>
                            <td><?php echo esc_html( $m_message ); ?></td>
                        </tr>
                        <tr>
                            <th></th>
                            <td><?php echo $image; ?></td>
                        </tr>
                        <tr>
                            <th><label for="image">Image</label></th>
                            <td>
                                <input type="file" name="image" id="image">
                            </td>
                        </tr>
                        <tr>
                            <th>Response</th>
                            <td>
                                <?php
                                $content   = $m_response_to;
                                $editor_id = 'my_custom_editor';
                                $settings  = array( 'textarea_name' => 'response_to' );
                                wp_editor( $content, $editor_id, $settings );
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="submit" class="button">Speichern</button>
            </form>
        <?php } ?>
    </div>
    <?php
}

function review_admin_scripts() {
    wp_enqueue_script( 'review-admin', plugin_dir_url( __FILE__ ) . 'js/review-admin.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'review_admin_scripts' );

function review_respond() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wp_reviews';
    $entry_id = $_POST['entry_id'] ?? 0;
    $response = $_POST['response'] ?? '';
    $wpdb->update(
        $table_name,
        array( 'response_to' => $response ),
        array( 'id' => $entry_id )
    );
    wp_die();
}
add_action( 'wp_ajax_review_respond', 'review_respond' );

function review_admin_init() {
    add_action( 'wp_ajax_review_respond', 'review_respond' );
}
add_action( 'admin_init', 'review_admin_init' );
?>
