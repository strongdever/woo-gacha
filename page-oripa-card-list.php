<?php

/**
Template Name: ガチャカード一覧
 ***/
get_header();
?>

<main id="gacha-card-list">
    <div class="container">
        <?php echo do_shortcode('[oripa_card_list update="new"]'); ?>
        <div class="btns">
            <a class="btn-oripa yellow-btn">
                オリパページへ
                <!-- <img class="btn-bg" src="https://t-card.shop/wp-content/uploads/btn-skyblue.png"> -->
            </a>
            <a class="btn-store-card yellow-btn">
                保有カード一覧へ
                <!-- <img class="btn-bg" src="https://t-card.shop/wp-content/uploads/btn-skyblue.png"> -->
            </a>
        </div>
        <div class="modal-wrapper"></div>
    </div>

</main>

<?php get_footer(); ?>