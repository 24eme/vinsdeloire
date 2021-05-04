<div style="text-align: center; top: 30%; position: absolute; width: 99%;"><center>
<img src="/images/loire2douane.gif" width="600" height="150"/>
<p>Transmission des données à douane.gouv.fr en cours... <br/>Veuillez patienter</p>
</center></div>
<form id="form_transmission" method="post" action="<?php echo url_for('drm_ciel', $drm); ?>">
</form>
<script src="/js/lib/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
        $(document).ready(function() {
            setTimeout("$('#form_transmission').submit();", 1000);
        });
</script>
