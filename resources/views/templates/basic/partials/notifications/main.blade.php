<style>
    .sidecart {
        width: 450px;
        min-width: 450px;
        height: auto;
        position: fixed;
        top: 0;
        overflow: auto !important;
        right: -450px;
        z-index: 999999999999999;
        transition: 0.2s ease;
        border-radius: 5px;
    }

    .sidecart ul {
        background-color: #f5d602;

    }

    .sidecart ul>ul {
        height: 100vh;
        overflow-y: auto;
    }

    @media(max-width: 400px) {
        .sidecart {
            width: 100vw;
            right: -100vw;
        }
    }

    .open-cart {
        right: 0px !important;
    }

    .open-cart:before {
        content: '';
        position: fixed;
        top: 0px;
        width: 100vw;
        height: 100vh;
        left: 0px;
        right: 0px;
        bottom: 0px;
        background-color: black;
        opacity: 0.5;
        z-index: -1;
    }

    .product-quantity {
        position: relative;
    }

    .product-quantity:after {
        content: 'x de';
        white-space: nowrap;
        position: absolute;
        z-index: 10;
        margin-left: 2px;
    }

    .sidecart-price>div {
        margin: auto;
        width: 100%;
        white-space: nowrap;
    }

    .notification {
        background-color: #fff;
        position: relative;
        padding: 10px;
        width: 400px;
        border-radius: 8px;
        box-shadow: 0 0 5px #00000033;
        cursor: pointer;
        margin-left: 10px;
        font-weight: 600;
        transition-property: top, left, opacity, transform, background-color;
        transition-duration: 100ms, 500ms, 200ms;
        transition-timing-function: ease;
        display: flex;
        gap: 11px;
        opacity: 1;
    }

    .notifyicon {
        width: 80px;
        margin-top: 30px;
        font-size: 50px;
        display: flex;
        align-items: center;
    }

    .text {
        display: flex;
        flex-direction: column;
    }

    .head {
        text-align: left
    }

    .time {
        position: absolute;
        right: 35px;
        top: 0px;
        margin-bottom: 8px;
    }

    .crossnotify {
        position: absolute;
        right: 15px;
        top: 10px;
        font-size: 16px;
        cursor: pointer;
        width: 20px;
        height: 20px;
        background-color: transparent;
        border-radius: 5px;
        transform: translateX(25%) translateY(-25%);
        transition: transform .3s;
        text-align: center;
        box-shadow: 0 0 7px 0px #00000040;
    }
</style>


<!-- Notification Modal -->

<div class="sidecart text-center">

    <ul class="nav flex-column addnotification py-3 " style="max-height: 100vh; overflow-y:hidden;flex-wrap: unset;">
        <div class="d-flex justify-content-between align-items-center">

            <div class="text-light h4 m-0 px-4 text-center mb-3 d-flex gap-3 justify-content-start align-items-center">
                Notification
                <div class="d-inline" onclick="toggleCart()"><i
                        class="far text-primary float-right fa-arrow-alt-circle-right mt-1"></i></div>
            </div>
            <div class="mark_all_read">
                <a class="activate readall">
                    <span>
                        <svg>
                            <use xlink:href="#circle">
                        </svg>
                        <svg>
                            <use xlink:href="#arrow">
                        </svg>
                        <svg>
                            <use xlink:href="#check">
                        </svg>
                    </span>
                    <div class="ul">
                        <small>Read all</small>
                        <small>Waiting</small>
                        <small>Done</small>
                    </div>
                </a>

                <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="circle">
                        <circle cx="8" cy="8" r="7.5"></circle>
                    </symbol>
                    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" id="arrow">
                        <path
                            d="M2.7008908,5.37931459 L2.7008908,5.37931459 C2.9224607,5.60207651 3.2826628,5.60304283 3.50542472,5.38147293 C3.52232305,5.36466502 3.53814843,5.34681177 3.55280728,5.32801875 L5.34805194,3.02646954 L5.34805194,10.3480519 C5.34805194,10.7081129 5.63993903,11 6,11 L6,11 C6.36006097,11 6.65194806,10.7081129 6.65194806,10.3480519 L6.65194806,3.02646954 L8.44719272,5.32801875 C8.6404327,5.57575732 8.99791646,5.61993715 9.24565503,5.42669716 C9.26444805,5.41203831 9.28230129,5.39621293 9.2991092,5.37931459 L9.2991092,5.37931459 C9.55605877,5.12098268 9.57132199,4.70855346 9.33416991,4.43193577 L6.75918715,1.42843795 C6.39972025,1.00915046 5.76841509,0.960656296 5.34912761,1.32012319 C5.31030645,1.35340566 5.27409532,1.38961679 5.24081285,1.42843795 L2.66583009,4.43193577 C2.42867801,4.70855346 2.44394123,5.12098268 2.7008908,5.37931459 Z">
                        </path>
                    </symbol>
                    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" id="check">
                        <path id="test"
                            d="M4.76499011,6.7673683 L8.2641848,3.26100386 C8.61147835,2.91299871 9.15190114,2.91299871 9.49919469,3.26100386 C9.51164115,3.27347582 9.52370806,3.28637357 9.53537662,3.29967699 C9.83511755,3.64141434 9.81891834,4.17816549 9.49919469,4.49854425 L5.18121271,8.82537365 C4.94885368,9.05820878 4.58112654,9.05820878 4.34876751,8.82537365 L2.50080531,6.97362503 C2.48835885,6.96115307 2.47629194,6.94825532 2.46462338,6.93495189 C2.16488245,6.59321455 2.18108166,6.0564634 2.50080531,5.73608464 C2.84809886,5.3880795 3.38852165,5.3880795 3.7358152,5.73608464 L4.76499011,6.7673683 Z">
                        </path>
                    </symbol>
                </svg>


            </div>
        </div>
        <ul></ul>


    </ul>

</div>
<button class="btnnn" hidden="true" onclick="getsound()"> sounnd</button>
<iframe id="notification-listeners" src="{{ route('notification.iframe') }}" hidden></iframe>

<script>
    function getnotifications(nid, icon, h, p, url, date) {
        if ($(`.notifyitem.itemid${nid}`).length) {
            return '';
        }
        let x = `<li class="nav-link d-flex flex-wrap flex-row notifyitem itemid${nid}">

                            <div class="notification" delNot="${nid}">
                                <a href="" class="las la-trash crossnotify" data-id="${nid}"></a>
                                    <p class="time">${date} </p>
                                <i class="notifyicon ${icon}"></i>
                                <span class="badge nb badge--success" style="position:absolute">New</span>
                                <div class="text">
                                    <a href="${url}">
                                        <h4 class="head">${h}</h4>
                                    </a>
                                    <p class="head">
                                        ${p}
                                    </p>

                                </div>
                            </div>
                        </li>`;
        return x;
    }

    function displayNofication(notify, toappend = 1) {
        let data1 = '';

        notify.forEach(n => {
            let user = "{{ auth()->user() && auth()->user()->seller }}"
            let a = n.meeting_status;
            let b = n.cf_status;
            let c = n.subs_status;
            let id = n.id;
            let h;
            let time = n.created_at;
            time = moment(time).fromNow();
            let p;
            let url;
            let icon;

            if (a == 0 && b != 0) {
                h = 'CustomField';
                url = "{{ route('user.notify.detail', ':pid') }}";
                url = url.replace(':pid', n.sell_id);

                if (a == 0 && b != 0) {
                    h = 'CustomField';
                    url = "{{ route('user.notify.detail', ':pid') }}";
                    url = url.replace(':pid', n.sell_id);
                    if (b == 1) {
                        p = 'Someone is requesting for changes in CustomField.';
                        icon = 'las la-tools';
                    } else if (b == 2) {
                        p = 'Your customfield update request is approved.';
                        icon = 'las la-check-square';
                    } else if (b == 3) {
                        p = 'Seller is asking for changes in CustomField';
                        icon = 'las la-tools';
                    } else {

                        return;
                    }
                }
            }
            if (a != 0 && b == 0) {
                h = 'Meeting';
                icon = 'las la-phone-volume';
                url = "{{ route('user.metnotify.detail', ':pid') }}";
                url = url.replace(':pid', n.product_id);
                if (a == 1) {
                    p = ' Some buyer is asking for the meeting?';
                } else if (a == 2) {
                    p = 'your meeting request is approved by the Seller'
                    icon = 'las la-check-square';
                } else if (a == 3) {
                    p = 'your meeting request is rejected by the Seller';
                    icon = 'las la-ban';
                } else {
                    $('.addnotification').append(
                        "<h1> No Recent Notification </h1>");
                    return;
                }
            }
            if (a == 0 && b == 0 && c == 0) {
                icon = 'las la-box';
                if (user == 0) {
                    h = 'Product Purchased'
                    p = `You purchased ${n.products.name} of ${n.products.user.username}.`
                    url = "{{ route('user.notify.detail', ':pid') }}";
                    url = url.replace(':pid', n.sell_id);
                } else {
                    h = 'Sold Product'
                    p = ` ${n.products.user.username} your  product named ${n.products.name} is sold.`
                    url = "{{ route('user.notify.detail', ':pid') }}";
                    url = url.replace(':pid', n.sell_id);

                }

            }
            if (a == 0 && b == 0 && n.sell_id == 0 && n.product_id == 0) {
                icon = 'las la-money-check-alt';
                h = 'Subscription'
                p = `You purchased ${n.subscription.name} Pkg of Hexa Tech Solution.`
                url = "{{ route('user.getplans') }}";
            }
            data1 = getnotifications(id, icon, h, p, url, time);
            if ($('.addnotification ul').length == 0) {
                $('.addnotification').append(`<ul></ul>`);
            }
            let nto = $('.addnotification ul');
            if (toappend == 1) {
                nto.append(data1);
            } else {
                nto.prepend(data1);
            }

            if (n.mark_read == 1) {
                $(`.itemid${n.id}`).css('background-color',
                    'rgba(150, 136, 136, 0.561)')
                let badge = $(`.itemid${n.id} span`)
                badge.removeClass('badge--success');
                badge.addClass('badge--danger');
                badge.text('Read');
            }

        });
    }

    function toggleCart() {
        let sideNotify = document.querySelector('.sidecart');
        sideNotify.classList.toggle('open-cart');


    }

    function getNotifications() {
        $.ajax({
            url: "{{ route('user.notify.all') }}",
            success: function(result) {
                if (result.notifications) {
                    var notifications = result.notifications;
                    if (notifications.length === 0) {
                        $('.no-recents').remove();
                        $('.addnotification').append(
                            "<h3 class='no-recents'> No Recent Notification </h3>");
                        return;
                    } else {
                        let ncount = notifications.filter(n => n.mark_read === 0).length;
                        $(".notifycount").text(ncount);
                        var notify = notifications;
                        let data1;
                        $('.addnotification li').remove();
                        displayNofication(notify);


                    }
                }

            }
        });
    }

    function notifylisteners() {
        $('body').on('click touch', '.activate', function(e) {
            var self = $(this);
            if (!self.hasClass('loading')) {
                self.addClass('loading');
                $.ajax({
                    url: "{{ route('user.notify.markasread') }}",
                    success: function(result) {
                        if (result == "success") {
                            setTimeout(function() {
                                self.addClass('done');
                                setTimeout(function() {
                                    self.removeClass('loading done');
                                }, 800);
                            }, 1200);
                            $('.notifyitem').css('background-color', 'rgba(150, 136, 136, 0.561)');
                            $('.nb').removeClass('badge--success');
                            $('.nb').addClass('badge--danger');
                            $('.nb').text('Read');
                        }

                    }
                });
            }
        });
        $('body').on("click", '.notifyitem', function() {
            let notifycss = $(this).find('.notification').closest('[delNot]');
            var nid = notifycss.attr('delNot');
            $.ajax({
                url: "{{ route('user.notify.markasread') }}" + '/' + nid,
                success: function(result) {
                    if (result == "success") {

                    }

                }
            });

        });
    }

    function notifymessagelistener(e) {
        let data = e.data;
        if (typeof data == 'object') {
            if (data?.key) {
                if (data.key == 'notification_count') {
                    console.log(data);
                    if (data?.result) {
                        let result = data.result;
                        var old = $(".notifycount").text(result.count);
                        displayNofication(result.last_notification, 0);
                        $("#myAudio").trigger('play');

                    }
                }

            }
            if (data?.name) {

            }
        }
    }
    window.addEventListener('message', notifymessagelistener, false);
</script>
