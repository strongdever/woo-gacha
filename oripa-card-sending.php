<?php

/**
Template Name: カード発送
 ***/
get_header();

$ncards = get_query_var('ncards') ? get_query_var('ncards') : 0;
$card_ids = get_query_var('card_ids') ? get_query_var('card_ids') : 0;
$post_id = get_query_var('postid') ? get_query_var('postid') : 0;
$arry_cardIDs = explode("-", $card_ids);

$agency_email = get_field('agency_email', $post_id);

$user_id = get_current_user_id();

//get the delivery address
if (is_user_logged_in()) {

    $first_name = get_user_meta($user_id, 'billing_first_name', true);
    $last_name = get_user_meta($user_id, 'billing_last_name', true);
    $country_code = get_user_meta($user_id, 'billing_country', true);
    $wc_countries = new WC_Countries();
    $country = $wc_countries->countries[$country_code];
    $post_code = get_user_meta($user_id, 'billing_postcode', true);
    $state_code = get_user_meta($user_id, 'billing_state', true);
    $wc_countries = WC()->countries;
    $states = $wc_countries->get_states($country_code);
    if (isset($states[$state_code])) {
        $province = $states[$state_code];
    } else {
        $province = 'Unknown';
    }
    $city = get_user_meta($user_id, 'billing_city', true);
    $address1 = get_user_meta($user_id, 'billing_address_1', true);
    $address2 = get_user_meta($user_id, 'billing_address_2', true);
    $phone = get_user_meta($user_id, 'billing_phone', true);
    $email = get_user_meta($user_id, 'billing_email', true);

    $mywpdb = new wpdb('tglobal', 'V-dF4pjMsBf_', 'tglobal_ec', 'mysql57.tglobal.sakura.ne.jp');
    $table_name = 'wp1567b2winning_cards';

    $placeholders = array_fill(0, count($arry_cardIDs), '%s');
    $placeholders = implode(', ', $placeholders);

    $card_ids_string = "'" . implode("','", $arry_cardIDs) . "'";

    $results = $mywpdb->get_results(
        $mywpdb->prepare("SELECT card_title, card_price FROM $table_name WHERE user_id = %d AND card_id IN ($card_ids_string)", $user_id),
        ARRAY_A
    );  //get all the card_price of the new winning cards
}

?>

<main id="gacha-card-sending">
    <div class="container main-form">
        <?php if (is_user_logged_in()) : ?>
            <div class="form-wrapper" id="myform">
                <ul class="vertical-wrapper profile-info">
                    <li class="horizontal-wrapper">
                        <div class="vertical-wrapper input-item half-width">
                            <label class="required" for="lastname">氏</label>
                            <input type="text" name="lastname" id="lastname" class="input half-length" placeholder="例）鈴木">
                            <div class="error-msg lastname-error">この項目は必須項目です。</div>
                        </div>
                        <div class="vertical-wrapper input-item half-width">
                            <label class="required" for="firstname">名</label>
                            <input type="text" name="firstname" id="firstname" class="input half-length" placeholder="例）太郎">
                            <div class="error-msg firstname-error">この項目は必須項目です。</div>
                        </div>
                    </li>
                    <li class="vertical-wrapper input-item">
                        <label class="required" for="country">国または地域</label>
                        <input type="text" name="country" id="country" class="input whole-length" placeholder="例）日本">
                        <div class="error-msg country-error">この項目は必須項目です。</div>
                    </li>
                    <li class="horizontal-wrapper">
                        <div class="vertical-wrapper input-item half-width">
                            <label class="required" for="post-code">郵便番号</label>
                            <input type="text" name="post-code" id="post-code" class="input half-length" placeholder="例）123-4567">
                            <div class="error-msg post-code-error">この項目は必須項目です。</div>
                        </div>
                        <div class="vertical-wrapper input-item half-width">
                            <label class="required" for="province">都道府県</label>
                            <input type="text" name="province" id="province" class="input half-length" placeholder="例）東京都">
                            <div class="error-msg province-error">この項目は必須項目です。</div>
                        </div>
                    </li>
                    <li class="vertical-wrapper input-item">
                        <label class="required" for="city">市区町村</label>
                        <input type="text" name="city" id="city" class="input whole-length" placeholder="例）東五城字備前">
                        <div class="error-msg city-error">この項目は必須項目です。</div>
                    </li>
                    <li class="vertical-wrapper input-item">
                        <label class="required" for="street">番地</label>
                        <input type="text" name="street" id="street" class="input whole-length" placeholder="例）23番地４">
                        <div class="error-msg street-error">この項目は必須項目です。</div>
                    </li>
                    <li class="vertical-wrapper input-item">
                        <input type="text" name="building-number" id="building-number" class="input whole-length" placeholder="アパート名、棟名、部屋番号など(オプション)">
                        <!-- <div class="error-msg building-number-error">この項目は必須項目です。</div> -->
                    </li>
                    <li class="vertical-wrapper input-item">
                        <label class="" for="phone">電話番号</label>
                        <input type="text" name="phone" id="phone" class="input whole-length" placeholder="例）050-1742-3631">
                        <!-- <div class="error-msg phone-error">この項目は必須項目です。</div> -->
                    </li>
                    <li class="vertical-wrapper input-item">
                        <label class="required" for="email">メールアドレス</label>
                        <input type="email" name="email" id="email" class="input whole-length" placeholder="例）mail@example.com">
                        <div class="error-msg email-error">この項目は必須項目です。</div>
                    </li>
                </ul>

                <div class="other-address-label">
                    <input type="checkbox" id="other-addr" name="other-addr" class="other-addr">
                    <label for="other-addr">別の住所へ配送しますか?</label>
                </div>

                <ul class="vertical-wrapper other-profile-info">
                    <li class="horizontal-wrapper">
                        <div class="vertical-wrapper input-item half-width">
                            <label class="required" for="other-lastname">氏</label>
                            <input type="text" name="other-lastname" id="other-lastname" class="input half-length" placeholder="例）鈴木">
                            <div class="error-msg other-lastname-error">この項目は必須項目です。</div>
                        </div>
                        <div class="vertical-wrapper input-item half-width">
                            <label class="required" for="other-firstname">名</label>
                            <input type="text" name="other-firstname" id="other-firstname" class="input half-length" placeholder="例）太郎">
                            <div class="error-msg other-firstname-error">この項目は必須項目です。</div>
                        </div>
                    </li>
                    <li class="vertical-wrapper input-item">
                        <label class="required" for="other-country">国または地域</label>
                        <input type="text" name="other-country" id="other-country" class="input whole-length" placeholder="例）日本">
                        <div class="error-msg other-country-error">この項目は必須項目です。</div>
                    </li>
                    <li class="horizontal-wrapper">
                        <div class="vertical-wrapper input-item half-width">
                            <label class="required" for="other-post-code">郵便番号</label>
                            <input type="text" name="other-post-code" id="other-post-code" class="input half-length" placeholder="例）123-4567">
                            <div class="error-msg other-post-code-error">この項目は必須項目です。</div>
                        </div>
                        <div class="vertical-wrapper input-item half-width">
                            <label class="required" for="other-province">都道府県</label>
                            <input type="text" name="other-province" id="other-province" class="input half-length" placeholder="例）東京都">
                            <div class="error-msg other-province-error">この項目は必須項目です。</div>
                        </div>
                    </li>
                    <li class="vertical-wrapper input-item">
                        <label class="required" for="other-city">市区町村</label>
                        <input type="text" name="other-city" id="other-city" class="input whole-length" placeholder="例）東五城字備前">
                        <div class="error-msg other-city-error">この項目は必須項目です。</div>
                    </li>
                    <li class="vertical-wrapper input-item">
                        <label class="required" for="other-street">番地</label>
                        <input type="text" name="other-street" id="other-street" class="input whole-length" placeholder="例）23番地４">
                        <div class="error-msg other-street-error">この項目は必須項目です。</div>
                    </li>
                    <li class="vertical-wrapper input-item">
                        <input type="text" name="other-building-number" id="other-building-number" class="input whole-length" placeholder="アパート名、棟名、部屋番号など(オプション)">
                        <!-- <div class="error-msg other-building-number">この項目は必須項目です。</div> -->
                    <li class="vertical-wrapper input-item">
                        <label class="" for="other-phone">電話番号</label>
                        <input type="text" name="other-phone" id="other-phone" class="input whole-length" placeholder="例）050-1742-3631">
                    </li>
                    <li class="vertical-wrapper input-item">
                        <label class="required" for="other-email">メールアドレス</label>
                        <input type="email" name="other-email" id="other-email" class="input whole-length" placeholder="例）mail@example.com">
                        <div class="error-msg other-email-error">この項目は必須項目です。</div>
                    </li>
                </ul>

                <div class="card-list-wrapper">
                    <div class="head-label">【お届けするカード情報】</div>
                    <div class="ncard-wrapper">
                    </div>
                    <div class="card-list">
                    </div>
                </div>

                <div class="horizontal-wrapper input-button">
                    <div class="sending-btn-wrapper">
                        <button id="submit-btn" class="btn submit-btn">送信する</button>
                    </div>
                    <a class="btn btn-cancel" href="https://t-card.shop/my-account/cards_list">キャンセルする（保有カードに戻ります）</a>
                </div>
            </div>
        <?php else : ?>
            <div class="no-items">この機能を利用するには、ログインする必要があります。</div>
        <?php endif; ?>
    </div>

</main>

<script type="text/javascript">
    !(function($) {
        $(document).ready(function() {

            <?php $ncard = count($results); ?>

            $('#lastname').val('<?php echo $last_name; ?>');
            $('#firstname').val('<?php echo $first_name; ?>');
            $('#country').val('<?php echo $country; ?>');
            $('#lastname').val('<?php echo $last_name; ?>');
            $('#post-code').val('<?php echo $post_code; ?>');
            $('#province').val('<?php echo $province; ?>');
            $('#city').val('<?php echo $city; ?>');
            $('#street').val('<?php echo $address1; ?>');
            $('#building-number').val('<?php echo $address2; ?>');
            $('#phone').val('<?php echo $phone; ?>');
            $('#email').val('<?php echo $email; ?>');

            console.log('---------');
            var postid = getUrlParameter('postid')
            $('#agency_email').val('<?php echo $agency_email; ?>');
            console.log($('#agency_email').val());

            let ncard_element =
                '<div class="ncard-label">発送するカード</div>' +
                '<input type="text" name="ncard" class="ncard" value="<?php echo $ncard; ?>枚" readonly>';
            $('.ncard-wrapper').append(ncard_element);

            var element = '<textarea name="cards-info" class="cards-info" readonly>';
            <?php $i = 0; ?>
            <?php foreach ($results as $result) : ?>
                <?php if ($i < $ncard) : ?>
                    element = element + '<?php echo $result['card_title']; ?>            <?php echo $result['card_price']; ?>PT&#10';
                <?php else : ?>
                    element = element + '<?php echo $result['card_title']; ?>            <?php echo $result['card_price']; ?>PT';
                <?php endif; ?>
                <?php $i++; ?>
            <?php endforeach; ?>
            element = element + '</textarea>';
            $('.card-list').append(element);

            // $('.cards-info').style.height = 'auto';
            // $('.cards-info').style.height = ($('.cards-info').scrollHeight) + 'px';
            $('.cards-info').css('height', 'auto');
            $('.cards-info').css('height', $('.cards-info').prop('scrollHeight') + 'px');

            $('#other-addr').change(function() {
                if ($(this).is(':checked')) {
                    $('.other-profile-info').show();
                    $('.other-profile-info').css('display', 'flex');
                } else {
                    $('.other-profile-info').hide();
                }
            });

            ///////////////////////////////2024.02.12 dron417///////////////////////////////////
            var spinner = '<div class="lds-spinner-wrapper"><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>';
            $('body').append(spinner);

            ///////////////////////////////2024.02.26 dron417///////////////////////////////////
            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

        });

        //alert
        // https://codepen.io/Aladini/pen/NbbQPL
        /* file */
        "function" != typeof Object.create && (Object.create = function(t) {
                function o() {}
                return o.prototype = t, new o
            }),
            function(t, o) {
                "use strict";
                var i = {
                    _positionClasses: ["bottom-left", "bottom-right", "top-right", "top-left", "bottom-center", "top-center", "mid-center"],
                    _defaultIcons: ["success", "error", "info", "warning"],
                    init: function(o) {
                        this.prepareOptions(o, t.toast.options), this.process()
                    },
                    prepareOptions: function(o, i) {
                        var s = {};
                        "string" == typeof o || o instanceof Array ? s.text = o : s = o, this.options = t.extend({}, i, s)
                    },
                    process: function() {
                        this.setup(), this.addToDom(), this.position(), this.bindToast(), this.animate()
                    },
                    setup: function() {
                        var o = "";
                        if (this._toastEl = this._toastEl || t("<div></div>", {
                                "class": "jq-toast-single"
                            }), o += '<span class="jq-toast-loader"></span>', this.options.allowToastClose && (o += '<span class="close-jq-toast-single">&times;</span>'), this.options.text instanceof Array) {
                            this.options.heading && (o += '<h2 class="jq-toast-heading">' + this.options.heading + "</h2>"), o += '<ul class="jq-toast-ul">';
                            for (var i = 0; i < this.options.text.length; i++) o += '<li class="jq-toast-li" id="jq-toast-item-' + i + '">' + this.options.text[i] + "</li>";
                            o += "</ul>"
                        } else this.options.heading && (o += '<h2 class="jq-toast-heading">' + this.options.heading + "</h2>"), o += this.options.text;
                        this._toastEl.html(o), this.options.bgColor !== !1 && this._toastEl.css("background-color", this.options.bgColor), this.options.textColor !== !1 && this._toastEl.css("color", this.options.textColor), this.options.textAlign && this._toastEl.css("text-align", this.options.textAlign), this.options.icon !== !1 && (this._toastEl.addClass("jq-has-icon"), -1 !== t.inArray(this.options.icon, this._defaultIcons) && this._toastEl.addClass("jq-icon-" + this.options.icon))
                    },
                    position: function() {
                        "string" == typeof this.options.position && -1 !== t.inArray(this.options.position, this._positionClasses) ? "bottom-center" === this.options.position ? this._container.css({
                            left: t(o).outerWidth() / 2 - this._container.outerWidth() / 2,
                            bottom: 20
                        }) : "top-center" === this.options.position ? this._container.css({
                            left: t(o).outerWidth() / 2 - this._container.outerWidth() / 2,
                            top: 20
                        }) : "mid-center" === this.options.position ? this._container.css({
                            left: t(o).outerWidth() / 2 - this._container.outerWidth() / 2,
                            top: t(o).outerHeight() / 2 - this._container.outerHeight() / 2
                        }) : this._container.addClass(this.options.position) : "object" == typeof this.options.position ? this._container.css({
                            top: this.options.position.top ? this.options.position.top : "auto",
                            bottom: this.options.position.bottom ? this.options.position.bottom : "auto",
                            left: this.options.position.left ? this.options.position.left : "auto",
                            right: this.options.position.right ? this.options.position.right : "auto"
                        }) : this._container.addClass("bottom-left")
                    },
                    bindToast: function() {
                        var t = this;
                        this._toastEl.on("afterShown", function() {
                            t.processLoader()
                        }), this._toastEl.find(".close-jq-toast-single").on("click", function(o) {
                            o.preventDefault(), "fade" === t.options.showHideTransition ? (t._toastEl.trigger("beforeHide"), t._toastEl.fadeOut(function() {
                                t._toastEl.trigger("afterHidden")
                            })) : "slide" === t.options.showHideTransition ? (t._toastEl.trigger("beforeHide"), t._toastEl.slideUp(function() {
                                t._toastEl.trigger("afterHidden")
                            })) : (t._toastEl.trigger("beforeHide"), t._toastEl.hide(function() {
                                t._toastEl.trigger("afterHidden")
                            }))
                        }), "function" == typeof this.options.beforeShow && this._toastEl.on("beforeShow", function() {
                            t.options.beforeShow()
                        }), "function" == typeof this.options.afterShown && this._toastEl.on("afterShown", function() {
                            t.options.afterShown()
                        }), "function" == typeof this.options.beforeHide && this._toastEl.on("beforeHide", function() {
                            t.options.beforeHide()
                        }), "function" == typeof this.options.afterHidden && this._toastEl.on("afterHidden", function() {
                            t.options.afterHidden()
                        })
                    },
                    addToDom: function() {
                        var o = t(".jq-toast-wrap");
                        if (0 === o.length ? (o = t("<div></div>", {
                                "class": "jq-toast-wrap"
                            }), t("body").append(o)) : (!this.options.stack || isNaN(parseInt(this.options.stack, 10))) && o.empty(), o.find(".jq-toast-single:hidden").remove(), o.append(this._toastEl), this.options.stack && !isNaN(parseInt(this.options.stack), 10)) {
                            var i = o.find(".jq-toast-single").length,
                                s = i - this.options.stack;
                            s > 0 && t(".jq-toast-wrap").find(".jq-toast-single").slice(0, s).remove()
                        }
                        this._container = o
                    },
                    canAutoHide: function() {
                        return this.options.hideAfter !== !1 && !isNaN(parseInt(this.options.hideAfter, 10))
                    },
                    processLoader: function() {
                        if (!this.canAutoHide() || this.options.loader === !1) return !1;
                        var t = this._toastEl.find(".jq-toast-loader"),
                            o = (this.options.hideAfter - 400) / 1e3 + "s",
                            i = this.options.loaderBg,
                            s = t.attr("style") || "";
                        s = s.substring(0, s.indexOf("-webkit-transition")), s += "-webkit-transition: width " + o + " ease-in;                       -o-transition: width " + o + " ease-in;                       transition: width " + o + " ease-in;                       background-color: " + i + ";", t.attr("style", s).addClass("jq-toast-loaded")
                    },
                    animate: function() {
                        var t = this;
                        if (this._toastEl.hide(), this._toastEl.trigger("beforeShow"), "fade" === this.options.showHideTransition.toLowerCase() ? this._toastEl.fadeIn(function() {
                                t._toastEl.trigger("afterShown")
                            }) : "slide" === this.options.showHideTransition.toLowerCase() ? this._toastEl.slideDown(function() {
                                t._toastEl.trigger("afterShown")
                            }) : this._toastEl.show(function() {
                                t._toastEl.trigger("afterShown")
                            }), this.canAutoHide()) {
                            var t = this;
                            o.setTimeout(function() {
                                "fade" === t.options.showHideTransition.toLowerCase() ? (t._toastEl.trigger("beforeHide"), t._toastEl.fadeOut(function() {
                                    t._toastEl.trigger("afterHidden")
                                })) : "slide" === t.options.showHideTransition.toLowerCase() ? (t._toastEl.trigger("beforeHide"), t._toastEl.slideUp(function() {
                                    t._toastEl.trigger("afterHidden")
                                })) : (t._toastEl.trigger("beforeHide"), t._toastEl.hide(function() {
                                    t._toastEl.trigger("afterHidden")
                                }))
                            }, this.options.hideAfter)
                        }
                    },
                    reset: function(o) {
                        "all" === o ? t(".jq-toast-wrap").remove() : this._toastEl.remove()
                    },
                    update: function(t) {
                        this.prepareOptions(t, this.options), this.setup(), this.bindToast()
                    }
                };
                t.toast = function(t) {
                    var o = Object.create(i);
                    return o.init(t, this), {
                        reset: function(t) {
                            o.reset(t)
                        },
                        update: function(t) {
                            o.update(t)
                        }
                    }
                }, t.toast.options = {
                    text: "",
                    heading: "",
                    showHideTransition: "fade",
                    allowToastClose: !0,
                    hideAfter: 3e3,
                    loader: !0,
                    loaderBg: "#9EC600",
                    stack: 5,
                    position: "bottom-left",
                    bgColor: !1,
                    textColor: !1,
                    textAlign: "left",
                    icon: !1,
                    beforeShow: function() {},
                    afterShown: function() {},
                    beforeHide: function() {},
                    afterHidden: function() {}
                }
            }(jQuery, window, document);

        /* Starts from here */
        $("#error").click(function() {
            $.toast({
                heading: 'Error',
                text: 'Try again!',
                icon: 'error',
                loader: true,
                loaderBg: '#fff',
                showHideTransition: 'plain',
                hideAfter: 4000,
                position: {
                    left: 100,
                    top: 30
                }
            })
        })

        $("#success").click(function() {
            $.toast({
                heading: 'Success',
                text: 'Logged In',
                icon: 'success',
                loader: true,
                loaderBg: '#fff',
                showHideTransition: 'fade',
                hideAfter: 4000,
                allowToastClose: false,
                position: {
                    left: 100,
                    top: 30
                }
            })
        })

        $("#info").click(function() {
            $.toast({
                heading: 'Info',
                text: 'Important information',
                icon: 'info',
                loader: true,
                loaderBg: '#fff',
                showHideTransition: 'slide',
                hideAfter: 4000,
                allowToastClose: false,
                position: {
                    left: 100,
                    top: 30
                }
            })
        })

        $("#warning").click(function() {
            $.toast({
                heading: 'Warning',
                text: 'You cant do that!',
                icon: 'warning',
                loader: false,
                hideAfter: false,
                allowToastClose: true,
                position: {
                    left: 100,
                    top: 30
                }
            })
        })
    })(jQuery);
</script>

<?php get_footer(); ?>