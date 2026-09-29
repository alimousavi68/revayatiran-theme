<?php
/** Accessible source choices and authenticated taxonomy option discovery. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function revayat_source_catalog( string $section ): array {
    if ( class_exists( 'Revayat_Companion_Homepage_Query' ) ) { return Revayat_Companion_Homepage_Query::catalog( $section ); }
    $types = array();
    foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $name => $type ) {
        if ( 'attachment' !== $name && 'situation_room' !== $name ) { $types[ $name ] = $type->labels->singular_name; }
    }
    $taxonomies = array();
    foreach ( get_taxonomies( array( 'show_ui' => true ), 'objects' ) as $name => $taxonomy ) {
        $attached = array_values( array_intersect( $taxonomy->object_type, array_keys( $types ) ) );
        if ( $attached ) { $taxonomies[ $name ] = array( 'label' => $taxonomy->labels->singular_name, 'types' => $attached, 'hierarchical' => $taxonomy->hierarchical ); }
    }
    return array( 'types' => $types, 'taxonomies' => $taxonomies );
}

function revayat_default_source( string $section ): string {
    if ( class_exists( 'Revayat_Companion_Homepage_Query' ) ) { return Revayat_Companion_Homepage_Query::default_type( $section ); }
    return array( 'hero' => 'post', 'daily-narrative' => 'post', 'news-monitoring' => 'post', 'situation-room' => 'situation_room', 'special-dossiers' => 'special_dossier', 'media-observatory' => 'media_observatory', 'analysts-network' => 'analyst_post', 'multimedia' => 'multimedia' )[ $section ] ?? 'post';
}

/** Reject malformed imported selections before coercion can silently broaden a query. */
function revayat_tax_filter_shape( $rule ): bool {
    if ( ! is_array( $rule ) || ! is_string( $rule['taxonomy'] ?? null ) ) { return false; }
    foreach ( array( 'include', 'exclude' ) as $key ) {
        $ids = $rule[ $key ] ?? array();
        if ( ! is_array( $ids ) || count( $ids ) > 500 || array_filter( $ids, static fn( $id ) => ! is_scalar( $id ) || ! ctype_digit( (string) $id ) || (int) $id < 1 ) ) { return false; }
    }
    return true;
}

function revayat_sanitize_tax_filters( $value, string $section ): string {
    $rules = is_string( $value ) ? json_decode( $value, true ) : null;
    if ( ! is_string( $value ) || '[' !== substr( ltrim( $value ), 0, 1 ) || ! is_array( $rules ) || array_values( $rules ) !== $rules || count( $rules ) > 12 ) { return '[]'; }
    $catalog = revayat_source_catalog( $section );
    $clean = array();
    foreach ( $rules as $rule ) {
        if ( ! revayat_tax_filter_shape( $rule ) || ! isset( $catalog['taxonomies'][ $rule['taxonomy'] ] ) ) { continue; }
        $ids = static fn( $key ) => array_slice( array_values( array_unique( array_filter( array_map( 'absint', (array) ( $rule[ $key ] ?? array() ) ) ) ) ), 0, 500 );
        $clean[] = array( 'taxonomy' => $rule['taxonomy'], 'include' => $ids( 'include' ), 'exclude' => $ids( 'exclude' ), 'operator' => 'AND' === ( $rule['operator'] ?? '' ) ? 'AND' : 'IN', 'children' => in_array( $rule['children'] ?? true, array( true, 1, '1' ), true ) );
    }
    return wp_json_encode( $clean, JSON_UNESCAPED_UNICODE );
}

function revayat_validate_tax_filters( $validity, $value, $setting ) {
    $section = substr( $setting->id, strlen( 'revayat_' ), -strlen( '_tax_filters' ) );
    $rules = is_string( $value ) ? json_decode( $value, true ) : null;
    $catalog = revayat_source_catalog( $section );
    if ( ! is_string( $value ) || '[' !== substr( ltrim( $value ), 0, 1 ) || ! is_array( $rules ) || array_values( $rules ) !== $rules || count( $rules ) > 12 ) {
        $validity->add( 'invalid_filters', 'ساختار فیلترها معتبر نیست؛ حداکثر ۱۲ فیلتر انتخاب کنید.' ); return $validity;
    }
    foreach ( $rules as $rule ) {
        if ( ! revayat_tax_filter_shape( $rule ) ) { $validity->add( 'invalid_filter', 'ساختار انتخاب‌ها معتبر نیست؛ در هر فهرست حداکثر ۵۰۰ مورد انتخاب کنید.' ); continue; }
        $taxonomy = $rule['taxonomy'];
        if ( ! isset( $catalog['taxonomies'][ $taxonomy ] ) ) {
            $validity->add( 'invalid_taxonomy', 'تاکسونومی انتخاب‌شده دیگر موجود نیست؛ فیلتر را اصلاح کنید.' ); continue;
        }
        $ids = array_values( array_unique( array_merge( array_map( 'absint', (array) ( $rule['include'] ?? array() ) ), array_map( 'absint', (array) ( $rule['exclude'] ?? array() ) ) ) ) );
        if ( count( $ids ) > 1000 ) { $validity->add( 'too_many_terms', 'تعداد انتخاب‌ها بیش از حد مجاز است.' ); continue; }
        if ( $ids ) {
            $found = get_terms( array( 'taxonomy' => $taxonomy, 'include' => $ids, 'hide_empty' => false, 'fields' => 'ids' ) );
            if ( is_wp_error( $found ) || count( $found ) !== count( $ids ) ) { $validity->add( 'invalid_terms', 'بعضی گزینه‌ها حذف شده‌اند یا به این تاکسونومی تعلق ندارند؛ انتخاب‌ها را اصلاح کنید.' ); }
        }
    }
    return $validity;
}

function revayat_query_terms_ajax(): void {
    check_ajax_referer( 'revayat_customizer_terms', 'nonce' );
    if ( ! current_user_can( 'edit_theme_options' ) ) { wp_send_json_error( array( 'message' => 'دسترسی مجاز نیست.' ), 403 ); }
    $section = sanitize_key( wp_unslash( $_POST['section'] ?? '' ) );
    $taxonomy = sanitize_key( wp_unslash( $_POST['taxonomy'] ?? '' ) );
    $catalog = revayat_source_catalog( $section );
    if ( ! isset( revayat_home_sections()[ $section ], $catalog['taxonomies'][ $taxonomy ] ) ) { wp_send_json_error( array( 'message' => 'تاکسونومی معتبر نیست.' ), 400 ); }
    $page = max( 1, min( 10000, absint( $_POST['page'] ?? 1 ) ) );
    $search = sanitize_text_field( wp_unslash( $_POST['search'] ?? '' ) );
    $args = array( 'taxonomy' => $taxonomy, 'hide_empty' => false, 'search' => $search, 'number' => 41, 'offset' => ( $page - 1 ) * 40, 'orderby' => 'name', 'order' => 'ASC' );
    $terms = get_terms( $args );
    if ( is_wp_error( $terms ) ) { wp_send_json_error( array( 'message' => 'دریافت گزینه‌ها ناموفق بود.' ), 400 ); }
    $more = count( $terms ) > 40;
    $format = static fn( $term ) => array( 'id' => $term->term_id, 'name' => html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ), 'slug' => rawurldecode( $term->slug ), 'parent' => $term->parent );
    $selected = array_slice( array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['selected'] ?? '' ) ) ) ) ), 0, 500 );
    $selected_terms = $selected ? get_terms( array( 'taxonomy' => $taxonomy, 'include' => $selected, 'hide_empty' => false ) ) : array();
    wp_send_json_success( array( 'terms' => array_map( $format, array_slice( $terms, 0, 40 ) ), 'selected' => is_wp_error( $selected_terms ) ? array() : array_map( $format, $selected_terms ), 'more' => $more ) );
}
add_action( 'wp_ajax_revayat_customizer_terms', 'revayat_query_terms_ajax' );

/** Loaded at customize_register, after the native control base class exists. */
function revayat_register_query_control_class(): void {
    if ( class_exists( 'Revayat_Query_Control' ) ) { return; }
    class Revayat_Query_Control extends WP_Customize_Control {
        public function render_content() {
            ?>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <?php if ( $this->description ) : ?><span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span><?php endif; ?>
            <div class="rv-query-control" data-query-setting="<?php echo esc_attr( $this->id ); ?>"></div>
            <?php
        }
    }
}
