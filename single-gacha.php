<?php get_header(); ?>

<main id="single-gacha">
    <div class="container">
        <?php
        $theme_image_pc = get_field('theme-image-pc');
        $theme_image_sp = get_field('theme-image-sp');
        $gacha_price = get_field('gacha-price');
        $total_ncard = get_field('total_ncards');
        $current_nCard = get_post_meta(get_the_ID(), 'current_nCard', true);
        $points_balance = Get_Balance();
        ?>
        <section class="top-image">
            <img class="theme-image pc" src="<?php echo $theme_image_pc; ?>">
            <img class="theme-image sp" src="<?php echo $theme_image_sp; ?>">
            <a class="btn-action btn-oripa" href="<?php echo HOME; ?>">
                <span>ガチャ一覧</span>
            </a>
            <div class="btns-wrapper">
                <div class="progress">
                    <div class="price-wrapper">
                        <img src="">
                        必要ポイント数 : <span class="coin-number"><?php echo $gacha_price; ?></span> PT
                    </div>
                    <div class="gacha-number">
                        残 <span class="current-number"><?php echo $current_nCard; ?></span> / <span class="total-number"><?php echo $total_ncard; ?></span>
                    </div>
                </div>
                <div class="btns" data-userid="<?php echo get_current_user_id(); ?>" data-id="<?php echo get_the_ID(); ?>" data-price="<?php echo $gacha_price; ?>" data-totalncard="<?php echo $total_ncard; ?>" data-currentncard="<?php echo $current_nCard; ?>" data-pointbalance="<?php echo $points_balance; ?>">
                    <div class="btn-1 btn btn-gacha" data-number="1">
                        <div class="btn-content">1SLOT <?php echo $gacha_price; ?>PT</div>
                        <img class="btn-bg" src="https://t-card.shop/wp-content/uploads/btn-skyblue.png">
                    </div>
                    <?php
                    $number = 10;
                    if ($current_nCard < 10) {
                        $number = $current_nCard;
                    }
                    if ($current_nCard < 1) {
                        $number = 10;
                    }
                    ?>
                    <div class="btn-10 btn btn-gacha" data-number="<?php echo $number; ?>">
                        <div class="btn-content"><?php echo $number; ?>SLOT <?php echo $gacha_price * $number; ?>PT</div>
                        <img class="btn-bg" src="https://t-card.shop/wp-content/uploads/btn-pink.png">
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal-wrapper"></div>
</main>

<?php get_footer(); ?>