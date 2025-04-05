<?php
/*
Plugin Name: Fahrschulbewertungen
Description: Bewertungen welche von Fahrschülern erstellt werden können und vom Fahrlehrer mit einem Bild beantwortet werden.
Version: 1.0
Requires at least: 5.2
Requires PHP:      7.2
Author: Flonik
Author URI: https://flonik.de
License: GPL v2 or later 
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: fahrschul_bewertungen
*/

// Create Table

global $wpdb;
$table_name = $wpdb->prefix . 'fahrschul_bewertungen';
$wpdb->query( "CREATE TABLE IF NOT EXISTS $table_name (
    id INT(11) NOT NULL AUTO_INCREMENT,
    vorname VARCHAR(50) NOT NULL,
    nachname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    sichtbar VARCHAR(100) NOT NULL,
    antwort_auf VARCHAR(100) NOT NULL,
    bild_id VARCHAR(100) NOT NULL,
    nachricht TEXT,
    PRIMARY KEY  (id)
);" );

function mein_formular_verarbeiten() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'fahrschul_bewertungen';
    if ( isset( $_POST['submit'] ) ) {
        $vorname   = $_POST['vorname'] ?? '';
        $key       = $_POST['key'] ?? '';
        $nachname  = $_POST['nachname'] ?? '';
        $email     = $_POST['email'] ?? '';
        $nachricht = $_POST['nachricht'] ?? '';
        if ($key == 'fbctx13'){
            $wpdb->insert(
                $table_name,
                array(
                    'vorname'     => $vorname,
                    'nachname'    => $nachname,
                    'email'       => $email,
                    'sichtbar'    => 0,
                    'antwort_auf' => '',
                    'bild_id'     => 0,
                    'nachricht'   => $nachricht,
                )
            );
            if ( $wpdb->insert_id ) {
                echo '<div class="mein-formular-erfolg">Vielen Dank! Ihre Daten wurden erfolgreich gespeichert.</div>';
            }
        }
    }
}
//add_action( 'init', 'mein_formular_verarbeiten' );

function fahrschule_review_shortcode() {
    ob_start();
    global $wpdb;
    $table_name = $wpdb->prefix . 'fahrschul_bewertungen';
    $eintraege = $wpdb->get_results( "SELECT * FROM $table_name WHERE sichtbar = 1" );
    ?>
    <?php if ( $eintraege ) { ?>
        <div class="fs-out">
            <?php foreach ( $eintraege as $eintrag ) { 
                $antwort    = $eintrag->antwort_auf;
                $bild       = $eintrag->bild_id;
                $image_size = 'full'; // z.B. thumbnail, medium, large oder full
                $image      = wp_get_attachment_image( $bild, $image_size );
                ?>
                <div class="fs-box">
                    <span class="fs-name"><?php echo esc_html( $eintrag->vorname ); ?> <?php echo esc_html( $eintrag->nachname ); ?></span>
                    <span class="fs-nachricht"><?php echo esc_html( $eintrag->nachricht ); ?></span>
                    <?php 
                    if ( ! empty( $antwort ) ) {
                        echo '<div class="fs-fahrlehrer">';
                        echo '<span class="fs-lehrername">Ghostcar</span>';
                        echo '<span class="fs-nachricht">' . esc_html( $antwort ) . '</span>';
                        echo '<div class="fs-bild">' . $image . '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p>Es sind keine Einträge vorhanden.</p>
    <?php }
    return ob_get_clean();
}
add_shortcode( 'show_reviews', 'fahrschule_review_shortcode' );

function fahrschule__formular_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'fahrschul_bewertungen';
    if ( isset( $_POST['submit'] ) ) {
        $vorname   = $_POST['vorname'] ?? '';
        $key       = $_POST['key'] ?? '';
        $nachname  = $_POST['nachname'] ?? '';
        $email     = $_POST['email'] ?? '';
        $nachricht = $_POST['nachricht'] ?? '';
        if ($key == 'fbctx13'){
            $wpdb->insert(
                $table_name,
                array(
                    'vorname'     => $vorname,
                    'nachname'    => $nachname,
                    'email'       => $email,
                    'sichtbar'    => 0,
                    'antwort_auf' => '',
                    'nachricht'   => $nachricht,
                )
            );
            if ( $wpdb->insert_id ) {
                echo '<div class="mein-formular-erfolg" autofocus>Vielen Dank! Ihre Daten wurden erfolgreich gespeichert.</div>';
            }
        }
    }
    ob_start();
    ?>
    <form method="post">
        <div class="fs-form">
            <div class="fb-col-30">
                <label for="vorname">Vorname:</label><br>
                <input type="text" name="vorname" id="vorname">
                <input type="hidden" name="key" id="key" value="fbctx13">
            </div>
            <div class="fb-col-30">
                <label for="nachname">Nachname:</label><br>
                <input type="text" name="nachname" id="nachname">
            </div>
            <div class="fb-col-30">
                <label for="email">E-Mail:</label><br>
                <input type="email" name="email" id="email">
            </div>
            <div class="fb-col-100">
                <label for="nachricht">Nachricht:</label>
                <textarea name="nachricht" id="nachricht"></textarea>
            </div>
            <input type="submit" name="submit" class="absenden" value="Absenden">
        </div>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'fahrschule_review', 'fahrschule__formular_shortcode' );

function mein_formular_admin_menu() {
    add_menu_page( 'Bewertungen', 'Bewertungen', 'manage_options', 'bewertungen', 'fahrschule' );
}
add_action( 'admin_menu', 'mein_formular_admin_menu' );

function fahrschule() {
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
        .fahrschule-erfolg {
            margin: 1em 0;
            padding: 1em;
            background-color: #d9edf7;
            border: 1px solid #bce8f1;
            color: #31708f;
        }
        .fahrschule-fehler {
            margin: 1em 0;
            padding: 1em;
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
        }
        .absenden {
            background-color: #3481B8;
            color: white;
            border: none;
            padding: 5px 10px;
            font-size: 0.8em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .absenden:hover {
            background-color: #1088F8;
        }
        .button {
            text-decoration: none;
            margin-right: 5px;
        }
    </style>
    <div class="wrap">
        <h2>Fahrschulbewertungen</h2>
        <?php
        // Hole und sichere GET-Parameter:
        $step         = $_GET['step'] ?? '';
        $id           = $_GET['id'] ?? '';
        $action       = $_GET['action'] ?? '';
        $func         = $_POST['func'] ?? '';
        $func_id      = $_POST['id'] ?? '';
        $antwort_auf  = $_POST['antwort_auf'] ?? '';
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'fahrschul_bewertungen';
        $message    = ''; // Variable initialisieren

        // Lösch-Funktion: Wird ausgeführt, wenn im URL-Parameter action=delete gesetzt ist
        if ( 'delete' === $action && ! empty( $id ) ) {
            if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'fahrschule_delete_entry_' . $id ) ) {
                $wpdb->delete( $table_name, array( 'id' => intval( $id ) ), array( '%d' ) );
                $message = '<div class="fahrschule-erfolg">Eintrag wurde erfolgreich gelöscht.</div>';
            } else {
                $message = '<div class="fahrschule-fehler">Sicherheitsüberprüfung fehlgeschlagen.</div>';
            }
        }
        
        if ( isset( $_POST['submit'] ) && isset( $_POST['fahrschule_admin_key'] ) && wp_verify_nonce( $_POST['fahrschule_admin_key'], 'fahrschule_admin_nonce' ) ) {
            $wpdb->update(
                $table_name,
                array(
                    'vorname'   => $_POST['vorname'] ?? '',
                    'nachname'  => $_POST['nachname'] ?? '',
                    'email'     => $_POST['email'] ?? '',
                    'nachricht' => $_POST['nachricht'] ?? '',
                    'sichtbar'  => $_POST['sichtbar'] ?? 0,
                ),
                array( 'id' => $_POST['id'] ?? 0 )
            );
            $message = '<div class="fahrschule-erfolg">Die Einträge wurden erfolgreich aktualisiert.</div>';
        }
        echo $message;
        
        if ( empty( $step ) ) {
            ?>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vorname</th>
                        <th>Nachname</th>
                        <th>E-Mail</th>
                        <th>Nachricht</th>
                        <th>Sichtbar</th>
                        <!--<th>Antwort auf</th>-->
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $wpdb->get_results( "SELECT * FROM $table_name" ) as $eintrag ) { ?>
                        <tr>
                            <form method="post">
                                <input type="hidden" name="fahrschule_admin_key" value="<?php echo wp_create_nonce( 'fahrschule_admin_nonce' ); ?>">
                                <input type="hidden" name="id" value="<?php echo esc_attr( $eintrag->id ); ?>">
                                <td><?php echo esc_html( $eintrag->id ); ?></td>
                                <td><input type="text" name="vorname" value="<?php echo esc_attr( $eintrag->vorname ); ?>"></td>
                                <td><input type="text" name="nachname" value="<?php echo esc_attr( $eintrag->nachname ); ?>"></td>
                                <td><input type="email" name="email" class="txt-area" value="<?php echo esc_attr( $eintrag->email ); ?>"></td>
                                <td><textarea name="nachricht" class="txt-area"><?php echo esc_html( $eintrag->nachricht ); ?></textarea></td>
                                <td><input type="checkbox" name="sichtbar" value="1" <?php checked( $eintrag->sichtbar, 1 ); ?>></td>
                                <!--<td><input type="number" name="antwort_auf" value="<?php echo esc_attr( $eintrag->antwort_auf ); ?>"></td>-->
                                <td>
                                    <input type="submit" name="submit" class="button" value="Aktualisieren">
                                    <a href="admin.php?page=bewertungen&step=antworten&id=<?php echo esc_attr( $eintrag->id ); ?>" class="button" data-eintrag-id="<?php echo esc_attr( $eintrag->id ); ?>">Antworten</a>
                                    <a href="<?php echo wp_nonce_url( 'admin.php?page=bewertungen&action=delete&id=' . esc_attr( $eintrag->id ), 'fahrschule_delete_entry_' . esc_attr( $eintrag->id ) ); ?>" class="button" onclick="return confirm('Möchten Sie diesen Eintrag wirklich löschen?');">Löschen</a>
                                </td>
                            </form>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { 
            if ( $func == 'antworten' ) {
                // Notwendige Dateien einbinden
                if ( ! function_exists( 'wp_handle_upload' ) ) {
                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                }
                if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
                    require_once( ABSPATH . 'wp-admin/includes/image.php' );
                }
                
                // Datei-Upload prüfen
                if ( ! empty( $_FILES['bild']['name'] ) ) {
                    $uploaded_file   = $_FILES['bild'];
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
                                'bild_id'     => $attach_id,
                                'antwort_auf' => $antwort_auf,
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
                            'antwort_auf' => $antwort_auf,
                        ),
                        array( 'id' => $_POST['id'] ?? 0 )
                    );
                }
            }
            
            $eintraege = $wpdb->get_results( "SELECT * FROM $table_name WHERE id = " . intval( $id ) );
            foreach ( $eintraege as $eintrag ) {
                $m_id            = $eintrag->id;
                $m_vorname       = $eintrag->vorname;
                $m_nachname      = $eintrag->nachname;
                $m_email         = $eintrag->email;
                $m_nachricht     = $eintrag->nachricht;
                $m_bild          = $eintrag->bild_id;
                $m_antwort_auf   = $eintrag->antwort_auf;
            }
            $image_size = 'thumbnail';
            $image      = wp_get_attachment_image( $m_bild, $image_size );
            ?>
            <a href="admin.php?page=bewertungen" class="button">Zurück</a>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="func" value="antworten">
                <input type="hidden" name="id" value="<?php echo esc_attr( $m_id ); ?>">
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label><?php echo esc_html( $m_vorname . ' ' . $m_nachname ); ?></label></th>
                            <td><?php echo esc_html( $m_nachricht ); ?></td>
                        </tr>
                        <tr>
                            <th></th>
                            <td><?php echo $image; ?></td>
                        </tr>
                        <tr>
                            <th><label for="bild">Bild</label></th>
                            <td>
                                <input type="file" name="bild" id="bild">
                            </td>
                        </tr>
                        <tr>
                            <th>Antwort</th>
                            <td>
                                <?php
                                $content   = $m_antwort_auf;
                                $editor_id = 'my_custom_editor';
                                $settings  = array( 'textarea_name' => 'antwort_auf' );
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

function fahrschule_admin_scripts() {
    wp_enqueue_script( 'fahrschule-admin', plugin_dir_url( __FILE__ ) . 'js/fahrschule-admin.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'fahrschule_admin_scripts' );

function fahrschule_antwort() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'fahrschul_bewertungen';
    $eintrag_id = $_POST['eintrag_id'] ?? 0;
    $antwort    = $_POST['antwort'] ?? '';
    $wpdb->update(
        $table_name,
        array( 'antwort' => $antwort ),
        array( 'id' => $eintrag_id )
    );
    wp_die();
}
add_action( 'wp_ajax_fahrschule_antwort', 'fahrschule_antwort' );

function fahrschule_admin_init() {
    add_action( 'wp_ajax_fahrschule_antwort', 'fahrschule_antwort' );
}
add_action( 'admin_init', 'fahrschule_admin_init' );
?>
