<?php
/**
 * Extends input/select. Elgg 7 removed the AMD loader, so the old
 * `require(['autocomplete/select'], ...)` wrapper was a ReferenceError on every
 * page carrying a select input. Import the ES module and initialise it from an
 * inline module script instead.
 */

elgg_import_esm('autocomplete/select');
?>
<script type="module">
	import select from 'autocomplete/select';

	select.init();
</script>
