<?php
/**
Template Name: ポイント購入ページ
***/
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

?>
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

<?php if ( astra_page_layout() == 'left-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

	<div id="primary" <?php astra_primary_class(); ?>>

		<div class="page-container">
            <div class="point-page-container repeat-bg repeat-bg-putchase">
                <div class="point-page">
                    <?php if (is_user_logged_in()) { ?>
                    <div class="point-purchase-title point-block-container">
                        <p>ポイント購入</p>
                        <div class="point-balance">
                            <p>所持ポイント</p>
                            <p><?php echo $result; ?> point</p>
                        </div>
                    </div>
                    <?php echo do_shortcode('[point_purchase point_num="500"]'); ?>
                    <?php echo do_shortcode('[point_purchase point_num="1000"]'); ?>
                    <?php echo do_shortcode('[point_purchase point_num="5000"]'); ?>
                    <?php } else { ?>
                    <p>ログインをしてください。</p>
                    <?php } ?>
                </div>
            </div>
        </div>

	</div><!-- #primary -->

<?php if ( astra_page_layout() == 'right-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

<?php get_footer(); ?>
