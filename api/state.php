<?php
require '../config.php';

$gameId = (int)($_GET['game'] ?? 0);
$token = $_GET['token'] ?? '';
$s = db()->prepare('SELECT g.*,q.title,(SELECT COUNT(*) FROM jolt_questions WHERE quiz_id=g.quiz_id) total_questions FROM jolt_games g JOIN jolt_quizzes q ON q.id=g.quiz_id WHERE g.id=?');
$s->bind_param('i', $gameId);
$s->execute();
$g = $s->get_result()->fetch_assoc();
if (!$g) json_response(['error' => 'Not found'], 404);

if ($g['status']==='question' && $g['question_started_at']) {
    $idx=max(0,(int)$g['current_question']-1);$s=db()->prepare('SELECT time_limit FROM jolt_questions WHERE quiz_id=? ORDER BY position LIMIT ?,1');$s->bind_param('ii',$g['quiz_id'],$idx);$s->execute();$limit=$s->get_result()->fetch_assoc();
    if($limit && microtime(true)>=strtotime($g['question_started_at'])+(int)$limit['time_limit']){db()->query("UPDATE jolt_games SET status='results',results_started_at=NOW(3) WHERE id=$gameId AND status='question'");$g['status']='results';$g['results_started_at']=date('Y-m-d H:i:s');}
}
if($g['status']==='results' && !empty($g['leaderboard_time']) && $g['results_started_at'] && microtime(true)>=strtotime($g['results_started_at'])+(int)$g['leaderboard_time']){
    if((int)$g['current_question']>=(int)$g['total_questions']){db()->query("UPDATE jolt_games SET status='finished' WHERE id=$gameId AND status='results'");$g['status']='finished';}
    else{$next=(int)$g['current_question']+1;db()->query("UPDATE jolt_games SET status='question',current_question=$next,question_started_at=NOW(3),results_started_at=NULL WHERE id=$gameId AND status='results'");$g['status']='question';$g['current_question']=$next;$g['question_started_at']=date('Y-m-d H:i:s');}
}

$data = ['status' => $g['status'], 'code' => $g['code'], 'title' => $g['title'], 'current' => (int)$g['current_question'], 'total' => (int)$g['total_questions'], 'music' => $g['music_theme']];
$players = db()->query('SELECT id,name,avatar,score,joined_at FROM jolt_players WHERE game_id='.$gameId.' ORDER BY score DESC,joined_at ASC')->fetch_all(MYSQLI_ASSOC);

if ($g['status'] === 'question' || $g['status'] === 'results') {
    $s = db()->prepare('SELECT * FROM jolt_questions WHERE quiz_id=? ORDER BY position LIMIT ?,1');
    $idx = max(0, (int)$g['current_question'] - 1);
    $s->bind_param('ii', $g['quiz_id'], $idx);
    $s->execute();
    $q = $s->get_result()->fetch_assoc();
    if ($q) {
        $answers = db()->query('SELECT id,text'.($g['status'] === 'results' ? ',is_correct' : '').' FROM jolt_answers WHERE question_id='.(int)$q['id'].' ORDER BY position')->fetch_all(MYSQLI_ASSOC);
        $elapsed = $g['question_started_at'] ? (int)((microtime(true) - strtotime($g['question_started_at'])) * 1000) : 0;
        $count = (int)db()->query('SELECT COUNT(*) c FROM jolt_responses WHERE game_id='.$gameId.' AND question_id='.(int)$q['id'])->fetch_assoc()['c'];
        $data['question'] = ['id' => $q['id'], 'text' => $q['text'], 'type' => $q['type'], 'time' => (int)$q['time_limit'], 'media_path' => $q['media_path'], 'media_type' => $q['media_type'], 'answers' => $answers, 'elapsed_ms' => max(0, $elapsed), 'responses' => $count];

        if ($g['status'] === 'results') {
            $points = [];
            $result = db()->query('SELECT player_id,points_awarded FROM jolt_responses WHERE game_id='.$gameId.' AND question_id='.(int)$q['id']);
            while ($row = $result->fetch_assoc()) $points[(int)$row['player_id']] = (int)$row['points_awarded'];
            $previous = $players;
            usort($previous, function ($a, $b) use ($points) {
                $aScore = (int)$a['score'] - ($points[(int)$a['id']] ?? 0);
                $bScore = (int)$b['score'] - ($points[(int)$b['id']] ?? 0);
                return $bScore <=> $aScore ?: strcmp($a['joined_at'], $b['joined_at']);
            });
            $previousRanks = [];
            foreach ($previous as $rank => $player) $previousRanks[(int)$player['id']] = $rank + 1;
            foreach ($players as $rank => &$player) {
                $player['previous_rank'] = $previousRanks[(int)$player['id']];
                $player['rank_change'] = $player['previous_rank'] - ($rank + 1);
            }
            unset($player);
        }
    }
}

foreach ($players as &$player) unset($player['id'], $player['joined_at']);
unset($player);
$data['players'] = $players;

if ($token) {
    $s = db()->prepare('SELECT id,score FROM jolt_players WHERE token=? AND game_id=?');
    $s->bind_param('si', $token, $gameId);
    $s->execute();
    $p = $s->get_result()->fetch_assoc();
    if (!$p) json_response(['error' => 'Unauthorized'], 403);
    $data['me'] = $p;
    if (isset($data['question'])) {
        $s = db()->prepare('SELECT answer_ids,is_correct,points_awarded FROM jolt_responses WHERE player_id=? AND question_id=?');
        $s->bind_param('ii', $p['id'], $data['question']['id']);
        $s->execute();
        $data['response'] = $s->get_result()->fetch_assoc();
    }
}

json_response($data);
