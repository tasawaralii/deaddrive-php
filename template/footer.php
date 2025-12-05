<footer>
<div class="card mt-3 mb-3">
    <div class="card-body" style="border:0px;">
        <h6 class="mb-0 text-center">

            <strong>

                <a href="../about-us">
                    <i class="fa fa-info-circle text-light"></i> About US
                </a> |
                <a id="api-link" href="#">
                    <i class="fa fa-code text-light"></i> API Docs
                </a> |

                <a href="/privacy-policy">
                    <i class="fa fa-bullhorn text-light"></i> Privacy & Policy
                </a> |
                <a href="/terms-conditions">
                    <i class="fa fa-book text-light"></i> Terms & Conditions
                </a> |
                <a href="../copyright-policy">
                    <i class="fa fa-copyright text-light"></i> Copyright Policy
                </a> |
                <a href="../contact-us">
                    <i class="fa fa-envelope text-light"></i> DMCA & Contact Us
                </a>

                |
                <a href="<?= $_ENV['CONTACT_TELEGRAM'] ?>" target="_blank">
                    <i class="fab fa-telegram-plane text-light"></i>
                    Join Telegram
                </a>
            </strong>
        </h6>
    </div>
</div>
</footer>

<script>
    document.getElementById("api-link").href = "https://api." + window.location.hostname;
</script>

<script type="text/javascript">

    var l = window.location.pathname,
        e = l.split('/');

    if (e.length >= 2) {

        var n = (e.length > 2) ? 2 : 1,
            c = document.getElementById(e[n]);

        if (c) {
            c.classList.add('active')
        }

    }

</script>

<script type="text/javascript" src="/js/mdb.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>