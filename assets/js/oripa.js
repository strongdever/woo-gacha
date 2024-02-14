!(function ($) {
    "use strict";

    $(document).ready(function () {
        // $('.btn-1').off('click').on('click', function () {
        //     var gacha_id = $(this).parent().data('id');
        //     var points_balance = $(this).parent().data('pointbalance');
        //     var gacha_price = $(this).parent().data('price');
        //     create_modal(gacha_id, 1, points_balance, gacha_price);
        //     $(".mask").addClass("active");
        // });

        var f_ok = false;

        $('.btn-gacha').off('click').on('click', function () {
            if (f_ok) {
                return;
            }

            var user_id = $(this).parent().data('userid');
            var gacha_id = $(this).parent().data('id');
            var points_balance = $(this).parent().data('pointbalance');
            var gacha_price = $(this).parent().data('price');
            var current_ncard = $(this).parent().data('currentncard');
            var number = $(this).data('number');
            if (current_ncard < 1) {
                $.toast({
                    heading: '通知',
                    text: 'このガチャは終了しました。',
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
                return false;
            }
            create_modal(user_id, gacha_id, number, points_balance, gacha_price);
            $(".mask").addClass("active");
        });

        function create_modal(user_id, post_id, number, points_balance, gacha_price) {
            var modal_content =
                '<div class="mask" role="dialog">' +
                '</div>' +
                '<div class="modal" role="alert">' +
                '<button class="close" role="button">X</button>' +
                '<div class="notice-title">アセロラ降臨！サポぶち抜きガチ</div>' +
                '<p class="description">' +
                'コインを消費してガチャを' + number + '回引きます。<br>' +
                'よろしいですか？' +
                '</p>' +
                '<div class="coin-numbers">' +
                '<div class="current-coin coin-number">' +
                '<img class="coin-img" src="https://t-card.shop/wp-content/themes/astra-child/assets/img/coin.png">' +
                '<div class="label">現在のコイン数 : </div>' +
                '<div class="current-coin-number n-card">' + points_balance + '</div>' +
                '<span class="unit-label">coin</span>' +
                '</div>' +
                '<div class="after-coin coin-number">' +
                '<img class="coin-img" src="https://t-card.shop/wp-content/themes/astra-child/assets/img/coin.png">' +
                '<div class="label">引いた後のコイン数 : </div>' +
                '<div class="after-coin-number n-card">' + (points_balance - gacha_price * number) + '</div>' +
                '<span class="unit-label">coin</span>' +
                '</div>' +
                '</div>' +
                '<a class="btn-ok" data-userid="' + user_id + '" data-id="' + post_id + '" data-number="' + number + '" data-ptbalance="' + points_balance + '" data-afterbalance="' + (points_balance - gacha_price * number) + '">ガチャを引く</a>' +
                '<button class="btn-cancel">キャンセル</button>' +
                '</div>';

            if ($('.modal-wrapper').children('.modal').length > 0) {
                $('.modal-wrapper').children('.mask').remove()
                $('.modal-wrapper').children('.modal').remove();
            }
            $('.modal-wrapper').append(modal_content);

            $('.btn-ok').off('click').on('click', function () {
                // closeModal();
                console.log(f_ok);
                if (f_ok) {
                    return;
                }
                f_ok = true;
                var user_id = $(this).data('userid');
                var gacha_id = $(this).data('id');
                var number = $(this).data('number');
                var pt_balance = $(this).data('ptbalance');
                var after_balance = $(this).data('afterbalance');
                if (user_id == 0) { //not logged in
                    $.toast({
                        heading: '通知',
                        text: 'この機能を利用するにはログインが必要です。',
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
                    });
                    closeModal();
                    return 0;
                }

                if (parseInt(after_balance) < 0) {   //not enough coin
                    $.toast({
                        heading: '通知',
                        text: 'コインが足りません',
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
                    });
                    closeModal();
                    return 0;
                }

                Processing_Gacha(gacha_id, number); //proccessing gacha
            });
            // Call the closeModal function on the clicks/keyboard
            $(".close, .mask").on("click", function () {
                closeModal();
            });

            $(".btn-cancel").off('click').on('click', function () {
                closeModal();
            });
        }

        function Processing_Gacha(gacha_id, number) {
            async_Request1(gacha_id, number);
        }

        var spinner = '<div class="lds-spinner-wrapper"><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>';
        $('body').append(spinner);

        function async_Request1(post_id, number) {
            $(".lds-spinner").show();

            var data = {
                entry: 'pulling gacha',
                post_id: post_id,
                number: number
            }
            // var json_data = JSON.stringify(data);
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'my_ajax_action1',
                    my_data: data
                },
                success: function (response) {
                    // Handle the response
                    var result = response.data['result'];
                    var winning_card = response.data['winning_card'];
                    var video_url = response.data['video_url'];
                    console.log(winning_card);
                    console.log(video_url);
                    console.log(result);

                    $(".lds-spinner").hide();   //hide the spinner

                    closeModal();   //close the modal

                    if (result != 'success') {   //display alert message in case of faliled request
                        $.toast({
                            heading: '通知',
                            text: result,
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
                        // alert(result);
                        return;
                    }

                    //display the video
                    var video_content =
                        '<div class="video-wrapper">' +
                        '<video class="video-screen" autoplay>' +
                        '<source src="' + video_url + '" type="video/mp4">' +
                        '</video>' +
                        '</div>';
                    $('body').append(video_content);
                    $('.video-wrapper').css('opacity', 1);
                    var video = $(".video-screen")[0];

                    video.onended = function () {
                        var url = "https://t-card.shop/card-list";
                        window.location.href = url;
                    };
                },
                error: function (response) {
                    $(".lds-spinner").hide();   //hide the spinner
                    closeModal();   //close the modal
                    // alert('サーバーとの通信中にエラーが発生しました。もう一度お試しください。');
                    $.toast({
                        heading: 'ガチャ失敗',
                        text: 'サーバーとの通信中にエラーが発生しました。申し訳ありませんが、もう一度お試しください。',
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
                    return;
                }
            });
        }

        // Function for close the Modal

        function closeModal() {
            $(".mask").removeClass("active");
        }

        $(document).keyup(function (e) {
            if (e.keyCode == 27) {
                closeModal();
            }
        });

        /////////////////////////////////////////////////////////////////////////////////////
        $('.btn-detail').click(function(event) {
            if (f_ok) {
                event.preventDefault();
                return;
            }
        })

        /////////////////////////////////////////////////////////////////////////////////////

        $('.btn-submit').off('click').on('click', function () {
            console.log("submit");
        })

        $('.btn-coin').off('click').on('click', function () {   //まとめてptに変える
            if (f_ok) {
                return;
            }

            var arry_cards = get_selected_cards();
            console.log(arry_cards);

            var number = arry_cards.length;
            if (number == 0) {
                $.toast({
                    heading: '通知',
                    text: 'カードを選択してください',
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
                });
                return 0;
            }
            var userid = $(this).data('userid');
            var current_balance = $(this).data('currentbalance');
            var next_balance = current_balance + get_selected_cards_price();
            var modal_content =
                '<div class="mask" role="dialog">' +
                '</div>' +
                '<div class="modal" role="alert">' +
                '<button class="close" role="button">X</button>' +
                '<div class="notice-title">カードをCoinに替える</div>' +
                '<p class="description">選択した保有カードをマイコインに還元します。<br>よろしいですか？</p>' +
                '<div class="coin-numbers">' +
                '<div class="current-coin coin-number">' +
                '<img class="coin-img" src="https://t-card.shop/wp-content/themes/astra-child/assets/img/coin.png">' +
                '<div class="label">現在のコイン数 : </div>' +
                '<div class="current-coin-number n-card">' + current_balance + '</div>' +
                '<span class="unit-label">coin</span>' +
                '</div>' +
                '<div class="after-coin coin-number">' +
                '<img class="coin-img" src="https://t-card.shop/wp-content/themes/astra-child/assets/img/coin.png">' +
                '<div class="label">還元した後のコイン数 : </div>' +
                '<div class="after-coin-number n-card">' + next_balance + '</div>' +
                '<span class="unit-label">coin</span>' +
                '</div>' +
                '</div>' +
                '<div class="btns">' +
                '<button class="btn-store-coin btn-item" data-userid="' + userid + '" data-number="' + number + '">ポイントに還元する</button>' +
                '<button class="btn-cancel btn-item">キャンセル</button>' +
                '</div>' +
                '</div>';

            if ($('.modal-wrapper').children('.modal').length > 0) {
                $('.modal-wrapper').children('.mask').remove()
                $('.modal-wrapper').children('.modal').remove();
            }
            $('.modal-wrapper').append(modal_content);
            $(".mask").addClass("active");

            $('.btn-store-coin').off('click').on('click', function () {
                // closeModal();
                console.log(f_ok);
                if (f_ok) {
                    return;
                }
                f_ok = true;
                var userid = $(this).data('userid');
                var number = $(this).data('number');
                if (userid == 0) {
                    $.toast({
                        heading: '通知',
                        text: 'この機能を利用するにはログインが必要です。',
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
                    });
                    closeModal();
                    return 0;
                }

                Card_to_Point(arry_cards);
            });

            // Call the closeModal function on the clicks/keyboard
            $(".close, .mask").on("click", function () {
                closeModal();
            });
            $(".btn-cancel").off('click').on('click', function () {
                closeModal();
            });
        });

        $('.btn-single-refunds').off('click').on('click', function () {   //まとめてptに変える
            if (f_ok) {
                return;
            }

            $('.card-id').prop('checked', false);
            $('.btn-check-all').prop('checked', false);
            $(this).parent().parent().parent().find('.input-wrapper .card-id').prop('checked', true);

            var arry_cards = get_selected_cards();
            console.log(arry_cards);

            var number = arry_cards.length;
            if (number == 0) {
                $.toast({
                    heading: '通知',
                    text: 'カードを選択してください',
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
                });
                return 0;
            }
            var userid = $(this).data('userid');
            var current_balance = $(this).data('currentbalance');
            var next_balance = current_balance + get_selected_cards_price();
            var modal_content =
                '<div class="mask" role="dialog">' +
                '</div>' +
                '<div class="modal" role="alert">' +
                '<button class="close" role="button">X</button>' +
                '<div class="notice-title">カードをCoinに替える</div>' +
                '<p class="description">選択した保有カードをマイコインに還元します。<br>よろしいですか？</p>' +
                '<div class="coin-numbers">' +
                '<div class="current-coin coin-number">' +
                '<img class="coin-img" src="https://t-card.shop/wp-content/themes/astra-child/assets/img/coin.png">' +
                '<div class="label">現在のコイン数 : </div>' +
                '<div class="current-coin-number n-card">' + current_balance + '</div>' +
                '<span class="unit-label">coin</span>' +
                '</div>' +
                '<div class="after-coin coin-number">' +
                '<img class="coin-img" src="https://t-card.shop/wp-content/themes/astra-child/assets/img/coin.png">' +
                '<div class="label">還元した後のコイン数 : </div>' +
                '<div class="after-coin-number n-card">' + next_balance + '</div>' +
                '<span class="unit-label">coin</span>' +
                '</div>' +
                '</div>' +
                '<div class="btns">' +
                '<button class="btn-store-coin btn-item" data-userid="' + userid + '" data-number="' + number + '">ポイントに還元する</button>' +
                '<button class="btn-cancel btn-item">キャンセル</button>' +
                '</div>' +
                '</div>';

            if ($('.modal-wrapper').children('.modal').length > 0) {
                $('.modal-wrapper').children('.mask').remove()
                $('.modal-wrapper').children('.modal').remove();
            }
            $('.modal-wrapper').append(modal_content);
            $(".mask").addClass("active");

            $('.btn-store-coin').off('click').on('click', function () {
                // closeModal();
                console.log(f_ok);
                if (f_ok) {
                    return;
                }
                f_ok = true;
                var userid = $(this).data('userid');
                var number = $(this).data('number');
                if (userid == 0) {
                    $.toast({
                        heading: '通知',
                        text: 'この機能を利用するにはログインが必要です。',
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
                    });
                    closeModal();
                    return 0;
                }

                Card_to_Point(arry_cards);
            });

            // Call the closeModal function on the clicks/keyboard
            $(".close, .mask").on("click", function () {
                closeModal();
            });
            $(".btn-cancel").off('click').on('click', function () {
                closeModal();
            });
        });

        function Card_to_Point(card_ids) {
            async_Request2(card_ids);
        }

        function async_Request2(card_ids) {
            $(".lds-spinner").show();

            var data = {
                entry: 'store coin',
                card_ids: card_ids
            }
            // var json_data = JSON.stringify(data);

            var number = card_ids.length;
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'my_ajax_action2',
                    my_data: data
                },
                success: function (response) {
                    // Handle the response
                    var result = response.data['result'];
                    console.log(result);

                    $(".lds-spinner").hide();   //hide the spinner

                    closeModal();   //close the modal

                    if (result == 'success') {
                        $.toast({
                            heading: 'コイン替え成功',
                            text: number + '枚のカードをコインに替えました。',
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
                    }
                    else {   //display alert message in case of faliled request
                        $.toast({
                            heading: '通知',
                            text: result,
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
                        // alert(result);
                        return;
                    }

                    var url = "https://t-card.shop/my-account/cards_list/";
                    window.location.href = url;
                },
                error: function (response) {
                    $(".lds-spinner").hide();   //hide the spinner
                    closeModal();   //close the modal
                    // alert('サーバーとの通信中にエラーが発生しました。もう一度お試しください。');
                    $.toast({
                        heading: 'コイン替え失敗',
                        text: 'サーバーとの通信中にエラーが発生しました。申し訳ありませんが、もう一度お試しください。',
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
                    return;
                }
            });
        }

        $('.btn-single-send').off('click').on('click', function () {   //商品を発送する
            if (f_ok) {
                return;
            }

            $('.card-id').prop('checked', false);
            $('.btn-check-all').prop('checked', false);
            $(this).parent().parent().parent().find('.input-wrapper .card-id').prop('checked', true);

            var arry_cards = get_selected_cards();
            console.log(arry_cards);

            var number = arry_cards.length;
            var userid = $(this).data('userid');

            show_modal(number, userid, arry_cards);
        });

        $('.btn-card-sending').off('click').on('click', function () {   //商品を発送する
            if (f_ok) {
                return;
            }

            var arry_cards = get_selected_cards();
            console.log(arry_cards);

            var number = arry_cards.length;
            var userid = $(this).data('userid');

            show_modal(number, userid, arry_cards);
        });

        function show_modal(number, userid, arry_cards) {
            if (number == 0) {
                $.toast({
                    heading: '通知',
                    text: 'カードを選択してください',
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
                });
                return 0;
            }

            var modal_content =
                '<div class="mask" role="dialog">' +
                '</div>' +
                '<div class="modal" role="alert">' +
                '<button class="close" role="button">X</button>' +
                '<div class="notice-title">カード発送</div>' +
                '<p class="description">選択した保有カードを発送致します。<br>よろしいですか？<br>発送の手続きをお願いします。</p>' +
                '<div class="btns">' +
                '<button class="btn-sending btn-item" data-userid="' + userid + '" data-number="' + number + '">カードを発送する</button>' +
                '<button class="btn-cancel btn-item">キャンセル</button>' +
                '</div>' +
                '</div>';

            if ($('.modal-wrapper').children('.modal').length > 0) {
                $('.modal-wrapper').children('.mask').remove();
                $('.modal-wrapper').children('.modal').remove();
            }
            $('.modal-wrapper').append(modal_content);
            $(".mask").addClass("active");


            $('.btn-sending').off('click').on('click', function () {
                // closeModal();
                console.log(f_ok);
                if (f_ok) {
                    return;
                }
                f_ok = true;
                var userid = $(this).data('userid');
                var number = $(this).data('number');
                if (userid == 0) {
                    $.toast({
                        heading: '通知',
                        text: 'この機能を利用するにはログインが必要です。',
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
                    });
                    closeModal();
                    return 0;
                }

                jump_SendingPage(arry_cards, number);
            });

            // Call the closeModal function on the clicks/keyboard
            $(".close, .mask").on("click", function () {
                closeModal();
            });
            $(".btn-cancel").off('click').on('click', function () {
                closeModal();
            });
        }

        function jump_SendingPage(arry_cards, number) {
            var url = "https://t-card.shop/card-sending/?ncards=" + number + "&card_ids=";
            $.each(arry_cards, function (index, value) {
                // Code to be executed for each array item
                if (index < number - 1) {
                    url = url + value + "-";
                } else {
                    url = url + value;
                }
            });
            window.location.href = url;
        }

        ///////////////////////////////2024.02.12 dron417///////////////////////////////////
        $('.submit-btn').click(function () { //送信する button of card-sending page
            var cardIds = getUrlParam('card_ids');
            var array_cardIDs = cardIds.split("-");

            console.log(array_cardIDs);

            async_delete_cards(array_cardIDs);
        })

        function async_delete_cards(cardIds) {
            $(".lds-spinner").show();

            var data = {
                card_ids: cardIds
            }
            // var json_data = JSON.stringify(data);
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'my_ajax_action3',
                    my_data: data
                },
                success: function (response) {
                    // Handle the response
                    var result = response.data['result'];
                    console.log(result);

                    $(".lds-spinner").hide(); //hide the spinner

                    if (result != 'success') { //display alert message in case of faliled request
                        $.toast({
                            heading: '通知',
                            text: result,
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
                        // alert(result);
                        return;
                    }

                    var url = "https://t-card.shop/my-account/cards_list/";
                    window.location.href = url;
                },
                error: function (response) {
                    $(".lds-spinner").hide(); //hide the spinner
                    // alert('サーバーとの通信中にエラーが発生しました。もう一度お試しください。');
                    $.toast({
                        heading: '送信失敗',
                        text: 'サーバーとの通信中にエラーが発生しました。申し訳ありませんが、もう一度お試しください。',
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
                    return;
                }
            });
        }

        function getUrlParam(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        $('.btn-oripa').click(function() {
            if (f_ok) {
                return;
            }

            window.location.href="https://t-card.shop/oripa/";
        })

        ////////////////////////////////////////////////////////////////////////////
        $('.card-wrapper').on('click', '.img-wrapper .lightbox', function () {  //lightbox
            var imgsrc = $(this).attr('src');
            $("body").append("<div class='img-popup'><div class='img-wrapper'><img src='" + imgsrc + "'></div></div>");
            $(".close-lightbox, .img-popup").click(function () {
                $(".img-popup").fadeOut(500, function () {
                    $(this).remove();
                }).addClass("lightboxfadeout");
            });

        });
        $(".lightbox").click(function () {
            $(".img-popup").fadeIn(500);
        });

        /////////////////////////////////////////////////////////////////////////////////////
        $('#check-all').change(function () {       //すべて選択 button (all checkboxes)
            if ($(this).is(':checked')) {
                $('.card-id').prop('checked', true);
            } else {
                $('.card-id').prop('checked', false);
            }
        });

        function get_selected_cards() {
            var checkedCheckboxes = $('.card-id:checked');
            var arry_cards = [];
            checkedCheckboxes.each(function () {
                var card_id = $(this).data('cardid');
                arry_cards.push(card_id);
            });

            return arry_cards;
        }

        function get_selected_cards_price() {
            var checkedCheckboxes = $('.card-id:checked');
            var cards_price = 0;
            checkedCheckboxes.each(function () {
                cards_price = cards_price + $(this).data('cardprice');
            });

            return cards_price;
        }
    });

})(jQuery);











//alert
// https://codepen.io/Aladini/pen/NbbQPL
/* file */
"function" != typeof Object.create && (Object.create = function (t) { function o() { } return o.prototype = t, new o }), function (t, o) { "use strict"; var i = { _positionClasses: ["bottom-left", "bottom-right", "top-right", "top-left", "bottom-center", "top-center", "mid-center"], _defaultIcons: ["success", "error", "info", "warning"], init: function (o) { this.prepareOptions(o, t.toast.options), this.process() }, prepareOptions: function (o, i) { var s = {}; "string" == typeof o || o instanceof Array ? s.text = o : s = o, this.options = t.extend({}, i, s) }, process: function () { this.setup(), this.addToDom(), this.position(), this.bindToast(), this.animate() }, setup: function () { var o = ""; if (this._toastEl = this._toastEl || t("<div></div>", { "class": "jq-toast-single" }), o += '<span class="jq-toast-loader"></span>', this.options.allowToastClose && (o += '<span class="close-jq-toast-single">&times;</span>'), this.options.text instanceof Array) { this.options.heading && (o += '<h2 class="jq-toast-heading">' + this.options.heading + "</h2>"), o += '<ul class="jq-toast-ul">'; for (var i = 0; i < this.options.text.length; i++)o += '<li class="jq-toast-li" id="jq-toast-item-' + i + '">' + this.options.text[i] + "</li>"; o += "</ul>" } else this.options.heading && (o += '<h2 class="jq-toast-heading">' + this.options.heading + "</h2>"), o += this.options.text; this._toastEl.html(o), this.options.bgColor !== !1 && this._toastEl.css("background-color", this.options.bgColor), this.options.textColor !== !1 && this._toastEl.css("color", this.options.textColor), this.options.textAlign && this._toastEl.css("text-align", this.options.textAlign), this.options.icon !== !1 && (this._toastEl.addClass("jq-has-icon"), -1 !== t.inArray(this.options.icon, this._defaultIcons) && this._toastEl.addClass("jq-icon-" + this.options.icon)) }, position: function () { "string" == typeof this.options.position && -1 !== t.inArray(this.options.position, this._positionClasses) ? "bottom-center" === this.options.position ? this._container.css({ left: t(o).outerWidth() / 2 - this._container.outerWidth() / 2, bottom: 20 }) : "top-center" === this.options.position ? this._container.css({ left: t(o).outerWidth() / 2 - this._container.outerWidth() / 2, top: 20 }) : "mid-center" === this.options.position ? this._container.css({ left: t(o).outerWidth() / 2 - this._container.outerWidth() / 2, top: t(o).outerHeight() / 2 - this._container.outerHeight() / 2 }) : this._container.addClass(this.options.position) : "object" == typeof this.options.position ? this._container.css({ top: this.options.position.top ? this.options.position.top : "auto", bottom: this.options.position.bottom ? this.options.position.bottom : "auto", left: this.options.position.left ? this.options.position.left : "auto", right: this.options.position.right ? this.options.position.right : "auto" }) : this._container.addClass("bottom-left") }, bindToast: function () { var t = this; this._toastEl.on("afterShown", function () { t.processLoader() }), this._toastEl.find(".close-jq-toast-single").on("click", function (o) { o.preventDefault(), "fade" === t.options.showHideTransition ? (t._toastEl.trigger("beforeHide"), t._toastEl.fadeOut(function () { t._toastEl.trigger("afterHidden") })) : "slide" === t.options.showHideTransition ? (t._toastEl.trigger("beforeHide"), t._toastEl.slideUp(function () { t._toastEl.trigger("afterHidden") })) : (t._toastEl.trigger("beforeHide"), t._toastEl.hide(function () { t._toastEl.trigger("afterHidden") })) }), "function" == typeof this.options.beforeShow && this._toastEl.on("beforeShow", function () { t.options.beforeShow() }), "function" == typeof this.options.afterShown && this._toastEl.on("afterShown", function () { t.options.afterShown() }), "function" == typeof this.options.beforeHide && this._toastEl.on("beforeHide", function () { t.options.beforeHide() }), "function" == typeof this.options.afterHidden && this._toastEl.on("afterHidden", function () { t.options.afterHidden() }) }, addToDom: function () { var o = t(".jq-toast-wrap"); if (0 === o.length ? (o = t("<div></div>", { "class": "jq-toast-wrap" }), t("body").append(o)) : (!this.options.stack || isNaN(parseInt(this.options.stack, 10))) && o.empty(), o.find(".jq-toast-single:hidden").remove(), o.append(this._toastEl), this.options.stack && !isNaN(parseInt(this.options.stack), 10)) { var i = o.find(".jq-toast-single").length, s = i - this.options.stack; s > 0 && t(".jq-toast-wrap").find(".jq-toast-single").slice(0, s).remove() } this._container = o }, canAutoHide: function () { return this.options.hideAfter !== !1 && !isNaN(parseInt(this.options.hideAfter, 10)) }, processLoader: function () { if (!this.canAutoHide() || this.options.loader === !1) return !1; var t = this._toastEl.find(".jq-toast-loader"), o = (this.options.hideAfter - 400) / 1e3 + "s", i = this.options.loaderBg, s = t.attr("style") || ""; s = s.substring(0, s.indexOf("-webkit-transition")), s += "-webkit-transition: width " + o + " ease-in;                       -o-transition: width " + o + " ease-in;                       transition: width " + o + " ease-in;                       background-color: " + i + ";", t.attr("style", s).addClass("jq-toast-loaded") }, animate: function () { var t = this; if (this._toastEl.hide(), this._toastEl.trigger("beforeShow"), "fade" === this.options.showHideTransition.toLowerCase() ? this._toastEl.fadeIn(function () { t._toastEl.trigger("afterShown") }) : "slide" === this.options.showHideTransition.toLowerCase() ? this._toastEl.slideDown(function () { t._toastEl.trigger("afterShown") }) : this._toastEl.show(function () { t._toastEl.trigger("afterShown") }), this.canAutoHide()) { var t = this; o.setTimeout(function () { "fade" === t.options.showHideTransition.toLowerCase() ? (t._toastEl.trigger("beforeHide"), t._toastEl.fadeOut(function () { t._toastEl.trigger("afterHidden") })) : "slide" === t.options.showHideTransition.toLowerCase() ? (t._toastEl.trigger("beforeHide"), t._toastEl.slideUp(function () { t._toastEl.trigger("afterHidden") })) : (t._toastEl.trigger("beforeHide"), t._toastEl.hide(function () { t._toastEl.trigger("afterHidden") })) }, this.options.hideAfter) } }, reset: function (o) { "all" === o ? t(".jq-toast-wrap").remove() : this._toastEl.remove() }, update: function (t) { this.prepareOptions(t, this.options), this.setup(), this.bindToast() } }; t.toast = function (t) { var o = Object.create(i); return o.init(t, this), { reset: function (t) { o.reset(t) }, update: function (t) { o.update(t) } } }, t.toast.options = { text: "", heading: "", showHideTransition: "fade", allowToastClose: !0, hideAfter: 3e3, loader: !0, loaderBg: "#9EC600", stack: 5, position: "bottom-left", bgColor: !1, textColor: !1, textAlign: "left", icon: !1, beforeShow: function () { }, afterShown: function () { }, beforeHide: function () { }, afterHidden: function () { } } }(jQuery, window, document);

/* Starts from here */
$("#error").click(function () {
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

$("#success").click(function () {
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

$("#info").click(function () {
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

$("#warning").click(function () {
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