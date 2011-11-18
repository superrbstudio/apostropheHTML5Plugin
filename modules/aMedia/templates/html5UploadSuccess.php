<h1>Drag and Drop Upload!</h1>

<div id="a-html5-media-uploader" style="height: 100px; width: 100px; border: thin solid black">
  <noscript>
    <p>Please enable JavaScript for this hotness.</p>
  </noscript>
</div>

<script type="text/javascript">

$(document).ready(function() {
  var uploader = new qq.FileUploader({
    element: $('#a-html5-media-uploader')[0],
    action: '<?php echo url_for('aMedia/html5Upload'); ?>',
    debug: true
  });
});

</script>