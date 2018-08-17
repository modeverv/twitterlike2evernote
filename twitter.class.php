<?php
class Twitter
{
    public $json_obj = null;
    private $pdo = null;
    public $id = null;
    public $json = null;

    public function __construct(string $json_str, PDO $pdo)
    {
        $this->json_obj = json_decode($json_str);
        $this->setDb($pdo);
    }

    private function setDb(PDO $PDOobj)
    {
        $this->pdo = $PDOobj;
    }

    public function set_to_db()
    {
        foreach ($this->json_obj as $tweet_obj) {
            if ($this->is_exists_in_db($tweet_obj)) {
                continue;
            }
            $this->insert_to_db($tweet_obj);
        }
    }

    public function insert_to_db($tweet_obj)
    {
        $id = $tweet_obj->id;
        $body = json_encode($tweet_obj);
        $evernote_set = 0;
        $txt = $tweet_obj->text;
        $created_at = strtotime($tweet_obj->created_at);
        echo $created_at . "\n";
        $sth = $this->pdo->prepare(
            "insert into twitter(id_str,body,evernote_set,txt,created_at) "
            . "values(?,?,?,?,?)"
        );
        $sth->execute([$id, $body, $evernote_set, $txt, $created_at]);
        echo "inserted : $id\n";
    }

    private function is_exists_in_db($tweet_obj): bool
    {
$sth = $this->pdo->prepare('select count(*) as cou from twitter where id_str = ?');
$sth->execute([$tweet_obj->id]);
$result = $sth->fetchAll();
        if ($result[0]["cou"] > 0) {
            return true;
        } else {
            return false;
        }
    }

}
