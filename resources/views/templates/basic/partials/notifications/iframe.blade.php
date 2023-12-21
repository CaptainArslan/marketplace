<script>
    let parent = window.parent;
    function getsound() {
        fetch("{{ route('user.notify.count') }}").then(t => {
            return t.json();
        }).then(t => {
            if (t) {
                if (t.count > localStorage.getItem("oldcount") ?? 0) {
                    localStorage.setItem("oldcount", t.count);
                    parent.postMessage({
                        key: 'notification_count',
                        result: t
                    }, '*');
                }

            }
            setTimeout(getsound, 2000);
        }).catch(x => {

        });
    }
    // window.addEventListener('message', function(e) {
    //     let data = e.data;
    //     if (typeof data == 'object') {
    //         if (data.key == 'get_users') {
    //             //getsound();

    //         }
    //     }
    // }, false);
    if (window.parent != window.self) {
        if (location.ancestorOrigins.length > 0) {
            let allowedorigins = ['http://localhost'];
            if (Object.values(location.ancestorOrigins).some(t => {
                    return allowedorigins.includes(t);
                })) {
                getsound();
            }
        }
    }


    // let b= 10;
    // let a = setInterval(() => {
    //     if(b==0){
    //         clearInterval(a);
    //         return;
    //     }
    //     b--;
    // }, 100);
</script>
