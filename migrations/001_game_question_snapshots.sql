USE jolt;
CREATE TABLE IF NOT EXISTS jolt_game_questions (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, game_id INT UNSIGNED NOT NULL,
 question_id INT UNSIGNED NULL, position SMALLINT UNSIGNED NOT NULL,
 text VARCHAR(500) NOT NULL, max_points SMALLINT UNSIGNED NOT NULL,
 FOREIGN KEY (game_id) REFERENCES jolt_games(id) ON DELETE CASCADE,
 FOREIGN KEY (question_id) REFERENCES jolt_questions(id) ON DELETE SET NULL,
 UNIQUE(game_id,position), INDEX(game_id,question_id)
) ENGINE=InnoDB;
INSERT IGNORE INTO jolt_game_questions(game_id,question_id,position,text,max_points)
SELECT g.id,q.id,q.position,q.text,q.points FROM jolt_games g JOIN jolt_questions q ON q.quiz_id=g.quiz_id;
