<style>
    html {
        margin: -1px;
        border: -1px;
        padding: -1px;
    }

    body {
        background: url('https://i.imgur.com/eYKOx30.jpg');
        background-repeat: no-repeat;
        background-attachment: fixed;
        font-family: 'Questrial', sans-serif;
    }

    ::-webkit-scrollbar {
        display: none;
    }

    ul {
        display: block;
        width: 25%;
        margin: 15% auto;
        padding: 0;
        list-style: none;
    }

    ul.container {
        box-shadow: 0px 0px 0px #000;
    }

    ul.submenu {
        display: block;
        width: 100%;
        margin: 0;
        padding: 0;
        background: rgba(220, 219, 219, .07);
    }

    a {
        display: block;
        padding: 10px;
        color: #EEE;
        text-decoration: none;
        transition: all .3s ease;
        opacity: .5;
    }

    a:hover {
        color: #fff;
        transition: all .3s ease;
    }

    a.parent1 {
        transition: all .3s ease;
        color: #555;
        background: #EEE;
        text-align: center;
    }

    a.parent2 {
        transition: all .3s ease;
        color: #555;
        background: #EEE;
        text-align: center;
    }

    a.parent3 {
        transition: all .3s ease;
        color: #555;
        background: #EEE;
        text-align: center;
    }

    a.parent4 {
        transition: all .3s ease;
        color: #555;
        background: #EEE;
        text-align: center;
    }

    a.parent5 {
        transition: all .3s ease;
        color: #555;
        background: #EEE;
        text-align: center;
    }

    a.parent6 {
        transition: all .3s ease;
        color: #555;
        background: #EEE;
        text-align: center;
    }

    a.tab1 {
        transition: all .3s ease;
        opacity: 0.5;
    }

    a.tab2 {
        transition: all .3s ease;
        opacity: 0.5;
    }

    a.tab3 {
        transition: all .3s ease;
        opacity: 0.5;
    }

    a.tab4 {
        transition: all .3s ease;
        opacity: 0.5;
    }

    a.tab5 {
        transition: all .3s ease;
        opacity: 0.5;
    }

    a.tab6 {
        transition: all .3s ease;
        opacity: 0.5;
    }

    a.tab1:hover {
        transition: all .3s ease;
        text-indent: 5px;
        opacity: 1;
    }

    a.tab2:hover {
        transition: all .3s ease;
        text-indent: 5px;
        opacity: 1;
    }

    a.tab3:hover {
        transition: all .3s ease;
        text-indent: 5px;
        opacity: 1;
    }

    a.tab4:hover {
        transition: all .3s ease;
        text-indent: 5px;
        opacity: 1;
    }

    a.tab5:hover {
        transition: all .3s ease;
        text-indent: 5px;
        opacity: 1;
    }

    a.tab6:hover {
        transition: all .3s ease;
        text-indent: 5px;
        opacity: 1;
    }

    ul>li:hover>a.parent1 {
        transition: all .3s ease;
        opacity: .8;
    }

    ul>li:hover>a.parent2 {
        transition: all .3s ease;
        opacity: .8;
    }

    ul>li:hover>a.parent3 {
        transition: all .3s ease;
        opacity: .8;
    }

    ul>li:hover>a.parent4 {
        transition: all .3s ease;
        opacity: .8;
    }

    ul>li:hover>a.parent5 {
        transition: all .3s ease;
        opacity: .8;
    }

    ul>li:hover>a.parent6 {
        transition: all .3s ease;
        opacity: .8;
    }
</style>
<html>

<head>
    <title>New Tab</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <link href='https://fonts.googleapis.com/css?family=Questrial' rel='stylesheet' type='text/css'>
</head>

<body>
    <ul class="container">
        <li><a class="parent1" href="#">Entertainment</a>
            <ul class="submenu">
                <li><a class="tab1" href="https://boards.4chan.org/g/catalog">/g/ - Technology</a></li>
                <li><a class="tab1" href="https://boards.4chan.org/gd/catalog">/gd/ - Graphic Design</a></li>
                <li><a class="tab1" href="https://play.google.com/music">Google Play Music</a></li>
                <li><a class="tab1" href="https://netflix.com">Netflix</a></li>
                <li><a class="tab1" href="https://soundcloud.com/stream">SoundCloud</a></li>
                <li><a class="tab1" href="https://www.flickr.com">Flickr</a></li>
                <li><a class="tab1" href="https://youtube.com">Youtube</a></li>
                <li><a class="tab1" href="https://web.whatsapp.com">WhatsApp Web</a></li>
            </ul>
        </li>
        <li><a class="parent2" href="#">Technology & Science</a>
            <ul class="submenu">
                <li><a class="tab2" href="https://www.codepen.io">CodePen</a></li>
                <li><a class="tab2" href="http://www.codeacademy.com">Codeacademy</a></li>
                <li><a class="tab2" href="https://www.github.com">Github</a></li>
                <li><a class="tab2" href="http://www.livescience.com">Live Science</a></li>
                <li><a class="tab2" href="https://www.newscientist.com">New Scientist</a></li>
                <li><a class="tab2" href="http://www.stackoverflow.com">Stack Overflow</a></li>
                <li><a class="tab2" href="http://www.stuff.tv">Stuff</a></li>
                <li><a class="tab2" href="https://www.wired.co.uk">Wired UK</a></li>
                <li><a class="tab2" href="https://www.wired.com">Wired US</a></li>
            </ul>
        </li>
        <!--<li><a class="parent3" href="http://www.distrowatch.com">DistroWatch</a></a>
        </li>-->
        <li><a class="parent3" href="#">Reddit</a>
            <ul class="submenu">
                <li><a class="tab4" href="https://www.reddit.com/r/analog">Analog</a></li>
                <li><a class="tab4" href="https://www.reddit.com/r/android">Android</a></li>
                <li><a class="tab4" href="https://www.reddit.com/r/citiesskylines">Cities: Skylines</a></li>
                <li><a class="tab4" href="https://www.reddit.com/r/space">Space</a></li>
                <li><a class="tab4" href="https://www.reddit.com/r/startpages">Startpages</a></li>
                <li><a class="tab4" href="https://www.reddit.com/r/techsupportgore">Techsupportgore</a></li>
                <li><a class="tab4" href="https://www.reddit.com/r/whatcouldgowrong">Whatcouldgowrong</a></li>
                <li><a class="tab4" href="https://www.reddit.com/r/4chan">4chan</a></li>
            </ul>
        </li>
        <li><a class="parent4" href="#">Misc</a>
            <ul class="submenu">
                <li><a class="tab5" href="https://www.dropbox.com">Dropbox</a></li>
                <li><a class="tab5" href="https://drive.google.com/drive">Google Drive</a></li>
                <li><a class="tab5" href="https://interfacelift.com/wallpaper/downloads/date/any/">InterfaceLIFT</a>
                </li>
                <li><a class="tab5" href="https://mega.co.nz">MEGA</a></li>
                <li><a class="tab5" href="http://cupcake.nilssonlee.se">Nilsson Lee Cupcake</a></li>
                <li><a class="tab5" href="https://pastebin.com">Pastebin</a></li>
                <li><a class="tab5" href="https://www.rarbg.com/">RARBG</a></li>
            </ul>
        </li>
        <!--<li><a class="parent6" href="#">Misc</a>
                <ul class="submenu">
                        <li><a class="tab6" href="http://www.azlyrics.com">AZ Lyrics</a></li>
                        <li><a class="tab6" href="http://www.wallbase.cc">Wallbase</a></li>
                        <li><a class="tab6" href="http://qrohlf.com/trianglify/">Trianglify</a></li>
                        <li><a class="tab6" href="http://hastebin.com">Hastebin</a></li>
                </ul>-->
    </ul>
    <!--
<div class="outer">
<div class="middle">
<div class="inner">

                                        <script type="text/javascript">
                                            document.write('<script id="news-feed" type="text/javascript" src="http://feed2js.org//feed2js.php?src=' +
                                                android_feed + '&chan=y&num=' + news_feed_num + '&utf=y"  "></scr' + 'ipt>');
                                            // document.write('<script id="news-tech-feed" type="text/javascript" src="http://feed2js.org//feed2js.php?src='+
                                            // bbc_world_feed + '&chan=y&num=' + news_feed_num + '&utf=y"  "></scr' + 'ipt>');
                                            // document.write('<script id="news-tech-feed" type="text/javascript" src="http://feed2js.org//feed2js.php?src='+
                                            // bbc_tech_feed + '&chan=y&num=' + news_feed_num + '&utf=y"  "></scr' + 'ipt>');
                                        </script>

</div>
</div>
</div> -->

    <!-- <div class="content-area" id="rss" style="position: absolute; top:80px; left:400px; width:200px; height:25px">

                                </div> -->

    <script>
        $('.submenu').hide();
        $("li:has(ul)").click(function() {
            $("ul", this).slideToggle('200');
        });
    </script>
</body>

</html>
