<?php


function divichild_enqueue_scripts() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/js/scripts.js', array( 'jquery' ));
}
add_action( 'wp_enqueue_scripts', 'divichild_enqueue_scripts' );


//you can add custom functions below this line:



add_shortcode( 'resource_categories', 'ds_resource_categories' );

function ds_resource_categories() {
    $params = [
        'limit' => -1,
    ];

    $resource_categories = pods( 'resource_category', $params );

    if( $resource_categories->fetch() ) {



        $content = '<div class="grid grid--resources">';
        while( $resource_categories->fetch() ) {
            $term_id = $resource_categories->display( 'term_id' );
            $term_name = $resource_categories->display( 'name' );
            $term_slug = $resource_categories->display( 'slug' );
            $term_image = get_stylesheet_directory_uri() . '/images/plus.png';
            if( !empty($resource_categories->display( 'featured_image' )) ) {
                $term_image = $resource_categories->display( 'featured_image' );
            }
            $featured_image = "<img src='{$term_image}' class='grid-item__image grid-item__image--resources grid-item__image--{$term_id}'>";

            // var_dump($resource_categories->fetch());
            $content .= '<a class="grid-item grid-item--resources grid-item--' . $term_id . '" href="' . get_the_permalink( 239717 ) . '?_sft_resource_category=' . $term_slug . '">';

                $content .= '<div class="grid-item__content grid-item__content--resources grid-item__content--' . $term_id . '">';
                    if( $featured_image ) {
                        $content .= $featured_image;
                    }
                    $content .= '<h2 class="grid-item__header grid-item__header--resources grid-item__header--' . $term_id . '">' . $term_name . '</h2>';

                $content .= '</div>';

            $content .= '</a>';

            unset($featured_image);

        }
        $content .= '</div>';
        return $content;
    }
}
