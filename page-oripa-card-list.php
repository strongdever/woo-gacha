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
            <a class="btn-oripa btn">
                <div class="btn-content">オリパページへ</div>
                <img class="btn-bg" src="https://t-card.shop/wp-content/uploads/btn-skyblue.png">
            </a>
            <a class="btn-store-card btn">
                <div class="btn-content">保有カード一覧へ</div>
                <img class="btn-bg" src="https://t-card.shop/wp-content/uploads/btn-skyblue.png">
            </a>
        </div>
        <div class="modal-wrapper"></div>
    </div>

</main>

<?php get_footer(); ?>