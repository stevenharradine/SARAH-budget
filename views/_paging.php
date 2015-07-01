<section class="paging">
	<a href="?month=<?php echo $month - 1 == 0 ? '12' : $month - 1; ?>&year=<?php echo $month - 1 == 0 ? $year - 1 : $year; ?>" />prev month</a>
	<a href="?month=<?php echo $month + 1 == 13 ? '1' : $month + 1; ?>&year=<?php echo $month + 1 == 13 ? $year + 1 : $year; ?>" />next month</a>
	
	<a href="?month=<?php echo $month; ?>&year=<?php echo $year - 1; ?>" />prev year</a>
	<a href="?month=<?php echo $month; ?>&year=<?php echo $year + 1; ?>" />next year</a>
</section>