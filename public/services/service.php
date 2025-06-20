<?php
$env = parse_ini_file('../../.env');

$dsn = sprintf('pgsql:host=%s;dbname=%s', $env['DB_HOST'], $env['DB_NAME']);
try {
  $dbconn = new PDO($dsn, $env['DB_USER'], $env['DB_PASSWORD']);
  $dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}
$dbconn->exec('LISTEN "datachange"');   // those doublequotes are very important

header("X-Accel-Buffering: no"); // disable ngnix webServer buffering
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
if (ob_get_contents()) {
  ob_end_flush();  // close PHP output buffering
}

while (1) {
  $result = "";
  // wait for one Notify 10seconds instead of using sleep(10)
  $result = $dbconn->pgsqlGetNotify(PDO::FETCH_ASSOC, 10000);

  try {
    if ($result) {
      $data = get_object_vars(json_decode($result['payload']));
      $sender = $data['data']->sender_id;
      $sender = $dbconn->query("SELECT avatar FROM users WHERE id = $sender")->fetch(PDO::FETCH_ASSOC);
      $sender = $sender['avatar'] ?? '/assets/img/avatar-default.jpg'; // default avatar
      $message = $data['data']->content;

      $pos = 'left'; // default position
      $lastParticipant = $dbconn->query("SELECT sender_id FROM messages ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_COLUMN);
      $pos = ($lastParticipant == $data['data']->sender_id) ? 'left' : 'right'; // alternate position based on participant count
      $data = sprintf('data: {"sender": "%s", "message": "%s", "pos": "%s"}', $sender, $message, $pos);
      echo "event: ping\n";
      //echo 'data: {"sender": "' . $sender . '", "message": "' . $message . ', "pos": "' . $pos . '"}' . "\n\n";
      echo $data;
      echo "\n\n";
    }
  } catch (Exception $e) {
    // handle exception if needed
    continue;
  }

  flush();
  if (connection_aborted())
    break;
}

// $counter = rand(1, 10);
// while (true) {
//   // Every second, send a "ping" event.

//   echo "event: ping\n";
//   $curDate = date(DATE_ISO8601);
//   echo 'data: {"time": "' . $curDate . '"}';
//   echo "\n\n";

//   // Send a simple message at random intervals.

//   $counter--;

//   if (!$counter) {
//     echo 'data: This is a message at time ' . $curDate . "\n\n";
//     $counter = rand(1, 10);
//   }

//   if (ob_get_contents()) {
//     ob_end_flush();
//   }
//   flush();

//   // Break the loop if the client aborted the connection (closed the page)

//   if (connection_aborted())
//     break;

//   sleep(10);
// }
