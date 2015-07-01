<?php
		function dateForSqlFormat ($date) {
			return $date == 'CURRENT_TIMESTAMP' ? $date : "'$date'";
		}
	class BudgetManager {
		public function updateRecord ($id, $store, $date) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$dateForSql = dateForSqlFormat ($date);

			$sql = <<<EOD
UPDATE
	`sarah`.`budget`
SET
	`store` = '$store',
	`date` = '$date'
WHERE
	`BUDGET_ID`='$id'
		AND
	`USER_ID`='$USER_ID';
EOD;
			
			return mysql_query($sql) or die(mysql_error());
		}

		public function updateItemRecord ($id, $item_name, $amount, $qty, $category, $brand, $size, $size_unit, $tax, $sale) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
UPDATE
	`sarah`.`budget_items`
SET
	`item_name` = '$item_name',
	`amount` = '$amount',
	`qty` = '$qty',
	`category` = '$category',
	`brand` = '$brand',
	`size` = '$size',
	`size_unit` = '$size_unit',
	`tax` = '$tax',
	`sale` = '$sale'
WHERE
	`BUDGET_ITEM_ID`='$id'
		AND
	`USER_ID`='$USER_ID';
EOD;
			
			return mysql_query($sql) or die(mysql_error());
		}

		public function updateRecurringRecord ($id, $amount, $category, $store, $items, $startdate, $enddate) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
	UPDATE
		`sarah`.`budget_recurring`
	SET
		`amount` = '$amount',
		`category` = '$category',
		`store` = '$store',
		`items` = '$items',
		`startDate` = '$startdate',
		`endDate` = '$enddate'
	WHERE
		`RECURRING_ID`='$id'
	AND
		`USER_ID`='$USER_ID';
EOD;
			
			return mysql_query($sql) or die(mysql_error());
		}

		public function addRecord ($store, $date) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$dateForSql = dateForSqlFormat ($date);

			$sql = <<<EOD
	INSERT INTO `sarah`.`budget` (
		`USER_ID`,
		`store`,
		`date`
	) VALUES (
		'$USER_ID',
		'$store',
		$dateForSql
	);
EOD;
			$data = mysql_query($sql) or die(mysql_error());
			return mysql_insert_id();
		}

		public function addItemRecord ($budget_id, $item_name, $amount, $qty, $category, $brand, $size, $size_unit, $tax, $sale) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$tax = percentToDecimal ($tax);

			$sql = <<<EOD
	INSERT INTO `sarah`.`budget_items` (
		`USER_ID`,
		`BUDGET_ID`,
		`item_name`,
		`amount`,
		`qty`,
		`category`,
		`brand`,
		`size`,
		`size_unit`,
		`tax`,
		`sale`
	) VALUES (
		'$USER_ID',
		'$budget_id',
		'$item_name',
		'$amount',
		'$qty',
		'$category',
		'$brand',
		'$size',
		'$size_unit',
		'$tax',
		'$sale'
	);
EOD;

			return mysql_query($sql) or die(mysql_error());
		}

		public function addRecurringRecord ($amount, $category, $store, $items, $startdate, $enddate) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$formatedStartDate = $startdate == 'CURRENT_TIMESTAMP' ? $startdate : "'$startdate'";
			$formatedEndDate = $enddate == 'CURRENT_TIMESTAMP' ? $enddate : "'$enddate'";

			$sql = <<<EOD
	INSERT INTO
		`sarah`.`budget_recurring` (
			`USER_ID`,
			`amount`,
			`category`,
			`store`,
			`items`,
			`startDate`,
			`endDate`
		) VALUES (
			'$USER_ID',
			'$amount',
			'$category',
			'$store',
			'$items',
			$formatedStartDate,
			$formatedEndDate
		)
EOD;

			return mysql_query($sql) or die(mysql_error());
		}

		public function deleteRecord ($id) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
	DELETE FROM `sarah`.`budget`
	WHERE `BUDGET_ID`='$id'
	AND `USER_ID`='$USER_ID';
EOD;

			return mysql_query($sql) or die(mysql_error());
		}

		public function deleteItemRecord ($id) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
	DELETE FROM
		`sarah`.`budget_items`
	WHERE
		`BUDGET_ITEM_ID`='$id'
			AND
		`USER_ID`='$USER_ID';
EOD;

			return mysql_query($sql) or die(mysql_error());
		}

		public function deleteRecurringRecord ($id) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
	DELETE FROM
		`sarah`.`budget_recurring`
	WHERE
		`RECURRING_ID`='$id'
			AND
		`USER_ID`='$USER_ID';
EOD;

			return mysql_query($sql) or die(mysql_error());
		}

		public function getRecord ($id) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
	SELECT
		*
	FROM
		`budget`
	WHERE
		`BUDGET_ID`='$id'
			AND
		`USER_ID`='$USER_ID';
EOD;
			$data = mysql_query($sql) or die(mysql_error());
			return mysql_fetch_array( $data );
		}

		public function getRecurringRecord ($id) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
	SELECT
		*
	FROM
		`budget_recurring`
	WHERE
		`RECURRING_ID`='$id'
			AND
		`USER_ID`='$USER_ID';
EOD;
			$data = mysql_query($sql) or die(mysql_error());
			return mysql_fetch_array( $data );
		}

		
		public function getItemRecord ($id) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
	SELECT
		*
	FROM
		`sarah`.`budget_items`
	WHERE
		`BUDGET_ITEM_ID` = '$id'
			AND
		`USER_ID` ='$USER_ID';
EOD;
			$data = mysql_query($sql) or die(mysql_error());
			return mysql_fetch_array( $data );
		}

		public function getAllRecords ($year=null, $month=null) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			/*
				TODO: bugfix, %$year-$month% has collision with %$month-$day%
			*/
			$sql = <<<EOD
	SELECT *
	FROM `budget`
	WHERE `date` LIKE '%$year-$month%'
	AND `USER_ID` = $USER_ID
EOD;
			$data = mysql_query($sql) or die(mysql_error());

			return $data;
		}

		public function getAllRecurringByMonth ($year=null, $month=null) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();
			$year = $year == null ? '1970' : $year;
			$month = $month == null ? '01' : $month;

			$date = new DateTime ("$year-$month-01 00:00:01");
			$daysInMonth = date ('t', $date->getTimestamp());

			$startDate = "$year-$month-$daysInMonth 00:00:01";
			$endDate = "$year-$month-01 00:00:01";

			$sql = <<<EOD
	SELECT
		*
	FROM
		budget_recurring
	WHERE
		`startDate` <= DATE('$startDate')
			AND
		`endDate` >= DATE('$endDate')
			AND
		`USER_ID` = $USER_ID
EOD;
			$data = mysql_query($sql) or die(mysql_error());

			return $data;
		}
		public function getAllRecurring () {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
	SELECT
		*
	FROM
		budget_recurring
	WHERE
		`USER_ID` = $USER_ID
EOD;
			$data = mysql_query($sql) or die(mysql_error());

			return $data;
		}
		public function getItems ($id) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
	SELECT
		*
	FROM
		`sarah`.`budget_items`
	WHERE
		`BUDGET_ID` = $id
			AND
		`USER_ID` = $USER_ID;
EOD;
			$data = mysql_query($sql) or die(mysql_error());

			return $data;
		}

		public function getBudgetIdFromItemId ($id) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
	SELECT
		`BUDGET_ID`
	FROM
		`budget_items`
	WHERE
		`BUDGET_ITEM_ID`='$id'
			AND
		`USER_ID` = $USER_ID;
EOD;

			$data = mysql_query($sql) or die(mysql_error());
			return mysql_fetch_row($data)[0];
		}

		public function getRecieptTotalById ($id) {
			global $sessionManager;
			$USER_ID = $sessionManager->getUserId();
			$total = 0;

			$sql = <<<EOD
	SELECT
		`amount`,
		`qty`,
		`tax`
	FROM
		`budget_items`
	WHERE
		`BUDGET_ID` = '$id'
			AND
		`USER_ID` = $USER_ID;
EOD;

			$data = mysql_query($sql) or die(mysql_error());

			while (($row = mysql_fetch_array( $data ) ) != null) {
				$total += $row['amount'] * $row['qty'] * (1 + $row['tax']);
			}

			return $total;
		}
	}