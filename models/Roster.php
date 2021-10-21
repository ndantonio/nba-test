<?php
namespace models\Roster;

include('config/database.php');
include('helpers/QueryHelperTrait.php');

class Roster 
{
	public function getPlayerStats($where) {
		$sql = "
            SELECT roster.name, player_totals.*
            FROM player_totals
                INNER JOIN roster ON (roster.id = player_totals.player_id)
            WHERE {$where}";
        return query($sql) ?: [];
	}

	public function getRoster($where) {
		$sql = "
            SELECT roster.*
            FROM roster
            WHERE {$where}";
        return query($sql) ?: [];
	}
}