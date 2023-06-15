<?php
/**
 * Class CA_Export
 */
class CA_Exports{

    private $current_lng;

    private $posts;

    const CA_VERSION = '1.2';
    /**
     * Constructs new CA_Export instance
     */
    public function __construct( $args, $assoc_args ) {

        $this->posts          = !empty( $assoc_args['posts'] ) ? $assoc_args['posts'] : array();
        $this->categories     = !empty( $assoc_args['category'] ) ? $assoc_args['category'] : '';
        $this->post_tags      = !empty( $assoc_args['post_tag'] ) ? $assoc_args['post_tag'] : '';
        $this->custom_items   = !empty( $assoc_args['custom_items'] ) ? $assoc_args['custom_items'] : '';

        $this->ca_cache = new CA_Cache();

        $get_time     = time();

        $this->ca_filename = 'ca_export_posts-' . $get_time . '.xml';
    }
    /**
     * Wrap given string in XML CDATA tag.
     *
     * @since 1.0.0
     *
     * @param string $str String to wrap in XML CDATA tag.
     *
     * @return string
     */
    private function ca_cdata( $str ) {

        if ( ! seems_utf8( $str ) ) {
            $str = utf8_encode( $str );
        }
        // $str = ent2ncr(esc_html($str));
        $str = '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $str ) . ']]>';

        return $str;
    }
    /**
     * Return the URL of the site
     *
     * @since 1.0.0
     *
     * @return string Site URL.
     */
    private function ca_siteurl() {
        return get_bloginfo_rss( 'url' );
    }
    /**
     * Output a tag_name XML tag from a given tag object
     *
     * @since 1.0.0
     *
     * @param object $tag Tag Object
     */
    private function ca_tagname( $tag ) {

        if ( empty( $tag->name ) ) {
            return;
        }

        return '<wp:tag_name>' . $this->ca_cdata( $tag->name ) . '</wp:tag_name>';
    }
    /**
     * Output a tag_description XML tag from a given tag object
     *
     * @since 1.0.0
     *
     * @param object $tag Tag Object
     */
    private function ca_tag_description( $tag ) {

        if ( empty( $tag->description ) ) {
            return;
        }

        return '<wp:tag_description>' . $this->ca_cdata( $tag->description ) . '</wp:tag_description>';
    }
    /**
     * Output list of taxonomy terms, in XML tag format, associated with a post
     *
     * @since 1.0.0
     */
    function ca_post_category( $post_ID ) {

        $terms = wp_get_post_terms( $post_ID, 'category', array( 'fields' => 'all' ) );

        $categories = '';
        foreach ( (array) $terms as $term ) {
            $categories .= "\t\t\t<category domain=\"{$term->taxonomy}\" nicename=\"{$term->slug}\" term_id=\"$term->term_id\">" . $this->ca_cdata( $term->name ) . "</category>\n";
        }

        unset( $terms );

        return $categories;
    }

    public function ca_post_taxonomy( $post_ID ) {
        $post = get_post( $post_ID );

        $taxonomies = get_object_taxonomies( $post->post_type );
        if ( empty( $taxonomies ) ) {
            return;
        }

        $category = '';
        foreach ( $taxonomies as $taxonomy ) {

            $terms = wp_get_object_terms( $post_ID, $taxonomy );

            foreach ( (array) $terms as $term ) {
                $category .= "\t\t<category domain=\"{$term->taxonomy}\" nicename=\"{$term->slug}\">" . $this->ca_cdata( $term->name ) . "</category>\n";
            }
        }

        return $category;
    }
    /**
     * Output list of authors with posts
     *
     * @since 1.0.0
     *
     * @global wpdb $wpdb     WordPress database abstraction object.
     *
     * @param array $post_ids Array of post IDs to filter the query by. Optional.
     */
    private function ca_author_list( array $post_ids = null ) {

        global $wpdb;

        if ( ! empty( $post_ids ) ) {
            $post_ids = array_map( 'absint', $post_ids );
            $and      = 'AND ID IN ( ' . implode( ', ', $post_ids ) . ')';
        } else {
            $and = '';
        }

        $authors = array();
        $results = $wpdb->get_results( "SELECT DISTINCT post_author FROM $wpdb->posts WHERE post_status != 'auto-draft' $and" );
        foreach ( (array) $results as $result ) {
            $authors[] = get_userdata( $result->post_author );
        }

        $authors = array_filter( $authors );

        $ca_authors = false;

        foreach ( $authors as $author ) {
            foreach ( $author->roles as $role ) {
            }

            $ca_authors .= "\n\t\t<wp:author>\n";
            $ca_authors .= "\t\t\t<wp:author_id>" . intval( $author->ID ) . "</wp:author_id>\n";
            $ca_authors .= "\t\t\t<wp:author_login>" . $this->ca_cdata( $author->user_login ) . "</wp:author_login>\n";
            $ca_authors .= "\t\t\t<wp:author_email>" . $this->ca_cdata( $author->user_email ) . "</wp:author_email>\n";
            $ca_authors .= "\t\t\t<wp:author_display_name>" . $this->ca_cdata( $author->display_name ) . "</wp:author_display_name>\n";
            $ca_authors .= "\t\t\t<wp:author_first_name>" . $this->ca_cdata( $author->first_name ) . "</wp:author_first_name>\n";
            $ca_authors .= "\t\t\t<wp:author_last_name>" . $this->ca_cdata( $author->last_name ) . "</wp:author_last_name>\n";
            $ca_authors .= "\t\t\t<wp:author_role>" . $this->ca_cdata( $role ) . "</wp:author_role>\n";
            $ca_authors .= "\t\t</wp:author>\n";
        }

        unset( $authors );
        unset( $results );

        return $ca_authors;
    }

    /**
     * Get the requested terms ready, empty unless posts filtered by category
     * or all content.
     */
    private function ca_get_category() {

        $ca_categories = false;

        if ( property_exists( $this, 'categories' ) && ! empty( $this->categories ) ) {
            foreach ( $this->categories as $category ) {
                $ca_categories .= "\n\t\t<wp:category>\n";
                $ca_categories .= "\t\t\t<wp:term_id>" . intval( $category->term_id ) . "</wp:term_id>\n";
                $ca_categories .= "\t\t\t<wp:category_nicename>" . $this->ca_cdata( $category->slug ) . "</wp:category_nicename>\n";
                $ca_categories .= "\t\t\t<wp:category_parent>" . $this->ca_cdata( $category->parent ? $category->parent : '' ) . " </wp:category_parent>\n";
                $ca_categories .= "\t\t\t<wp:cat_name>" . $this->ca_cdata( $category->name ) . "</wp:cat_name>\n";
                $ca_categories .= "\t\t\t<wp:category_description>" . $this->ca_cdata( $category->description ) . "</wp:category_description>\n";
                $ca_categories .= "\t\t\t<wp:taxonomy>" . $this->ca_cdata( $category->taxonomy ) . "</wp:taxonomy>\n";
                $ca_categories .= "\t\t</wp:category>\n";
            }
        }

        if ( property_exists( $this, 'post_tags' ) && ! empty( $this->post_tags ) ) {
            foreach ( $this->post_tags as $post_tag ) {
                $ca_categories .= "\n\t\t<wp:tag>\n";
                $ca_categories .= "\t\t\t<wp:term_id>" . intval( $post_tag->term_id ) . "</wp:term_id>\n";
                $ca_categories .= "\t\t\t<wp:tag_slug>" . $this->ca_cdata( $post_tag->slug ) . "</wp:tag_slug >\n";
                $ca_categories .= "\t\t\t" . $this->ca_tagname( $post_tag ) . "\n";
                $ca_categories .= "\t\t\t" . $this->ca_tag_description( $post_tag ) . "\n";
                $ca_categories .= "\t\t</wp:tag>\n";
            }
        }

        return $ca_categories;
    }

    public function ca_term_description( $term ) {
        if ( empty( $term->description ) ) {
            return;
        }

        return "\t\t<wp:term_description>" . ca_cdata( $term->description ) . "</wp:term_description>\n";
    }

    public function ca_term_meta( $term ) {
        global $wpdb;

        $termmeta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->termmeta WHERE term_id = %d", $term->term_id ) );

        $term_meta = '';
        foreach ( $termmeta as $meta ) {
        
            if ( ! apply_filters( 'ca_export_skip_termmeta', false, $meta->meta_key, $meta ) ) {
                $term_meta .= sprintf( "\t\t<wp:termmeta>\n\t\t\t<wp:meta_key>%s</wp:meta_key>\n\t\t\t<wp:meta_value>%s</wp:meta_value>\n\t\t</wp:termmeta>\n", ca_cdata( $meta->meta_key ), ca_cdata( $meta->meta_value ) );
            }
        }

        return $term_meta;
    }

    private function ca_get_custom_terms() {

        $ca_custom_type = false;

        if ( property_exists( $this, 'custom_items' ) && ! empty( $this->custom_items ) ) {
            foreach ( $this->custom_items as $custom_type => $custom_items ) {
                foreach ( $custom_items as $custom_item ) {
                    $parent = $custom_item->parent ? $custom_items[ $custom_item->parent ]->slug : '';

                    $ca_custom_type .= "\n\t\t<wp:term>\n";
                    $ca_custom_type .= "\t\t\t<wp:term_id>" . intval( $custom_item->term_id ) . "</wp:term_id>\n";
                    $ca_custom_type .= "\t\t\t<wp:term_slug>" . $this->ca_cdata( $custom_item->slug ) . "</wp:term_slug>\n";
                    $ca_custom_type .= "\t\t\t<wp:term_parent>" . $this->ca_cdata( $parent ) . "</wp:term_parent>\n";
                    $ca_custom_type .= "\t\t\t<wp:term_name>" . $this->ca_cdata( $custom_item->name ) . "</wp:term_name>\n";
                    $ca_custom_type .= "\t\t\t<wp:term_taxonomy>" . $this->ca_cdata( $custom_item->taxonomy ) . "</wp:term_taxonomy>\n";
                    $ca_custom_type .= $this->ca_term_description( $custom_item );
                    $ca_custom_type .= $this->ca_term_meta( $custom_item );
                    $ca_custom_type .= "\t\t</wp:term>\n";
                }
            }
        }

        return $ca_custom_type;
    }
    /**
     * Filter whether to selectively skip post meta used for xml exports.
     *
     * Returning a truthy value to the filter will skip the current meta
     * object from being exported.
     *
     */
    private function ca_get_post_items() {

        global $wpdb;

        $ca_items = false;

        foreach ( $this->posts as $i => $post ) {

            $ca_items .= "\n\t\t<item>\n";
            $ca_items .= "\t\t\t<title>" . apply_filters( 'the_title_rss', $post->post_title ) . "</title>\n";
            $ca_items .= "\t\t\t<link>" . $this->ca_cdata( get_permalink( $post->ID ) ) . "</link>\n";
            $ca_items .= "\t\t\t<pubDate>" . $this->ca_cdata( $post->post_date ) . "</pubDate>\n";
            $ca_items .= "\t\t\t<dc:creator>" . $this->ca_cdata( get_the_author_meta( $post->post_author ) ) . "</dc:creator>\n";
            $ca_items .= "\t\t\t<guid isPermaLink=\"false\">" . $this->ca_cdata( get_the_guid( $post->ID ) ) . "</guid>\n";
            $ca_items .= "\t\t\t<excerpt:encoded>" . $this->ca_cdata( $post->post_excerpt ) . "</excerpt:encoded>\n";
            // $ca_items .= "\t\t\t<content:encoded>" . $this->ca_cdata( $post->post_content ) . "</content:encoded>\n";
            $ca_items .= "\t\t\t<wp:post_id>" . intval( $post->ID ) . "</wp:post_id>\n";
            $ca_items .= "\t\t\t<wp:post_date>" . $this->ca_cdata( $post->post_date ) . "</wp:post_date>\n";
            $ca_items .= "\t\t\t<wp:post_author>" . $this->ca_cdata( $post->post_author ) . "</wp:post_author>\n";
            $ca_items .= "\t\t\t<wp:post_date_gmt>" . $this->ca_cdata( $post->post_date_gmt ) . "</wp:post_date_gmt>\n";
            $ca_items .= "\t\t\t<wp:comment_status>" . $this->ca_cdata( $post->comment_status ) . "</wp:comment_status>\n";
            $ca_items .= "\t\t\t<wp:ping_status>" . $this->ca_cdata( $post->ping_status ) . "</wp:ping_status>\n";
            $ca_items .= "\t\t\t<wp:post_name>" . $this->ca_cdata( $post->post_name ) . "</wp:post_name>\n";
            $ca_items .= "\t\t\t<wp:status>" . $this->ca_cdata( $post->post_status ) . "</wp:status>\n";
            $ca_items .= "\t\t\t<wp:post_parent>" . intval( $post->post_parent ) . "</wp:post_parent>\n";
            $ca_items .= "\t\t\t<wp:menu_order>" . intval( $post->menu_order ) . "</wp:menu_order>\n";
            $ca_items .= "\t\t\t<wp:post_type>" . $this->ca_cdata( $post->post_type ) . "</wp:post_type>\n";
            $ca_items .= "\t\t\t<wp:post_password>" . $this->ca_cdata( $post->post_password ) . "</wp:post_password>\n";
            $ca_items .= "\t\t\t<wp:is_sticky>" . intval( is_sticky( $post->ID ) ) . "</wp:is_sticky>\n";

            if ( $post->post_type == 'attachment' ) {
                $ca_items .= "\t\t\t<wp:attachment_url>" . $this->ca_cdata( wp_get_attachment_url( $post->ID ) ) . "</wp:attachment_url>\n";
            }

            $ca_items .= $this->ca_post_category( $post->ID );
            $ca_items .= $this->ca_post_taxonomy( $post->ID );
            $ca_items .= $this->ca_get_postmeta( $post );
            $ca_items .= $this->ca_get_translation( $post->translations );
            $ca_items .= $this->ca_comments( $post->ID );

            $ca_items .= "\n\t\t</item>\n";
        }

        return $ca_items;
    }
    /**
     * Filter whether to selectively skip post meta used for CA exports.
     *
     * Returning a truthy value to the filter will skip the current meta
     * object from being exported.
     *
     */
    private function ca_get_postmeta( $post ) {

        global $wpdb;

        $postmeta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d", $post->ID ) );

        $ca_postmeta = false;

        foreach ( $postmeta as $meta ) {
            $ca_postmeta .= "\n\t\t\t<wp:postmeta>\n";
            $ca_postmeta .= "\t\t\t\t<wp:meta_key>" . $this->ca_cdata( $meta->meta_key ) . "</wp:meta_key>\n";
            $ca_postmeta .= "\t\t\t\t<wp:meta_value>" . $this->ca_cdata( $meta->meta_value ) . "</wp:meta_value>\n";
            $ca_postmeta .= "\t\t\t</wp:postmeta>\n";
        }

        $post_author_id = ! empty( $post->post_author ) ? $post->post_author : 0;
        $author_obj     = get_user_by( 'id', $post_author_id );
        $user_login     = ! empty( $author_obj->user_login ) ? $author_obj->user_login : '';
        $user_email     = ! empty( $author_obj->user_email ) ? $author_obj->user_email : '';

        $ca_postmeta .= "\n\t\t\t<wp:postmeta>\n";
        $ca_postmeta .= "\t\t\t\t<wp:meta_key>" . $this->ca_cdata( '_post_author_slug' ) . "</wp:meta_key>\n";
        $ca_postmeta .= "\t\t\t\t<wp:meta_value>" . $this->ca_cdata( $user_login ) . "</wp:meta_value>\n";
        $ca_postmeta .= "\t\t\t</wp:postmeta>\n";

        $ca_postmeta .= "\n\t\t\t<wp:postmeta>\n";
        $ca_postmeta .= "\t\t\t\t<wp:meta_key>" . $this->ca_cdata( '_post_author_email' ) . "</wp:meta_key>\n";
        $ca_postmeta .= "\t\t\t\t<wp:meta_value>" . $this->ca_cdata( $user_email ) . "</wp:meta_value>\n";
        $ca_postmeta .= "\t\t\t</wp:postmeta>\n";

        return apply_filters( 'ca_xml_postmeta', $ca_postmeta, $post );
    }

    private function ca_get_translation( $post_translations ) {

        $ca_post_translations = false;

        if ( ! empty( $post_translations ) ) {
            foreach ( $post_translations as $lng => $post_id ) {
                $ca_post_translations .= "\n\t\t\t<wp:translation>\n";
                $ca_post_translations .= "\t\t\t\t<wp:locale>" . $this->ca_cdata( $lng ) . "</wp:locale>\n";
                $ca_post_translations .= "\t\t\t\t<wp:element_id>" . intval( $post_id ) . "</wp:element_id>\n";
                $ca_post_translations .= "\t\t\t</wp:translation>\n";
            }
        }

        return $ca_post_translations;
    }

    private function ca_comments( $post_id ) {

        global $wpdb;

        $_comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = 1", $post_id ) );

        $comments = array_map( 'get_comment', $_comments );

        $ca_post_comments = false;

        foreach ( $comments as $comment ) {
            $ca_post_comments .= "\n\t\t\t<wp:comment>\n";

            foreach ( $comment as $ca_tag => $ca_data ) {
                if ( $ca_tag == 'comment_id' || $ca_tag == 'comment_parent' || $ca_tag == 'comment_user_id' ) {
                    $ca_data = intval( $ca_data );
                } else {
                    $ca_data = $this->ca_cdata( $ca_data );
                }

                $ca_post_comments .= "\t\t\t\t<wp:" . $ca_tag . '>' . $ca_data . '</wp:' . $ca_tag . ">\n";
            }

            $ca_post_comments .= "\t\t\t</wp:comment>\n";
        }

        return $ca_post_comments;
    }


    private function ca_get_header() {
        $this->current_locale = 'ea-ca';
        $ca_version   = self::CA_VERSION;
        $ca_siteurl  = $this->ca_siteurl();
        $ca_generator = get_the_generator( 'export' );

        $time             = date( 'D, d M Y H:i:s +0000' );
        $blog_name        = get_bloginfo_rss( 'name' );
        $blog_url         = get_bloginfo_rss( 'url' );
        $blog_description = get_bloginfo_rss( 'description' );
        $blog_charset     = get_bloginfo( 'charset' );

        return <<<EOF
<?xml version="1.0" encoding="{$blog_charset}" ?>
{$ca_generator}
<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/{$ca_version}/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/{$ca_version}/"
	         >
	<channel>
		<title>{$blog_name}</title>
		<link>{$blog_url}</link>
		<description>{$blog_description}</description>
		<pubDate>{$time}</pubDate>
		<language>{$this->current_locale}</language>
		<wp:wxr_version>{$ca_version}</wp:wxr_version>
		<wp:base_site_url>{$ca_siteurl}</wp:base_site_url>
		<wp:base_blog_url>{$blog_url}</wp:base_blog_url>
EOF;
    }

    private function ca_footer() {

        return <<<EOF

		</channel>
</rss>
EOF;
    }
    /**
     * create xml string
     *
     * @param  posts with all relation
     * return string xml
     */
    public function get_ca() {

        if( empty( $this->posts ) ) {
            return '';
        }

        $post_ids = [];
        foreach ( $this->posts as $post ) {
            $post_ids[] = $post->ID;
        }

        $this->ca_cache->unlink_ca( $this->ca_filename );

        $this->ca_cache->write( $this->ca_get_header(), $this->ca_filename );
        $this->ca_cache->write( $this->ca_author_list( $post_ids ), $this->ca_filename );
        $this->ca_cache->write( $this->ca_get_category(), $this->ca_filename );
        $this->ca_cache->write( $this->ca_get_custom_terms(), $this->ca_filename );
        $this->ca_cache->write( $this->ca_get_post_items(), $this->ca_filename );

        $this->ca_cache->write( $this->ca_footer(), $this->ca_filename, false );

        unset( $ca_items );

        $ca = $this->ca_cache->get_ca_stack();

        return $ca;
    }
}
