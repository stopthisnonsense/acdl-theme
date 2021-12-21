<?php


function divichild_enqueue_scripts() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/js/scripts.js', array( 'jquery' ));
}
add_action( 'wp_enqueue_scripts', 'divichild_enqueue_scripts' );


//you can add custom functions below this line:



add_shortcode( 'resource_categories', 'ds_resource_categories' );

function ds_resource_categories( $atts ) {
    $atts = shortcode_atts(
        [
            'ids' => [ 24,25 ],
            'include' => true,
            'blog' => true,
            'depth' => 1,
        ], $atts, 'ds_resource_categories'
    );
    $ids_to_target = $atts['ids'];
    $include_exclude = 'exclude';
    if( filter_var($atts['include'], FILTER_VALIDATE_BOOLEAN) ) {
        $include_exclude = 'include';
    }
    $params = [
        'limit' => -1,
    ];
    $ds_terms = get_terms( [
        'taxonomy' => 'resource_category',
        'parent' => 0,
        'fields' => 'ids',
        $include_exclude => $ids_to_target,
    ] );
    // var_dump( $ds_terms );

    $resource_categories = pods( 'resource_category', $params );
    if( $resource_categories->fetch() ) {
        $content = '<div class="grid grid--resources">';
        while( $resource_categories->fetch() ) {
            if( in_array( $resource_categories->display( 'term_id' ), $ds_terms )  ) {

                $term_children = get_term_children( $resource_categories->display( 'term_id' ),  $resource_categories->display( 'taxonomy' ) );

                $term_id = $resource_categories->display( 'term_id' );
                $term_name = $resource_categories->display( 'name' );
                $term_slug = $resource_categories->display( 'slug' );
                $term_image = get_stylesheet_directory_uri() . '/images/plus.png';
                if( !empty($resource_categories->display( 'featured_image' )) ) {
                    $term_image = $resource_categories->display( 'featured_image' );
                }
                // $featured_image = "<img src='{$term_image}' class='grid-item__image grid-item__image--resources grid-item__image--{$term_id}'>";

                // var_dump($resource_categories->fetch());
                $term_item_link = '#' . $term_slug;
                if( 0 < $atts['depth'] ) {
                    $term_item_link = get_term_link( $term_id, 'resource_category' );
                }

                $content .= '<div class="grid-item grid-item--resources grid-item--' . $term_id . '">';
                    $content .= '<a class="grid-item__content grid-item__content--resources grid-item__content--' . $term_id . '" href="' . $term_item_link . '">';

                        if( $featured_image ) {
                            $content .= $featured_image;
                        }
                        $content .= '<h2 class="grid-item__header grid-item__header--resources grid-item__header--' . $term_id . '">' . $term_name . '</h2>';

                    $content .= '</a>';

                    unset($featured_image);
                    if( 0 < $atts[ 'depth' ] && is_iterable($term_children) ) {
                        $child_term_template = "
                    <div class='grid-item__content grid-item__content--resources grid-item__content--{$term_id}'>
                    <h3 class='grid-item__subheader grid-item__subheader--{$term_id}'>{$term_name} Subcategories</h3>";
                    foreach( $term_children as $term_child ) {
                        $term_child_data = get_term_by( 'id', $term_child, $resource_categories->display( 'taxonomy' ) );
                        // var_dump( $term_child_data );

                        $term_child_name = $term_child_data->name;
                        $term_child_id = $term_child_data->term_id;
                        $term_child_slug = $term_child_data->slug;
                        $term_child_link = '#' . $term_child_slug;
                        if( 0 < $atts['depth'] ) {
                            $term_child_link = get_term_link( $term_child_id, $resource_categories->display( 'taxonomy' ) );
                        }

                        $child_term_template .= "<a class='grid-item__link grid-item__link--{$term_child_slug}' href='{$term_child_link}'>{$term_child_name}</a>";

                    }
                    $child_term_template .= "</div>";

                    $content .= $child_term_template;
                    }

                $content .= "</div>";

            }
        }
        if( filter_var($atts['blog'], FILTER_VALIDATE_BOOLEAN) ) {
            $content .= categories_card();
        }



        $content .= '</div>';
        return $content;
    }
}

function categories_card() {
    $term_children = get_categories();
    $content .= '<div class="grid-item grid-item--resources grid-item--blog">';
                    $content .= '<a class="grid-item__content grid-item__content--resources grid-item__content--blog" href="' . get_post_type_archive_link( 'post' ) . '">';
                        $content .= '<h2 class="grid-item__header grid-item__header--resources grid-item__header--blog">Blog</h2>';
                    $content .= '</a>';

                    $child_term_template = "
                    <div class='grid-item__content grid-item__content--resources grid-item__content--{$term_id}'>
                    <h3 class='grid-item__subheader grid-item__subheader--{$term_id}'>{$term_name} Subcategories</h3>";
                    foreach( $term_children as $term_child ) {
                        // var_dump($term_child);
                        $term_child_data = $term_child;
                        // var_dump( $term_child_data );

                        $term_child_name = $term_child_data->name;
                        $term_child_id = $term_child_data->term_id;
                        $term_child_slug = $term_child_data->slug;
                        $term_child_link = get_term_link( $term_child_id, 'category' );
                        $child_term_template .= "<a class='grid-item__link grid-item__link--{$term_child_slug}' href='{$term_child_link}'>{$term_child_name}</a>";

                    }
                    $child_term_template .= "</div>";

                    $content .= $child_term_template;
                $content .= "</div>";
    return $content;
}

function resource_categories_posts( $query ) {
    if( $query->is_tax( 'resource-category' ) ) {
        $query->set( 'post_type', [ 'post', 'resource' ] );
        $query->set( 'tax_query', [
            [
                'taxonomy' => 'resource-category',
                'field' => 'slug',
                'terms' => get_queried_object()->slug,
            ],
        ] );
    }
}

add_action( 'pre_get_posts', 'resource_categories_posts' );


function ds_category_archive() {
    $category_query = get_queried_object();
    $category_parent_slug = $category_query->slug;
    if( $category_query->post_type == 'page' ) {
        $category_queries = get_terms( 'resource_category', [
            'hide_empty' => false,
            'exclude' => [24, 25]
        ] );
        // var_dump( $category_query );
    }
        $category_pagination = "<div class='pagination pagination--search-filter'>" . get_the_posts_pagination() . "</div>";

        // if(  )
        $templates[] = "<div class='category-parent category-parent--{$category_parent_slug}'>
        {$category_pagination}
        </div>";
    if( is_iterable( $category_queries ) ) {
        foreach( $category_queries as $category_query ) {
            $templates[] = category_constructor( $category_query );
            // var_dump(category_constructor( $category_query ));
        }
    } else {
        $templates[] = category_constructor( $category_query );
    }

    if( have_posts() && get_post_type() != 'page' ) {
        $category_posts_template = "<div class='category-child-posts category-child-posts--{$category_parent_slug}'>";

            while( have_posts() ) {

                the_post();
                $category_post_link = get_the_permalink();

                $category_post_title = get_the_title();
                $category_post_excerpt = wpautop( get_the_excerpt() );
                $category_post_date = get_the_date();

                $category_post_pods = pods( 'resource', get_the_ID() );

                $viewable = 'View Content';
                if( $category_post_pods->display( 'file' ) && $category_post_pods->display('download_only') == true) {
                    $category_post_link = $category_post_pods->display( 'file' );
                    $viewable = 'Download File';
                }
                // var_dump($category_post_link);
                $category_post_terms = get_the_terms( get_the_ID(), 'resource_category' );
                if( is_iterable($category_post_terms) ) {
                    $term_names = join(', ', array_unique(wp_list_pluck($category_post_terms, 'name')));
                } else {
                    $term_names = 'Uncategorized';
                }

                $post_type = ucwords(get_post_type( get_the_ID() ));


                $category_posts_template .= "<a class='category-child-post category-child-post--{$category_parent_slug}' href='{$category_post_link}'>
                    <h3 class='category-child-post__title category-child-post__title--{$category_parent_slug}'>{$category_post_title}</h3>
                    <p class='category-child-post__date category-child-post__date--{$category_parent_slug}'>{$category_post_date}</p>
                    <div class='category-child-post__description category-child-post__description--{$category_parent_slug}'>{$category_post_excerpt}</div>
                    <p class='category-child-post__type category-child-post__type--{$category_parent_slug}'>{$post_type} Topics: {$term_names} </p>

                    <p class='category-child-post__download category-child-post__download--{$category_parent_slug}'> {$viewable}</p>
                </a>";

            } ?>
    <?php
        $category_posts_template .= $category_pagination;
        $category_posts_template .= "</div>";
        $templates[] = "<h2>All {$category_name} Resources</h2>
        <div class='category-parent category-parent--{$category_parent_slug}'>{$category_posts_template}</div>";
    }
    if( !empty( $templates ) ) {
        $templates = implode("\n", $templates);
    }
    return $templates;
}

add_shortcode( 'category_archive', 'ds_category_archive' );

function category_constructor( $category_query ) {
    if( !empty($category_query) ) {
        // var_dump($category_query);
        $category_parent = $category_query->taxonomy;
        $category_id = $category_query->term_id;
        $category_name = $category_query->name;

        $category_parent_slug = $category_query->slug;
        $category_parent_description = wpautop( $category_query->description );

        $category_children = get_term_children( $category_id, $category_parent );
        // var_dump( $category_children );


        if( is_array($category_children) && !empty($category_children) ) {
            // var_dump($category_children);
            if( get_post_type() != 'page' ) {
                $category_name = "$category_name Subcategories";
            }
            $templates = "<div class='subcategories subcategories--{$category_parent_slug}'>
            <h2 class='subcategory__title subcategory__title--{$category_parent_slug}' id='$category_parent_slug'> $category_name</h2>";

            foreach($category_children as $category_child) {

                $category_child_data = get_term_by( 'id', $category_child, $category_parent );
                // var_dump($category_child_data);

                $category_child_title = $category_child_data->name;
                $category_child_slug = $category_child_data->slug;
                $category_child_description = $category_child_data->description;
                $category_child_description = wpautop( $category_child_description );



                $category_child_link = get_term_link( $category_child_data->term_id, 'resource_category' );

                $category_child_template_title = "<h3 class='category-child__title category-child__title--{$category_child_slug}' id='$category_child_slug'>{$category_child_title}</h3>";

                $category_child_template_description = "<div class='category-child__description category-child__description--{$category_child_slug}'>{$category_child_description}</div>";

                if( get_post_type() != 'page' ) {
                    $category_child_template_link = "<a class='category-child__link category-child__link--{$category_child_slug}' href='{$category_child_link}'> See more {$category_child_title}</a>";
                }



                $child_args = [
                    'posts_per_page' => '3',
                    'post_type' => [ 'post', 'resource' ],
                    'tax_query' => [
                        [
                            'taxonomy' => $category_parent,
                            'field' => 'slug',
                            'terms' => $category_child_slug,
                        ],
                    ],
                 ];
                 if( get_post_type() == 'page' ) {
                    $child_args['posts_per_page'] = -1;
                    }
                $category_child_posts = get_posts( $child_args );
                // var_dump($category_child_posts);
                $template = "<div class='category-child category-child--{$category_child_slug}'>";



                    if( is_array($category_child_posts) && !empty($category_child_posts) ) {
                        $template .= "{$category_child_template_title}
                    {$category_child_template_description}";
                        $template .= "<div class='category-child-posts category-child-posts--{$category_child_slug}'>";
                            foreach($category_child_posts as $category_child_post) {
                                // setup_postdata( $category_child_post );
                                // var_dump( $category_child_post );
                                $category_child_post_title = get_the_title($category_child_post->ID);
                                $category_child_post_excerpt = get_the_excerpt($category_child_post->ID);
                                $category_child_pods = pods( 'resource', $category_child_post->ID );
                                // var_dump( $category_child_pods );
                                $category_child_post_link = get_the_permalink($category_child_post->ID);

                                $category_child_post_date = get_the_date( 'M d, Y', $category_child_post->ID);

                                $viewable = 'View Content';
                                if( $category_child_pods->display( 'file' ) && $category_child_pods->display('download_only') == true) {
                                    $category_child_post_link = $category_child_pods->display( 'file' );
                                    $viewable = 'Download File';
                                }

                                $category_child_post_terms = get_the_terms( $category_child_post->ID, 'resource_category' );
                                // var_dump( $category_child_post_terms);
                                if( is_iterable($category_child_post_terms) ) {
                                    $term_names = join(', ', array_unique(wp_list_pluck($category_child_post_terms, 'name')));
                                } else {
                                    $term_names = 'Uncategorized';
                                }

                                $post_type = ucwords(get_post_type( $category_child_post->ID ));

                                $template .= "<a class='category-child-post category-child-post--{$category_child_slug}' href='{$category_child_post_link}'>

                                    <h4 class='category-child-post__title category-child-post__title--{$category_child_slug}'>{$category_child_post_title}</h4>

                                    <p class='category-child-post__date category-child-post__date--{$category_child_slug}'><small>Published: {$category_child_post_date}</small></p>

                                    <div class='category-child-post__description category-child-post__description--{$category_child_slug}'>{$category_child_post_excerpt}</div>

                                    <p class='category-child-post__type category-child-post__type--{$category_child_slug}'>{$post_type} Topics: {$term_names} </p>

                                    <p class='category-child-post__download category-child-post__download--{$category_child_slug}'> {$viewable}</p>
                                </a>";
                                // wp_reset_postdata()
                            }
                            $template .= $category_child_template_link;
                        $template .= "</div>";

                    }


                $template .= "</div>";

                $templates .= $template;
                unset($template);
            }
            $templates .= "</div>";
        }
        // var_dump($templates);


        return $templates;
    }
}