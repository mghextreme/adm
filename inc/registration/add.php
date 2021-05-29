<script type="text/javascript" charset="UTF-8" language="JavaScript">
	jQuery(document).ready(function(){
		jQuery('#submit.submit').click(function(){
			jQuery("#theForm.theForm").validationEngine();
		});
	});
</script>
<?php addForm($_GET['menu'], -1); ?>