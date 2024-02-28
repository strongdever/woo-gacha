<?php

//----------------------------oripa page start---------------------->
add_filter('query_vars', function ($vars) {
  $vars[] = 'ncards';
  $vars[] = 'card_ids';
  $vars[] = 'postid';
  return $vars;
});

add_filter('query_vars', function ($vars) {
  $vars[] = 'post_id';
  $vars[] = 'card_id';
  return $vars;
});
// オリパトップページ
// ガチャ一覧
function gacha_list($atts)
{
  $product_num = (isset($atts['num']) ? $atts['num'] : -1);

  $top_gacha_list = "";

  $args = array(
    'post_type'      => 'gacha',
    'posts_per_page' => $product_num,        //取得する数　-1 を指定すると全部取得
  );

  $wp_query = new WP_Query($args);

  if ($wp_query->have_posts()) {
    $i = 0;
    $top_gacha_list = '<div class="gacha-list">';
    while ($wp_query->have_posts()) {
      $wp_query->the_post();

      global $gacha;

      set_current_ncard(get_the_ID(), -1);
      // set_current_ncard(get_the_ID(), Get_TotalAndCurrendnCards(get_the_ID())); //for 'current_nCard' meta key initialization
      $thumb_img = get_field('thumb-image');
      $gacha_price = get_field('gacha-price');
      $total_ncard = get_field('total_ncards');
      $current_nCard = get_post_meta(get_the_ID(), 'current_nCard', true);
      if (is_user_logged_in()) {
        // $user_id = get_current_user_id();
        $login = true;
      } else {
        $login = false;
      }
      $number = 10;
      if ($current_nCard < 10) {
        $number = $current_nCard;
      }
      if ($current_nCard < 1) {
        $number = 10;
      }
      $points_balance = Get_Balance();

      $sold_outs = get_field('sold-out');
      $sold_out = '';
      if ($sold_outs) {
        foreach ($sold_outs as $value) {
          $sold_out = $value;
        }
      }
      if ($current_nCard < 1) {
        $display = 'block';
      } else {
        $display = 'none';
      }

      if ($sold_out == 'sold-out') {
        $hidden = 'none';
      } else {
        $hidden = 'block';
      }
      $top_gacha_list .=
        '<div class="gacha-item slide-in scroll-in" style="display: ' . $hidden . ';">
              <div class="item-wrapper">
                <div class="item-body">
                  <div class="soldout-wrapper" style="display: ' . $display . ';"><div class="soldout">SOLD OUT</div></div>
                  <div class="thumbnail-wrapper">
                    <img class="thumbnail" src="' . $thumb_img . '">
                    <div class="process-bar">
                      <div class="number-text">
                        <div class="percent-bar">
                          <span class="current-percent" style="width:' . $current_nCard / $total_ncard * 100 . '%">
                        </div>
                        <div class="card-balance">残<span class="current-num">' . $current_nCard . '</span>/<span class="total-num">' . $total_ncard . '</span></div>
                      </div>
                    </div>
                  </div>
                  <div class="btns-wrapper">
                    <div class="btns" data-userid="' . $login . '" data-id="' . get_the_ID() . '" data-price="' . $gacha_price . '" data-totalncard="' . $total_ncard . '" data-currentncard="' . $current_nCard . '" data-pointbalance="' . $points_balance . '">
                      <div class="btn-1 btn btn-gacha" data-number="1">
                        <div class="btn-content">
                          1<span>SLOT</span> ' . $gacha_price . 'PT
                        </div>
                        <img class="btn-bg" src="' . T_DIRE_URI . '-child/assets/img/oripa/btn-back-yellow.png">
                      </div>
                      <div class="btn-10 btn btn-gacha" data-number="' . $number . '">
                        <div class="btn-content">
                        ' . $number . '<span>SLOT</span> ' . $gacha_price * $number . 'PT
                        </div>
                        <img class="btn-bg" src="' . T_DIRE_URI . '-child/assets/img/oripa/btn-back-blue.png">
                      </div>
                    </div>
                    <img class="btn-right-stars" src="' . T_DIRE_URI . '-child/assets/img/' . ($i % 2 == 0 ? 'right-stars-red.png' : 'right-stars-yellow.png') . '">
                  </div>
                </div>
                <a class="btn-detail yellow-btn" href="' . get_the_permalink() . '">
                  当たり一覧
                </a>
              </div>
            </div>';
    }
    $top_gacha_list .= '</div>';
  }
  wp_reset_query();

  return $top_gacha_list;
}

add_shortcode('oripa_gacha_list', 'gacha_list');

function card_list($atts)
{ //カード一覧
  $winning_update = (isset($atts['update']) ? $atts['update'] : 'all');
  $display_card_list = "";

  if (is_user_logged_in()) {
    global $mywpdb;
    $mywpdb = new wpdb('tglobal', 'V-dF4pjMsBf_', 'tglobal_ec', 'mysql57.tglobal.sakura.ne.jp');
    $table_name = 'wp1567b2winning_cards';

    $user_id = get_current_user_id();

    if ($winning_update == 'all') {
      $winning_cards = $mywpdb->get_results(
        $mywpdb->prepare("SELECT card_id, post_id, card_title, card_filename, card_price FROM $table_name WHERE user_id = %d ORDER BY card_price DESC", $user_id),
        ARRAY_A
      );
    } else {
      $winning_cards = $mywpdb->get_results(
        $mywpdb->prepare("SELECT card_id, post_id, card_title, card_filename, card_price FROM $table_name WHERE user_id = %d AND winning_update = %s ORDER BY card_price DESC", $user_id, $winning_update),
        ARRAY_A
      );
    }

    $path_parts = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $path_parts = pathinfo($path_parts);
    $page_slug = $path_parts['filename'];

    $ncard = count($winning_cards);
    if (Get_Balance()) {
      $current_balance = Get_Balance();
    } else {
      $current_balance = 0;
    }

    $next_balance = $current_balance;
    if ($ncard > 0) {
      $display_card_list .=
        '<div class="ckeck-ncards">
          <div class="remain-cards"><span>' . $ncard . '</span>枚のカードが残っています。</div>
          <div class="check-all-wrapper">
              <input type="checkbox" class="btn-check-all" id="check-all">
              <label for="check-all">すべて選択</label>
          </div>
        </div>
        <ul class="card-list">';
      foreach ($winning_cards as $winning_card) {
        $post_id = $winning_card['post_id'];

        $metaKey = 'post_id_store';
        $metaValue = $post_id;
        update_user_meta($user_id, $metaKey, $metaValue);

        $card_theme_path = get_field('upload-url', $post_id);
        $img_path = $card_theme_path . '/' . $winning_card['card_filename'];
        $next_balance += $winning_card['card_price'];
        $card_title = $winning_card['card_title'];
        $display_card_list .=
          '<li class="card-item">
            <div class="card-wrapper">
                <div class="input-wrapper">
                    <input type="checkbox" class="card-id" data-cardid="' . $winning_card['card_id'] . '" data-cardprice="' . $winning_card['card_price'] . '" id="' . $winning_card['card_id'] . '" ckecked>
                    <label for="' . $winning_card['card_id'] . '"></label>
                </div>
                <div class="img-wrapper">
                    <img class="thumb lightbox" src="' . $img_path . '">
                    <div class="coin-bar">
                        <img class="coin" src="' . T_DIRE_URI . '-child/assets/img/coin.png">
                        <span class="coin-num">' . $winning_card['card_price'] . '</span>
                    </div>
                </div>
                <div class="content-pannel">
                  <h2 class="title">' . $card_title . '</h2>
                  <div class="refunds-points-wrapper">
                    <div class="label">還元ポイント</div>
                    <div class="refunds-points">' . $winning_card['card_price'] . 'pt</div>
                  </div>
                  <div class="btns">
                    <div class="btn btn-single-refunds" data-userid="' . $user_id . '" data-ncard="' . $ncard . '" data-currentbalance="' . $current_balance . '" data-nextbalance="' . $next_balance . '" data-pageslug="' . $page_slug . '" data-postid="' . $post_id . '">
                        <span>
                          ポイントに交換<br>' .
          $winning_card['card_price'] . 'pt
                        </span>
                    </div>
                    <div class="btn btn-single-send" data-userid="' . $user_id . '" data-ncard="' . $ncard . '" data-currentbalance="' . $current_balance . '" data-nextbalance="' . $next_balance . '" data-pageslug="' . $page_slug . '" data-postid="' . $post_id . '">
                        <span>
                          カードを発送
                        </span>
                    </div>
                  </div>
                </div>
            </div>
        </li>';
      }
      $display_card_list .=
        '</ul>
          <div class="options">
            <button class="btn-option btn-coin" data-userid="' . $user_id . '" data-ncard="' . $ncard . '" data-currentbalance="' . $current_balance . '" data-nextbalance="' . $next_balance . '" data-pageslug="' . $page_slug . '" data-postid="' . $post_id . '">
              <span>ポイントに交換</span>
            </button>
            <button class="btn-option btn-card-sending" data-userid="' . $user_id . '" data-ncard="' . $ncard . '" data-currentbalance="' . $current_balance . '" data-nextbalance="' . $next_balance . '" data-pageslug="' . $page_slug . '" data-postid="' . $post_id . '">
              <span>カードを発送</span>
            </div>
            </button>
          </div>';
    } else {
      $display_card_list .=
        '<div class="no-items">表示するカード情報がありません。</div>';
    }

    if (is_user_logged_in()) {  //continue gacha
      // $user_id = get_current_user_id();
      $login = true;
    } else {
      $login = false;
    }

    $user_id = get_current_user_id(); // Replace with the user ID
    $metaKey = 'post_id_store';

    $post_id = get_user_meta($user_id, $metaKey, true);
    $gacha_price = get_field('gacha-price', $post_id);
    $total_ncard = get_field('total_ncards', $post_id);
    $current_nCard = get_post_meta($post_id, 'current_nCard', true);
    $points_balance = Get_Balance();
    $number = 10;
    if ($current_nCard < 10) {
      $number = $current_nCard;
    }
    if ($current_nCard < 1) {
      $number = 10;
    }

    $display_card_list .=
      '<div class="continue-gacha">
      <div class="label">もう一度引く</div>
      <div class="btns-wrapper">
      <img class="btn-left-stars btn-stars" src="' . T_DIRE_URI . '-child/assets/img/right-stars-yellow.png">
      <div class="btns-bar">
        <div class="btns" data-userid="' . $login . '" data-id="' . $post_id . '" data-price="' . $gacha_price . '" data-totalncard="' . $total_ncard . '" data-currentncard="' . $current_nCard . '" data-pointbalance="' . $points_balance . '">
          <div class="btn-1 btn btn-gacha" data-number="1">
            <div class="btn-content">
              1<span>SLOT</span> ' . $gacha_price . 'PT
            </div>
            <img class="btn-bg" src="' . T_DIRE_URI . '-child/assets/img/oripa/btn-back-yellow.png">
          </div>
          <div class="btn-10 btn btn-gacha" data-number="' . $number . '">
            <div class="btn-content">
            ' . $number . '<span>SLOT</span> ' . $gacha_price * $number . 'PT
            </div>
            <img class="btn-bg" src="' . T_DIRE_URI . '-child/assets/img/oripa/btn-back-blue.png">
          </div>
        </div>
        <div class="display-ncards number-text">
          <div class="percent-bar">
            <span class="current-percent" style="width:' . $current_nCard / $total_ncard * 100 . '%">
          </div>
          <div class="card-balance">残<span class="current-num">' . $current_nCard . '</span>/<span class="total-num">' . $total_ncard . '</span></div>
        </div>
      </div>
      <img class="btn-right-stars btn-stars" src="' . T_DIRE_URI . '-child/assets/img/right-stars-red.png">
    </div>
    </div>';
  } else {
    $display_card_list .=
      '<div class="no-items">表示するカード情報がありません。</div>';
  }

  return $display_card_list;
}
add_shortcode('oripa_card_list', 'card_list');

// Ajax response to process gacha
function handle_ajax_request1()
{
  // Retrieve the data
  $data = $_POST['my_data'];
  $post_id = $data['post_id'];
  $number = (int) $data['number'];

  $result = Deduction_Coin($post_id, $number);  //deduct the coin of the gacha when the user pull that gacha

  $winning_card = [];
  if ($result == 'success') {
    Update_Old_item(get_current_user_id()); //update the winning_update item of the table as 'old'
    for ($i = 1; $i <= $number; $i++) {
      $card = Lottery_Process($post_id);  //process the gacha
      $winning_card[] = $card;
      Store_Winning_Card($card['card_id'], $card['winner_id'], $post_id, $card['card_title'], $card['card_img'], $card['card_coin']);
    }

    //get the video url
    $cpt_name = 'video-group';
    $video_url = Get_VideoURL($cpt_name, $number, $winning_card);
  }

  $response = array(
    'result' => $result,
    'post_id' => $post_id,
    'video_url' => $video_url,
    'winning_card' => $winning_card,
    'message' => 'Data received and processed successfully!',
  );
  // echo "sdgsgsergrstg";
  wp_send_json_success($response);
  wp_die();
}
add_action('wp_ajax_my_ajax_action1', 'handle_ajax_request1');

// Ajax response to return the coin of the winning cards
function handle_ajax_request2()
{
  // Retrieve the data
  $data = $_POST['my_data'];
  $card_ids = $data['card_ids'];

  //prcessing the store the coin of the winning cards
  $result = Card_to_Point($card_ids);  //return the coin of the winning cards

  $response = array(
    'result' => $result,
  );

  wp_send_json_success($response);
  wp_die();
}
add_action('wp_ajax_my_ajax_action2', 'handle_ajax_request2');

//Ajax response to delete the sending cards of the winning cards
function handle_ajax_request3()
{
  // Retrieve the data
  $data = $_POST['my_data'];
  $card_ids = $data['card_ids'];
  $formData = $data['formData'];

  //proccessing of sending mail
  $result = SendingMail($card_ids, $formData);  //return the coin of the winning cards

  //prcessing the store the coin of the winning cards
  if ($result == 'success') {
    $result = Delete_SendingCards($card_ids);  //return the coin of the winning cards
  }

  $response = array(
    'result' => $result,
  );

  wp_send_json_success($response);
  wp_die();
}
add_action('wp_ajax_my_ajax_action3', 'handle_ajax_request3');

function Delete_SendingCards($card_ids)
{
  $func_result = 'success';
  if (is_user_logged_in()) {
    global $mywpdb;
    $mywpdb = new wpdb('tglobal', 'V-dF4pjMsBf_', 'tglobal_ec', 'mysql57.tglobal.sakura.ne.jp');
    $table_name = 'wp1567b2winning_cards';

    $user_id = get_current_user_id();
    $cardIDsString = "'" . implode("','", $card_ids) . "'";
    $mywpdb->query(
      $mywpdb->prepare(
        "DELETE FROM $table_name WHERE user_id = %s AND card_id IN ($cardIDsString)",
        $user_id
      )
    );

    $func_result = 'success';
  } else {
    $func_result = 'ログインしてから実行してください';
  }
  return $func_result;
}

//when publish the gacha post
function gacha_publish($post_ID, $post, $update)
{
  // Check if the post status is set to 'publish'
  if ($post->post_status === 'publish') {
    set_current_ncard(get_the_ID(), Get_TotalAndCurrendnCards(get_the_ID())); //for 'current_nCard' meta key initialization
  }
}

add_action('save_post', 'gacha_publish', 10, 3);


//meta_key setting for current number of the cards
function set_current_ncard($post_id, $number)
{
  $meta_key = 'current_nCard';
  $count = get_post_meta($post_id, $meta_key, true);

  // update_post_meta($post_id, $meta_key, get_field('total_ncards', $post_id)); //meta_key initialize

  if ($count === '') {
    $count = get_field('total_ncards', $post_id);
    add_post_meta($post_id, $meta_key, $count);
  } else {
    // $count = $count - $number;
    if ($number >= 0) {
      update_post_meta($post_id, $meta_key, $number);
    }
  }
}

function Get_Balance()
{  //get the current user's point balance
  if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $points_balance = mycred_get_users_balance($user_id, 'mycred_default');
    return $points_balance;
  } else {
    return 0;
  }
}

function Get_TotalAndCurrendnCards($post_id)
{
  $curren_ncard = 0;
  if (get_field('csv', $post_id)) {
    $csv_path = get_field('csv', $post_id);
    if (($handle = fopen($csv_path, "r")) !== false) {

      $curren_ncard = 0;
      while (($data = fgetcsv($handle)) !== false) {
        if ($data[5] == '0') {
          $curren_ncard++;
        }
      }

      fclose($handle);
    }
  }
  return $curren_ncard;
}

function Get_VideoURL($cpt_name, $number, $winning_card)
{ //get video url
  $video_group = get_field($cpt_name, 'option');
  if ($video_group) {
    $str1 = Normalizer::normalize("１等", Normalizer::FORM_KC);
    $str2 = Normalizer::normalize("2等", Normalizer::FORM_KC);
    $str3 = Normalizer::normalize("3等", Normalizer::FORM_KC);
    $str4 = Normalizer::normalize("4等", Normalizer::FORM_KC);

    if ($number == 1) {
      $rank = $winning_card[0]['card_rank'];
    } else {
      $temp_coin = 0;
      $k = 0;
      for ($i = 0; $i < $number; $i++) {
        if ($temp_coin < $winning_card[$i]['card_coin']) {
          $temp_coin = $winning_card[$i]['card_coin'];
          $k = $i;
        }
      }
      $rank = $winning_card[$k]['card_rank'];
    }

    $rank = Normalizer::normalize($rank, Normalizer::FORM_KC);
    switch ($rank) {
      case $str1:
        $video_url = $video_group['video1'];
        break;
      case $str2:
        $video_url = $video_group['video2'];
        break;
      case $str3:
        $video_url = $video_group['video3'];
        break;
      case $str4:
        $video_url = $video_group['video4'];
        break;
      default:
        $video_url = $video_group['video5'];
    }
  } else {
    $video_url = '';
  }

  return $video_url;
}

function Deduction_Coin($post_id, $number)
{
  if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $gacha_price = get_field('gacha-price', $post_id); // Replace with the desired value to subtract
    $amount = $gacha_price * $number;
    // Get the current balance of the user's myCRED points
    $current_balance = Get_Balance();

    // Check if the user has enough points to subtract
    if ($current_balance >= $amount) {
      // Subtract the points from the user's balance
      $reference = $number . 'SLOT';
      $entry = 'オリパでの購入';
      mycred_subtract($reference, $user_id, $amount, $entry);
      $result = 'success';
    } else {
      $result = 'ポイントが足りません!';
    }
  } else {
    $result = 'ログインしてから実行してください';
  }
  return $result;
}

function SendingMail($card_ids, $formData)
{
  $func_result = 'success';

  if (is_user_logged_in()) {
    global $mywpdb;
    $mywpdb = new wpdb('tglobal', 'V-dF4pjMsBf_', 'tglobal_ec', 'mysql57.tglobal.sakura.ne.jp');
    $table_name = 'wp1567b2winning_cards';

    $card_ids_string = "'" . implode("','", $card_ids) . "'";

    $query = "SELECT card_title, card_price, post_id FROM $table_name WHERE card_id IN ($card_ids_string)";
    $results = $mywpdb->get_results($query);
    $cards_list = $results;
    $ncard = count($results);

    mb_language("Japanese");
    mb_internal_encoding("UTF-8");

    //------------------sending to the users start---------------->
    $to = $formData[0][9];
    $subject = "カード発送";
    $message = $formData[0][0] . $formData[0][1] . 'さん
    
    ご当選おめでとうございます。
    
    T-Cardオリパをご利用いただきありがとうございます。
    下記のカードの発送を受け承りました。
    
    商品の発送までしばらくお待ちください。\r\n
    
    
    【お届け先情報】
    
    国または地域 : ' . $formData[0][2] . '
    郵便番号 : ' . $formData[0][3] . '
    都道府県 : ' . $formData[0][4] . '
    市区町村 : ' . $formData[0][5] . '
    番地 : ' . $formData[0][6] . $formData[0][7] . '
    お名前 : ' . $formData[0][0] . $formData[0][1] . '
    連絡先 : ' . $formData[0][8] . '
    メール : ' . $formData[0][9];
    if (count($formData) > 1) {
      $message .= '
      

      別の住所
      
      国または地域 : ' . $formData[1][2] . '
      郵便番号 : ' . $formData[1][3] . '
      都道府県 : ' . $formData[1][4] . '
      市区町村 : ' . $formData[1][5] . '
      番地 : ' . $formData[1][6] . $formData[1][7] . '
      お名前 : ' . $formData[1][0] . $formData[1][1] . '
      連絡先 : ' . $formData[1][8] . '
      メール : ' . $formData[1][9];
    }

    $message .= '


    【お届けするカード情報】

    発送するカード: ' . $ncard . '
    
    ';
    foreach ($results as $result) {
      $message .= $result->card_title . '             ' . $result->card_price . 'pt
    ';
    }


    '----------------------------------
    またのご利用をお待ちしています。
    https://t-card.shop/


    事業者の名称および連絡先
    会社名：T-Global株式会社
    担当者：兼田　拓也
    本社住所：〒494-0008
    愛知県一宮市東五城字備前23番地4
    メールアドレス：contact@t-card.shop';

    $headers = 'From: admin@t-card.shop' . "\r\n";
    if (count($formData) > 1) {
      $headers .= 'Bcc: ' . $formData[1][9];
    }

    mb_send_mail($to, $subject, $message, $headers);
    //-----------------sending to the users end--------------------

    //-----------------sending to the managers start-------------------->
    $cards_array_result = [];
    foreach ($cards_list as $card) {
      $post_id = $card->post_id;

      if (!isset($cards_array_result[$post_id])) {
        $cards_array_result[$post_id] = [];
      }

      $cards_array_result[$post_id][] = [$card->card_title, $card->card_price];
    }

    sleep(0.5);
    foreach ($cards_array_result as $post_id => $cards) {
      $to = get_field('agency_email', $post_id);
      $message = '管理担当者様
      
    お客様より発送の手続きが行われました。
    下記の内容で発送の準備をお願い致します。



    【お届け先情報】
    
    国または地域 : ' . $formData[0][2] . '
    郵便番号 : ' . $formData[0][3] . '
    都道府県 : ' . $formData[0][4] . '
    市区町村 : ' . $formData[0][5] . '
    番地 : ' . $formData[0][6] . $formData[0][7] . '
    お名前 : ' . $formData[0][0] . $formData[0][1] . '
    連絡先 : ' . $formData[0][8] . '
    メール : ' . $formData[0][9];
      if (count($formData) > 1) {
        $message .= '
      

      別の住所
      
      国または地域 : ' . $formData[1][2] . '
      郵便番号 : ' . $formData[1][3] . '
      都道府県 : ' . $formData[1][4] . '
      市区町村 : ' . $formData[1][5] . '
      番地 : ' . $formData[1][6] . $formData[1][7] . '
      お名前 : ' . $formData[1][0] . $formData[1][1] . '
      連絡先 : ' . $formData[1][8] . '
      メール : ' . $formData[1][9];
      }

      $message .= '


    【お届けするカード情報】

    発送するカード: ' . count($cards) . '
    
    ';
      foreach ($cards as $card) {
        $message .= $card[0] . '             ' . $card[1] . 'pt
    ';
      }


      '----------------------------------
    またのご利用をお待ちしています。
    https://t-card.shop/


    事業者の名称および連絡先
    会社名：T-Global株式会社
    担当者：兼田　拓也
    本社住所：〒494-0008
    愛知県一宮市東五城字備前23番地4
    メールアドレス：contact@t-card.shop
    ';

      $headers = 'From: admin@t-card.shop' . "\r\n";
      $headers .= 'Reply-To: ' . $formData[0][9];
      mb_send_mail($to, $subject, $message, $headers);
      sleep(0.5);
    }
    //-----------------sending to the managers end-------------------->
    $func_result = 'success';
  } else {
    $func_result = 'ログインしてから実行してください';
  }

  return $func_result;
}

function Card_to_Point($card_ids)
{
  $func_result = '';
  if (is_user_logged_in()) {
    global $mywpdb;
    $mywpdb = new wpdb('tglobal', 'V-dF4pjMsBf_', 'tglobal_ec', 'mysql57.tglobal.sakura.ne.jp');
    $table_name = 'wp1567b2winning_cards';

    $card_ids_string = "'" . implode("','", $card_ids) . "'";

    $query = "SELECT card_price FROM $table_name WHERE card_id IN ($card_ids_string)";
    $results = $mywpdb->get_results($query);
    $ncard = count($results);

    if ($results) {
      $reward_price = 0;
      foreach ($results as $result) {
        $reward_price = $reward_price + $result->card_price;
      }
    }

    $query = "DELETE FROM $table_name WHERE card_id IN ($card_ids_string)";
    $mywpdb->query($query);

    if ($ncard == 1) {
      $reference = $ncard . 'CARD';
    } else {
      $reference = $ncard . 'CARDS';
    }
    $userid = get_current_user_id();
    $entry = 'ポイント替える';
    mycred_add($reference, $userid, $reward_price, $entry);

    $func_result = 'success';
  } else {
    $func_result = 'ログインしてから実行してください';
  }
  return $func_result;
}

function Lottery_Process($post_id)  //lottering
{
  $winning_card = [];
  $rank = '';
  $winner = '';
  $winning_number = '';

  if (get_field('csv', $post_id)) {
    $csv_path = get_field('csv', $post_id);
    $file_name = basename($csv_path);
    $file_path = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/uploads/' . $file_name;

    $arry_remain_cardID = [];

    $data = []; //data of csv file
    $new_data = []; //new data to write to csv file

    if (($handle = fopen($file_path, "r")) !== false) {
      while (($row = fgetcsv($handle)) !== false) {
        $data[] = $row;
        if ($row[5] == '0') {
          $arry_remain_cardID[] = $row[0];
        }
      }
      fclose($handle);
    } else {
      return false;
    }

    $winning_number = $arry_remain_cardID[rand(0, count($arry_remain_cardID) - 1)]; //get the winning number

    foreach ($data as $row) {
      if ($row[0] == $winning_number) {
        $card_id = $row[0]; //get the id of the winning card
        $card_rank = $row[1];  //get the rank of the winning card
        $card_coin = $row[2];
        $card_title = $row[3];
        $card_img = $row[4];
        $row[5] = '1';
        $row[6] = get_current_user_id();
        $winning_flag = $row[5];
        $winner = $row[6];
      }

      $new_data[] = $row; //create the new data to write to the csv file.
    }
    if (($handle = fopen($file_path, "w")) !== false) {    //write the new data to the csv file.
      foreach ($new_data as $row) {
        fputcsv($handle, $row);
      }
      fclose($handle);
    } else {
      return $handle;
    }

    $winning_card['card_id'] = $card_id;
    $winning_card['card_rank'] = $card_rank;
    $winning_card['card_coin'] = $card_coin;
    $winning_card['card_title'] = $card_title;
    $winning_card['winner_id'] = $winner;
    $winning_card['card_img'] = $card_img;
  }

  set_current_ncard($post_id, Get_TotalAndCurrendnCards($post_id));

  return $winning_card;
}

// オプションページを追加
if (function_exists('acf_add_options_page')) {
  $option_page = acf_add_options_page(array(
    'page_title' => 'テーマオプション', // 設定ページで表示される名前
    'menu_title' => 'テーマオプション', // ナビに表示される名前
    'menu_slug' => 'top_setting',
    'capability' => 'edit_posts',
    'redirect' => false
  ));
}

function Store_Winning_Card($card_id, $winner, $post_id, $card_title, $card_filename, $card_coin)  //store the winning card to the wp1567b2winning_cards table
{
  global $mywpdb;
  $mywpdb = new wpdb('tglobal', 'V-dF4pjMsBf_', 'tglobal_ec', 'mysql57.tglobal.sakura.ne.jp');

  $winner_data = get_userdata($winner);
  $winner_name = $winner_data->user_login;
  $table_name = 'wp1567b2winning_cards';
  $card_number = $winner . $post_id . $card_id;
  date_default_timezone_set('Asia/Tokyo');
  $update_time = date('Y-m-d H:i:s');

  $data = array(
    'card_id' => $card_number,
    'user_id' => $winner,
    'user_name' => $winner_name,
    'post_id' => $post_id,
    'card_title' => $card_title,
    'card_filename' => $card_filename,
    'card_price' => $card_coin,
    'winning_update' => 'new',
    'update_time' => $update_time,
  );

  $mywpdb->insert($table_name, $data);
}

function Update_Old_item($winner)
{
  global $mywpdb;
  $mywpdb = new wpdb('tglobal', 'V-dF4pjMsBf_', 'tglobal_ec', 'mysql57.tglobal.sakura.ne.jp');
  $table_name = 'wp1567b2winning_cards';

  $mywpdb->update(
    $table_name,
    array('winning_update' => 'old'),
    array('user_id' => $winner) // Specify the condition to identify the row to be updated
  );
}

// my account のメニューに保有カードメニュー項目を追加しました。 start---------->
// reference url: https://tcd-theme.com/2023/05/woocommerce-mypage-customize.html
add_action('init', 'cards_list_add_endpoint');
function cards_list_add_endpoint()
{
  add_rewrite_endpoint('cards_list', EP_PAGES);
}

add_filter('woocommerce_account_menu_items', 'cards_list_add_menu_item');
function cards_list_add_menu_item($items)
{
  $items['cards_list'] = '保有カードリスト';
  return $items;
}

add_action('woocommerce_account_cards_list_endpoint', 'cards_list_add_menu');
function cards_list_add_menu()
{
  echo '<div id="gacha-card-list" class="store-cards-list"><div class="container">' . card_list('') . '<div class="modal-wrapper"></div></div></div>';
}
// my account のメニューに保有カードメニュー項目を追加しました。 end---------->

function custom_dashboard_css()
{
  wp_enqueue_style('custom-dashboard-css', T_DIRE_URI . '-child/assets/css/oripa-admin-dashboard.css');
}
add_action('admin_enqueue_scripts', 'custom_dashboard_css');

add_filter('show_admin_bar', '__return_false', 99); //remove the space by the admin bar

function oripa_button_shortcode($atts)
{
  // ショートコードの属性（attributes）を指定
  $atts = shortcode_atts(
    array(
      'text' => 'ショップを見る', // デフォルトのテキスト
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

  $button_html = '
            <a class="yellow-btn" href="' . esc_url($link) . '">' . esc_html($atts['text']) . '</a>
    ';

  return $button_html;
}

// ショートコードの登録
add_shortcode('oripa_btn', 'oripa_button_shortcode');

//<----------------------------oripa page end----------------------
