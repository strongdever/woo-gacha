<?php

define('HOME', home_url('/'));  //oripa page
define('T_DIRE_URI', get_template_directory_uri()); //oripa page

// Astraテーマのプレークポイント変更
// スマホのブレークポイント
add_filter('astra_mobile_breakpoint', function () {
    return 767;
});

// タブレットのブレークポイント
add_filter('astra_tablet_breakpoint', function () {
    return 1440;
});

// style.cssを読み込む
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles', 999999);
function my_theme_enqueue_styles()
{
    wp_enqueue_style('child-style', get_stylesheet_uri());
}

// WooCommerceのボタンデザイン変更
function wp_enqueue_woocommerce_style(){
	//独自スタイルシートの登録
	wp_register_style( 'woocommerce-original-style', get_stylesheet_directory_uri() . '/assets/css/woocommerce.css' );
	//クラスの存在チェック
	if ( class_exists( 'woocommerce' ) ) {
		//読み込み
		wp_enqueue_style( 'woocommerce-original-style' );
	}
}
add_action( 'wp_enqueue_scripts', 'wp_enqueue_woocommerce_style' );

// カスタムjs読み込み
function add_link_files()
{
    wp_enqueue_script('reload-top', home_url() . '/wp-content/themes/astra-child/assets/js/script-top.js', true, false);
    // お問い合わせページでのみ読み込み
    if (is_page('contact')) {
        wp_enqueue_script('reload-contact', home_url() . '/wp-content/themes/astra-child/assets/js/script-contact.js', false, true);
    }
    if (is_page('toreka')) {
        wp_enqueue_script('reload-content-height', home_url() . '/wp-content/themes/astra-child/assets/js/script-content-height.js', false, true);
    }

    //----------------------------oripa page start---------------------->
    $current_url = $_SERVER['REQUEST_URI'];

    global $post;
    if (is_front_page() || (is_home() && is_page()) || is_page('toreka') || is_page('card-list') || strpos($current_url, 'cards_list') !== false || ($post->post_type == "gacha" && is_single())) { // oripa page
        wp_enqueue_style('c-oripa', home_url() . '/wp-content/themes/astra-child/assets/css/oripa.css', [], '1.0', 'all');
    }
    if (is_page('card-sending') || is_page('thanks')) { // oripa page
        wp_enqueue_style('c-contact', home_url() . '/wp-content/themes/astra-child/assets/css/oripa-contact.css', [], '1.0', 'all');
        // wp_enqueue_script('ajax-script', home_url() . '/wp-content/themes/astra-child/oripa-card-sending.php', array('jquery'), '1.0', true);  //oripa page
        // wp_localize_script('ajax-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php'))); //oripa page
    }
    if (is_front_page() || is_page('card-list') || is_page('card-sending') || strpos($current_url, 'cards_list') !== false || ($post->post_type == "gacha" && is_single())) { // oripa page
        wp_enqueue_script('s-jquery', home_url() . '/wp-content/themes/astra-child/assets/js/jquery.min.js', [], '1.0', false); //oripa page
        wp_enqueue_script('s-oripa', home_url() . '/wp-content/themes/astra-child/assets/js/oripa.js', true, false);  //oripa page

        wp_enqueue_script('ajax-script', home_url() . '/wp-content/themes/astra-child/assets/js/oripa.js', array('jquery'), '1.0', true);  //oripa page
        wp_localize_script('ajax-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php'))); //oripa page
    }
    //<----------------------------oripa page end----------------------
}
add_action('wp_enqueue_scripts', 'add_link_files');

// spectra Hideのウィンドウサイズを変更
function custom_hide_elements_css()
{
    $custom_css = '
        @media screen and (min-width: 1441px) {
            body .uag-hide-desktop.uagb-google-map__wrap, body .uag-hide-desktop {
                display: none !important;
            }
			body .uag-hide-tab.top-trade-products,
			body .uag-hide-mob.top-trade-products{
				display: grid !important;
			}
			body .uag-hide-desktop.top-trade-products{
				display: none !important;
			}
        }
        @media (min-width: 768px) and (max-width: 1440px) {
            body .uag-hide-tab.uagb-google-map__wrap, body .uag-hide-tab {
                display: none !important;
            }
			body .uag-hide-desktop.top-trade-products,
			body .uag-hide-mob.top-trade-products{
				display: grid !important;
			}
			body .uag-hide-tab.top-trade-products{
				display: none !important;
			}
        }
        @media screen and (max-width: 767px) {
            body .uag-hide-mob.uagb-google-map__wrap, body .uag-hide-mob {
                display: none !important;
            }
			body .uag-hide-desktop.top-trade-products,
			body .uag-hide-tab.top-trade-products{
				display: grid !important;
			}
			body .uag-hide-mob.top-trade-products{
				display: none !important;
			}
        }';

    echo '<style>' . $custom_css . '</style>';
}
add_action('wp_footer', 'custom_hide_elements_css');

// トレカトップページ
// 商品一覧
function top_product_list($atts)
{
    $top_product_list = "";
    if ($atts[0] == 'sp') {
        $product_num = 6;
	} elseif ($atts[0] == 'tb') {
		$product_num = 10;
	} else {
        $product_num = 14;
    }

    // カテゴリが 'point' のものを省くための条件
    $category_exclude = array(
        'taxonomy' => 'product_cat',
        'field'    => 'slug',
        'terms'    => 'point',
        'operator' => 'NOT IN',
    );

    if ($atts[1] == 'recommend') {
        $tax_query   = WC()->query->get_tax_query();
        $tax_query[] = array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'featured',
            'operator' => 'IN',
        );

        // カテゴリ 'point' を含まない条件を追加
        $tax_query[] = $category_exclude;

        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => $product_num,
            'orderby'        => 'modified',
            'tax_query'      => $tax_query,
        );
    } else if ($atts[1] == 'new') {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => $product_num,
            'orderby'        => 'modified',
            'tax_query'      => array($category_exclude), // カテゴリ 'point' を含まない条件を追加
        );
    } else {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => $product_num,
            'tax_query'      => array($category_exclude), // カテゴリ 'point' を含まない条件を追加
        );
    }

    $wp_query = new WP_Query($args);

    if ($wp_query->have_posts()) {
        while ($wp_query->have_posts()) {
            $wp_query->the_post();

            global $product;

            $top_product_list .=
                '<div class="top-trade-product slide-in"><a href="' . get_permalink() . '">
			<div class="thumbnail">' . woocommerce_get_product_thumbnail() . '</div>
        	<div class="product-detail"><p class="name">' . get_the_title() . '</p>
        	<p class="price">¥' . number_format_i18n(floatval($product->get_price())) . '</p></div>
      		</a></div>';
        }
    }
    wp_reset_query();

    return $top_product_list;
}
add_shortcode('sc_top_product_list', 'top_product_list');


// トレカトップページここまで

// トレカページ「もっと見る」ボタン
function toreka_button_shortcode($atts)
{
    // ショートコードの属性（attributes）を指定
    $atts = shortcode_atts(
        array(
            'text' => 'もっと見る', // デフォルトのテキスト
            'background_image' => 'https://t-card.shop/wp-content/uploads/btn-skyblue.png', // デフォルトの背景画像URL
            'slug' => '', // リンク先URL
        ),
        $atts,
        'shop_button'
    );

    // ショートコードの属性からスラッグを取得
    $slug = $atts['slug'];
    // ショートコードの中身
    $home_url = home_url();
    // スラッグとホームURLを組み合わせてリンクを生成
    $link = $home_url . '/' . $slug;
    // $button_html = '<div class="toreka-button link-btn">';
    // $button_html .= '<img src="' . $home_url . '/wp-content/uploads/btn-skyblue.png" alt="">';
    // $button_html .= '<a href="' . esc_url($link) . '">' . esc_html($atts['text']);
    // $button_html .= '</a></div>';

    $button_html = '
            <a class="button" href="' . esc_url($link) . '">' . esc_html($atts['text']) . '</a>
    ';

    return $button_html;
}

// ショートコードの登録
add_shortcode('toreka_btn', 'toreka_button_shortcode');

// ショップを見るボタン
function shop_button_shortcode($atts)
{
    // ショートコードの属性（attributes）を指定
    $atts = shortcode_atts(
        array(
            'text' => 'ショップを見る', // デフォルトのテキスト
            'background_image' => 'https://t-card.shop/wp-content/uploads/btn-orange.png', // デフォルトの背景画像URL
        ),
        $atts,
        'shop_button'
    );

    // ショートコードの中身
    $home_url = home_url();
    $button_html =
        '<div class="shop-button link-btn"><img src="' . $home_url . '/wp-content/uploads/btn-orange.png" alt="">';
    $button_html .= '<a href="' . $home_url . '/toreka">';
    $button_html .= esc_html($atts['text']);
    $button_html .= '</a></div>';

    return $button_html;
}

// ショートコードの登録
add_shortcode('shop_btn', 'shop_button_shortcode');

// カテゴリリスト
// WooCommerce商品カテゴリ一覧を表示するカスタムショートコード
function custom_product_categories_list()
{
    $output = '<div class="product-categories-list">';
    $output .= '<p>アイテムカテゴリ</p><ul>';

    // 'point' カテゴリを除外するフィルターを追加
    $exclude_category = 'point';
    add_filter('get_terms', 'exclude_category_from_product_categories', 10, 3);

    // 商品カテゴリ一覧のパラメータ
    $args = array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => 0,  // 空のカテゴリも表示
    );

    // 商品カテゴリ一覧を取得
    $product_categories = get_terms($args);

    // カテゴリが存在するか確認
    if ($product_categories && !is_wp_error($product_categories)) {
        foreach ($product_categories as $category) {
            // カテゴリのリンクを取得
            $category_link = get_term_link($category);

            // カテゴリが 'point' でない場合のみリストに追加
            if ($category->slug !== $exclude_category) {
                $output .= '<li><a href="' . esc_url($category_link) . '">' . esc_html($category->name) . '</a></li>';
            }
        }
    }

    // フィルターを削除
    remove_filter('get_terms', 'exclude_category_from_product_categories', 10);

    $output .= '</ul></div>';

    return $output;
}

// 'point' カテゴリを除外するフィルター関数
function exclude_category_from_product_categories($terms, $taxonomies, $args)
{
    if ('product_cat' === $taxonomies && !empty($args['exclude'])) {
        $exclude_category = (array)$args['exclude'];
        foreach ($terms as $key => $term) {
            if (in_array($term->slug, $exclude_category)) {
                unset($terms[$key]);
            }
        }
    }
    return $terms;
}

// ショートコード [product_categories_list] を追加
add_shortcode('product_categories_list', 'custom_product_categories_list');


// オリパページの各アイテム
function oripa_item($atts)
{
    $atts = shortcode_atts(
        array(
            'item_id' => 1, // デフォルトのアイテムID
            'max_num' => 100, // オリパの最大数
            'current_num' => 50, // オリパの残数
            'item_num' => 10, // オリパの表示数
        ),
        $atts,
    );

    $item_num = $atts['item_num'];
    ob_start(); // バッファリングを開始

?>
    <?php for ($i = 0; $i < $item_num; $i++) {
        if ($i % 2 == 0) { ?>
            <div class="oripa-container repeat-bg repeat-bg-oripa">
            <?php } ?>
            <div class="oripa-item">
                <div class="oripa-item-overview">
                    <div class="oripa-item-top">
                        <div class="oripa-item-img">
                            <img src="<?php echo esc_url(home_url()) ?>/wp-content/uploads/oripa_default.jpg" alt="">
                        </div>
                        <div class="oripa-item-num">
                            <div class="oripa-item-num-current" style="width: 50%;">
                            </div>
                            <div class="oripa-item-num-text">
                                残 <?php echo esc_html($atts['current_num']) ?>/<?php echo esc_html($atts['max_num']) ?>
                            </div>
                        </div>
                    </div>
                    <div class="oripa-item-divide">
                    </div>
                    <div class="oripa-item-bottom">
                        <div class="oripa-slot-btns">
                            <div class="link-btn oripa-slot-btn">
                                <img src="<?php echo esc_url(home_url()) ?>/wp-content/uploads/btn-skyblue.png" alt="">
                                <a href=""> 1 SLOT 100pt</a>
                            </div>
                            <div class="link-btn oripa-slot-btn">
                                <img src="<?php echo esc_url(home_url()) ?>/wp-content/uploads/btn-pink.png" alt="">
                                <a href="">10 SLOT 100pt</a>
                            </div>
                        </div>
                        <div class="oripa-img">
                            <?php if ($i % 2 == 0) { ?>
                                <img src="<?php echo esc_url(home_url()) ?>/wp-content/uploads/2023/08/shop-news-icon.png" alt="">
                            <?php } else { ?>
                                <img src="<?php echo esc_url(home_url()) ?>/wp-content/uploads/2023/08/oripa-icon.png" alt="">
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="link-btn oripa-item-hit">
                    <?php if ($i % 2 == 0) { ?>
                        <img src="<?php echo esc_url(home_url()) ?>/wp-content/uploads/btn-green.png" alt="">
                    <?php } else { ?>
                        <img src="<?php echo esc_url(home_url()) ?>/wp-content/uploads/btn-orange.png" alt="">
                    <?php } ?>
                    <a href="">当たり一覧</a>
                </div>
            </div>
            <?php if (($i % 2 != 0) || (($item_num % 2 != 0) && ($i + 1 == $item_num))) { ?>
            </div>
        <?php } ?>
<?php
    }

    return ob_get_clean(); // バッファを返し、バッファリングを終了
}

// ショートコード [oripa_item] を追加
add_shortcode('oripa_item', 'oripa_item');

// 1つのみ購入可能
add_filter('woocommerce_add_cart_item_data', 'woo_custom_add_to_cart');
function woo_custom_add_to_cart($cart_item_data)
{
    global $woocommerce;
    $woocommerce->cart->empty_cart();
    return $cart_item_data;
}

function custom_hide_div_based_on_category()
{
    // WooCommerceのカートに商品がある場合のみ実行
    if (is_checkout()) {
        $cart_items = WC()->cart->get_cart();

        // カート内の商品を順番に処理
        foreach ($cart_items as $cart_item) {
            $product_id = $cart_item['product_id'];
            $product = wc_get_product($product_id);

            // 商品が 'point' カテゴリに属しているかチェック
            if (has_term('point', 'product_cat', $product_id)) {
                // 特定のIDを持つdivを非表示にするスクリプトを追加
                echo '<script>
                        jQuery(document).ready(function($) {
                            $("#mycred-partial-payment-woo").hide();
                        });
                      </script>';
                break; // 一度見つかれば処理を終了
            }
        }
    }
}

add_action('wp_footer', 'custom_hide_div_based_on_category');

// To change add to cart text on single product page
add_filter('woocommerce_product_single_add_to_cart_text', 'woocommerce_custom_single_add_to_cart_text');
function woocommerce_custom_single_add_to_cart_text()
{
    return __('購入する', 'woocommerce');
}

// To change add to cart text on product archives(Collection) page
add_filter('woocommerce_product_add_to_cart_text', 'woocommerce_custom_product_add_to_cart_text');
function woocommerce_custom_product_add_to_cart_text()
{
    return __('購入する', 'woocommerce');
}

add_filter('wc_add_to_cart_message', '__return_false');

// ポイント購入ブロック表示のショートコード
// functions.phpに以下のコードを追加

function point_purchase_shortcode($atts)
{
    // ショートコードの引数を取得
    $atts = shortcode_atts(
        array(
            'point_num' => '',
        ),
        $atts,
        'point_purchase'
    );

    // 商品カテゴリのスラッグ
    $category_slug = $atts['point_num'] . 'pt';

    // 商品情報を取得
    $product = get_page_by_path($category_slug, OBJECT, 'product');

    // 商品が存在する場合
    if ($product) {
        $product_id = $product->ID;

        // 商品の通常価格を取得
        $normal_price = get_post_meta($product_id, '_regular_price', true);

        // ショートコードの出力
        $output = '
        <div class="point-purchase point-block-container">
            <div class="purchase-img">
                <img src="https://t-card.shop/wp-content/uploads/point-header-icon.png">
            </div>
            <div class="purchase-rate">
                <p>' . esc_html(number_format($atts['point_num'])) . ' pt</p>
                <p>' . esc_html(number_format($normal_price)) . '円で購入</p>
            </div>
            <div class="purchase-button">
                <a href="' . esc_url(add_query_arg('add-to-cart', $product_id, 'https://t-card.shop/product-category/point/')) . '">購入する</a>
            </div>
        </div>';

        return $output;
    }

    // 商品が存在しない場合は何も出力しない
    return '';
}
add_shortcode('point_purchase', 'point_purchase_shortcode');

// 商品カテゴリーがポイントの商品カテゴリーページは、固定ページのポイント購入へリダイレクト
function redirect_product_category_point()
{
    if (is_product_category('point')) {
        $redirect_url = get_permalink(get_page_by_path('point-purchase'));
        wp_redirect($redirect_url);
        exit;
    }
}
add_action('template_redirect', 'redirect_product_category_point');

function custom_order_received_redirect()
{
    $request_uri = $_SERVER['REQUEST_URI'];

    // リクエストが/order-received/で始まる場合
    if (strpos($request_uri, '/order-received/') === 0) {
        // 新しいURLにリダイレクト
        $new_url = str_replace('/order-received/', '/checkout/order-received/', $request_uri);
        wp_redirect(home_url($new_url), 301);
        exit();
    }
}
add_action('template_redirect', 'custom_order_received_redirect');

// すべてのアイテムでポイント以外
function exclude_category_products_shortcode($atts) {
    // ショートコードの属性を解析
    $atts = shortcode_atts(
        array(
            'exclude_category' => 'point',   // 除外したいカテゴリのスラッグ
            'orderby'          => 'date',    // 並べ替えの方式（新着アイテム順）
            'order'            => 'DESC',    // 並べ替え順（降順）
            'recommended_only' => false,      // おすすめ商品のみ
        ),
        $atts,
        'exclude_category_products'
    );

    // 特定のカテゴリを取得
    $exclude_category = get_term_by('slug', $atts['exclude_category'], 'product_cat');

    // ページネーションのためのページ番号を取得
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    // カテゴリが存在する場合は、そのカテゴリを除外して商品を取得
    if ($exclude_category) {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => 24,
            'paged'          => $paged,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'id',
                    'terms'    => $exclude_category->term_id,
                    'operator' => 'NOT IN',
                ),
            ),
            'orderby'        => $atts['orderby'],
            'order'          => $atts['order'],
        );

        // おすすめ商品のみを表示する場合
        if ($atts['recommended_only']) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => 'featured',
                'operator' => 'IN',
            );
        }

        $query = new WP_Query($args);

        // 商品が存在する場合はループして表示
        ob_start();
        if ($query->have_posts()) {
            echo '<div class="product-grid">';
            while ($query->have_posts()) {
                $query->the_post();
                // 商品の表示コードを追加
                wc_get_template_part('content', 'product');
            }
            echo '</div>';

            // ページネーションを表示
            echo '<div class="pagination">';
            echo paginate_links(array(
                'total'   => $query->max_num_pages,
                'current' => max(1, $paged),
            ));
            echo '</div>';
        } else {
            echo '該当する商品がありません。';
        }
        return ob_get_clean();
    }
}

// ショートコードを登録
add_shortcode('exclude_category_products', 'exclude_category_products_shortcode');

/**
 * WooCommerce パスワード強度設定変更
 * 強度設定
 * 4 = Strong
 * 3 = Medium (デフォルト) 
 * 2 = 少し強めのWeak
 * 1 = Weak
 * 0 = 閾値なし
 */
function change_password_strength( $strength ) {
    return 2;
}
add_filter( 'woocommerce_min_password_strength', 'change_password_strength' );

// Replace the existing function with a modified version
remove_action('woocommerce_after_shop_loop_item', 'astra_woo_shop_parent_category', 9);

// 商品一覧にタグ表示を追加
function astra_woo_shop_parent_category() {
    if ( apply_filters( 'astra_woo_shop_parent_category', true ) ) : ?>
        <span class="ast-woo-product-category">
            <?php
                global $product;
                $product_categories = function_exists( 'wc_get_product_category_list' ) ? wc_get_product_category_list( get_the_ID(), ';', '', '' ) : $product->get_categories( ';', '', '' );

                $product_categories = htmlspecialchars_decode( wp_strip_all_tags( $product_categories ) );
                if ( $product_categories ) {
                    list( $parent_cat ) = explode( ';', $product_categories );
                    echo apply_filters( 'astra_woo_shop_product_categories', esc_html( $parent_cat ), get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                }

                $product_tag_ids = get_the_terms(get_the_ID(), 'product_tag');
                if ($product_tag_ids && !is_wp_error($product_tag_ids)) {
                    $tags = array();

                    foreach ($product_tag_ids as $tag) {
                        $tags[] = $tag->name;
                    }

                    $product_tags = implode(', ', $tags);

                    if ($product_tags) {
                        echo '<br><span class="product-tags">' . '<span class="tagged_as">' . _n('Tag:', 'Tags:', count($product_tag_ids), 'woocommerce') . '</span> ' . $product_tags . '</span>';
                    }
                }
            ?>
        </span>
    <?php
    endif;
}

// ポイント購入メニュー
function point_purchase_link()
{
    global $wpdb;

    // 現在ログイン中のユーザーのIDを取得
    $user_id = get_current_user_id();

    // テーブル名を含んだ完全なテーブル名を作成
    $table_name = $wpdb->prefix . 'usermeta';

    // SQLクエリを構築
    $query = $wpdb->prepare(
        "SELECT meta_value FROM $table_name WHERE user_id = %d AND meta_key = 'mycred_default'",
        $user_id
    );

    // クエリを実行し、結果を取得
    $result = $wpdb->get_var($query);
    $point = number_format($result, 0);

    $uploads_baseurl = wp_upload_dir()['baseurl']; 
    $point_page = esc_url( home_url( '/point-purchase/' ) );

    $point_link = '
        <div class="point-link-container">
            <img class="point-header-icon" src ="' . $uploads_baseurl . '/point-header-icon.png" width="80px" height="90px">
            <p class="current-point">' . $point .' pt</p>
            <a class="point-link" href="' . $point_page . '"><img src="' . $uploads_baseurl . '/point-link.png" width="30px" height="30px"></a>
        </div>
    ';

    return $point_link;
}

add_shortcode('point-link', 'point_purchase_link');

// ポイント購入時のテキスト変更
function mycred_woo_payout_rewards( $order_id ) {
	 
    // Get Order
    $order    = wc_get_order( $order_id );

    global $woocommerce;

    // if we want to stop the rewarding system
    $proceed = apply_filters( 'mycred_before_woo_payout_reward', true, $order);

    if( $proceed == false )
        return;
    
    $paid_with = ( version_compare( $woocommerce->version, '3.0', '>=' ) ) ? $order->get_payment_method() : $order->payment_method;
    $buyer_id  = ( version_compare( $woocommerce->version, '3.0', '>=' ) ) ? $order->get_user_id() : $order->user_id;

    // If we paid with myCRED we do not award points by default
    if ( $paid_with == 'mycred' && apply_filters( 'mycred_woo_reward_mycred_payment', false, $order ) === false )
        return;

    // Get items
    $items    = $order->get_items();
    $types    = mycred_get_types();

    // Loop through each point type
    foreach ( $types as $point_type => $point_type_label ) {

        // Load type
        $mycred = mycred( $point_type );

        // Check for exclusions
        if ( $mycred->exclude_user( $buyer_id ) ) continue;

        // Calculate reward
        $payout = $mycred->zero();
        foreach ( $items as $item ) {

            // Get the product ID or the variation ID
            $product_id    = absint( $item['product_id'] );
            $variation_id  = absint( $item['variation_id'] );
            $reward_amount = mycred_get_woo_product_reward( $product_id, $variation_id, $point_type );

            // Reward can not be empty or zero
            if ( $reward_amount != '' && $reward_amount != 0 )
                $payout = ( $payout + ( $mycred->number( $reward_amount ) * $item['qty'] ) );

        }

        // We can not payout zero points
        if ( $payout === $mycred->zero() ) continue;

        // Let others play with the reference and log entry
        $reference = apply_filters( 'mycred_woo_reward_reference', '報酬', $order_id, $point_type );
        $log       = apply_filters( 'mycred_woo_reward_log',       '%plural%購入', $order_id, $point_type );

        // Make sure we only get points once per order
        if ( ! $mycred->has_entry( $reference, $order_id, $buyer_id ) ) {

            // Execute
            $mycred->add_creds(
                $reference,
                $buyer_id,
                $payout,
                $log,
                $order_id,
                array( 'ref_type' => 'post' ),
                $point_type
            );

        }

    }

}

//管理画面以外で指定したバージョンのjQueryを呼び出す
function load_script(){
	if (!is_admin()){
		wp_deregister_script('jquery');
		wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', array());
	}
}
add_action('wp_enqueue_scripts', 'load_script');

// 背景画像のプリロード
function add_custom_preload_image() {
    if (is_page('toreka')) {
        $upload_dir = wp_upload_dir();
        $file_name = 'toreka-bg.png';
        echo '<link rel="preload" as="image" href="' . esc_url($upload_dir['baseurl'] . '/' . $file_name) . '"/>';
        echo '<link rel="preload" as="image" href="' . esc_url($upload_dir['baseurl'] . '/2023/12/top_fv_sp.png') . '"/>';
    }
}
add_action('wp_head', 'add_custom_preload_image');

// ログインユーザーのみ適用するcss
function custom_css_for_logged_in_user() {
    $current_user = wp_get_current_user();
    if (is_user_logged_in()) {
        echo '<style>';
        echo '@media (max-width: 1080px) {';
        echo '.site-header-section>div:last-child {';
        echo 'display: none;';
        echo '}';
        echo '}';
        echo '</style>';
    }
}

// wp_headアクションに関数を追加してCSSを挿入
add_action('wp_head', 'custom_css_for_logged_in_user');

// CSSキャッシュの強制クリア
function my_update_styles( $styles ) {
    $mtime = filemtime( get_stylesheet_directory() . '/style.css' );
    $styles->default_version = $mtime;
  }
  add_action( 'wp_default_styles', 'my_update_styles' );

require_once get_stylesheet_directory() . '/oripa-functions.php';  //oripa page