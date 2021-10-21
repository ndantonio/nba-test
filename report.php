<?php

/**
 * Use this file to output reports required for the SQL Query Design test.
 * An example is provided below. You can use the `asTable` method to pass your query result to,
 * to output it as a styled HTML table.
 */

require_once('vendor/autoload.php');
require_once('config/database.php');
require_once('helpers/QueryHelperTrait.php');
require_once('include/utils.php');

/*
 * Example Query
 * -------------
 * Retrieve all team codes & names
 */
echo '<h1>Example Query</h1>';
$teamSql = "SELECT * FROM team";
$teamResult = query($teamSql);
echo asTable($teamResult);

/*
 * Report 1
 * --------
 * Produce a query that reports on the best 3pt shooters in the database that are older than 30 years old. Only 
 * retrieve data for players who have shot 3-pointers at greater accuracy than 35%.
 * 
 * Retrieve
 *  - Player name
 *  - Full team name
 *  - Age
 *  - Player number
 *  - Position
 *  - 3-pointers made %
 *  - Number of 3-pointers made 
 *
 * Rank the data by the players with the best % accuracy first.
 */
echo '<h1>Report 1 - Best 3pt Shooters</h1>';

$teamSql = "SELECT 
				r.name as `player`, 
				t.name as `team`, 
				pt.age, 
				r.number, 
				r.pos, 
				CONCAT(ROUND(( pt.3pt/pt.3pt_attempted * 100 )),'%') as `3pt_pct`,
				pt.3pt as `3pt_made`
			FROM roster `r`
			INNER JOIN player_totals `pt` ON r.id = pt.player_id
			INNER JOIN team `t` ON r.team_code = t.code
			WHERE 
				pt.age > 30
			HAVING
				`3pt_pct` > 35
			ORDER BY `3pt_pct` DESC";

$teamResult = query($teamSql);
echo asTable($teamResult);


/*
 * Report 2
 * --------
 * Produce a query that reports on the best 3pt shooting teams. Retrieve all teams in the database and list:
 *  - Team name
 *  - 3-pointer accuracy (as 2 decimal place percentage - e.g. 33.53%) for the team as a whole,
 *  - Total 3-pointers made by the team
 *  - # of contributing players - players that scored at least 1 x 3-pointer
 *  - of attempting player - players that attempted at least 1 x 3-point shot
 *  - total # of 3-point attempts made by players who failed to make a single 3-point shot.
 * 
 * You should be able to retrieve all data in a single query, without subqueries.
 * Put the most accurate 3pt teams first.
 */
echo '<h1>Report 2 - Best 3pt Shooting Teams</h1>';

$teamSql = "SELECT
				t.name as `team`,
				CONCAT(ROUND(SUM(pt.3pt)/SUM(3pt_attempted) * 100, 2), '%') as `team_3pt_accuracy`,
				SUM(pt.3pt) as `team_3pt_made`,
				COUNT(CASE WHEN pt.3pt > 0 THEN r.id END) as `contributing_players`
			FROM team `t`
			RIGHT JOIN roster `r` ON t.code = r.team_code
			INNER JOIN player_totals `pt` ON r.id = pt.player_id
			GROUP BY t.code
			ORDER BY `team_3pt_accuracy` DESC
			";

$teamResult = query($teamSql);
echo asTable($teamResult);

?>