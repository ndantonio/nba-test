<?php
namespace classes\Exporter;

use Illuminate\Support;
use LSS\Array2Xml;
use models\Roster;

include('../include/utils.php');

class Exporter 
{
    private $roster;

    public function __construct(Roster $roster)
    {
        $this->roster = $roster;
    }

    function setSearchFilters($search) {
        $where = [];
        if ($search->has('playerId')) $where[] = "roster.id = '" . $search['playerId'] . "'";
        if ($search->has('player')) $where[] = "roster.name = '" . $search['player'] . "'";
        if ($search->has('team')) $where[] = "roster.team_code = '" . $search['team']. "'";
        if ($search->has('position')) $where[] = "roster.pos = '" . $search['position'] . "'";
        if ($search->has('country')) $where[] = "roster.nationality = '" . $search['country'] . "'";

        $where = implode(' AND ', $where);

        return $where;
    }

    function getPlayerStats($search) {
        
        $where = $this->setSearchFilters($search);

        $data = $this->roster->getPlayerStats($where);

        // calculate totals
        foreach ($data as &$row) {
            unset($row['player_id']);
            $row['total_points'] = ($row['3pt'] * 3) + ($row['2pt'] * 2) + $row['free_throws'];
            $row['field_goals_pct'] = $row['field_goals_attempted'] ? (round($row['field_goals'] / $row['field_goals_attempted'], 2) * 100) . '%' : 0;
            $row['3pt_pct'] = $row['3pt_attempted'] ? (round($row['3pt'] / $row['3pt_attempted'], 2) * 100) . '%' : 0;
            $row['2pt_pct'] = $row['2pt_attempted'] ? (round($row['2pt'] / $row['2pt_attempted'], 2) * 100) . '%' : 0;
            $row['free_throws_pct'] = $row['free_throws_attempted'] ? (round($row['free_throws'] / $row['free_throws_attempted'], 2) * 100) . '%' : 0;
            $row['total_rebounds'] = $row['offensive_rebounds'] + $row['defensive_rebounds'];
        }
        return collect($data);
    }

    function getPlayers($search) {
        
        $where = $this->setSearchFilters($search);

        $data = $this->roster->getRoster($where);
        
        return collect($data)
            ->map(function($item, $key) {
                unset($item['id']);
                return $item;
            });
    }
}

?>