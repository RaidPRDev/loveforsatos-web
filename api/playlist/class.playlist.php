<?php
/**
 * Created by IntelliJ IDEA.
 * User: fania
 * Date: 9/18/2018
 * Time: 8:19 PM
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

class PLAYLIST
{
    private $db;
    private $db_table;

    function __construct($DB_con, $db_table)
    {
        $this->db = $DB_con;
        $this->db_table = $db_table;
    }

    public function validateToken($token)
    {
        try
        {
            $db_statement = "SELECT ";
            // add fields
            $db_statement .= "pl.id, ";
            $db_statement .= "pl.token, ";
            $db_statement .= "pl.user_id, ";
            $db_statement .= "pl.team_id ";
            $db_statement .= "FROM  " . $this->db_table . " AS pl ";

            $db_statement .= "WHERE pl.token = :token ";

            $stmt = $this->db->prepare($db_statement);
            $stmt->bindValue(':token', $token, PDO::PARAM_STR);

            if ($stmt->execute())
            {
                $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($list) > 0)
                {
                    $response["success"] = true;
                    $response["playlist"] = $list[0];
                }
                else
                {
                    $response["success"] = false;
                    $response["error"] = "No playlist found";
                }
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }
        }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();
        }

        return $response;


    }

    public function create($info)
    {
        // now register, add new database item
        $count = 1;
        $total = count($info);

        // add insert header
        $db_statement = "INSERT INTO " . $this->db_table . "(";
        foreach($info as $key=>$value)
        {
            $db_statement .= $key;

            if ($count < $total)  $db_statement .= ", ";
            else $db_statement .= ") ";

            $count++;
        }

        // reset count
        $count = 1;

        // add values info
        $db_statement .= "VALUES(";
        foreach($info as $key=>$value)
        {
            $db_statement .= ":" . $key;

            if ($count < $total)  $db_statement .= ", ";
            else $db_statement .= ")";

            $count++;
        }

        try
        {
            $stmt = $this->db->prepare($db_statement);

            foreach($info as $key=>$value)
            {
                $bindVal = ":" . $key;
                $stmt->bindValue($bindVal, $info[$key]);
            }

            if ($stmt->execute())
            {
                $response["success"] = true;
                $response["lastInsertId"] = $this->db->lastInsertId(); // Get the newly created ID
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }
        }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();
        }

        return $response;
    }

    public function update($info)
    {
        // remove ajax key
        unset($info['ajax']);

        $count = 1;
        $total = count($info);

        // add insert header
        $db_statement = "UPDATE " . $this->db_table . " SET ";
        foreach($info as $key=>$value)
        {
            $db_statement .= $key . " = :" . $key;

            if ($count < $total)  $db_statement .= ", ";
            else $db_statement .= " ";

            $count++;
        }

        // reset count
        $count = 1;

        // add values info
        $db_statement .= "WHERE id = :itemID";

        try
        {
            $stmt = $this->db->prepare($db_statement);

            foreach($info as $key=>$value)
            {
                $bindVal = ":" . $key;
                $stmt->bindValue($bindVal, $info[$key]);
            }

            $stmt->bindValue(':itemID', $info['id'], PDO::PARAM_INT);

            if ($stmt->execute())
            {
                $response["success"] = true;
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }
        }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();

        }

        return $response;
    }

    public function getPlaylistByID($uid)
    {
        try
        {
            $db_statement = "SELECT ";
            // add fields
            $db_statement .= "pl.id AS id, ";
            $db_statement .= "pl.token AS token, ";
            $db_statement .= "pl.description AS description, ";
            $db_statement .= "team.id AS team_id, ";
            $db_statement .= "team.name AS team_name ";
            $db_statement .= "FROM  " . $this->db_table . " AS pl ";

            // add team user
            $db_statement .= "INNER JOIN users AS team ";
            $db_statement .= "ON pl.team_id = team.id ";

            $db_statement .= "WHERE pl.id = :id ";

            $stmt = $this->db->prepare($db_statement);
            $stmt->bindValue(':id', $uid, PDO::PARAM_INT);

            if ($stmt->execute())
            {
                $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($list) > 0)
                {
                    $response["success"] = true;
                    $response["playlist"] = $list[0];
                }
                else
                {
                    $response["success"] = false;
                    $response["error"] = "No playlist item found";
                }
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }
        }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();
        }

        return $response;
    }

    public function getAllPlaylistsByID($uid)
    {
        try
        {
            //$stmt = $this->db->prepare("SELECT *
			//	FROM " . $this->db_table . " WHERE id = :id");


            $db_statement = "SELECT ";
            $db_statement .= "pl.id as playlist_id, ";
            $db_statement .= "pl.token, ";
            $db_statement .= "pl.user_id, ";
            $db_statement .= "pl.team_id, ";
            $db_statement .= "( ";
            $db_statement .= "SELECT name ";
            $db_statement .= "FROM users ";
            $db_statement .= "WHERE id = pl.team_id ";
            $db_statement .= "LIMIT 1 ";
            $db_statement .= ") AS team_name, ";
            $db_statement .= "pl.description ";

            $db_statement .= "FROM  " . $this->db_table . " AS pl ";
            $db_statement .= "WHERE pl.id = :id ";

            $stmt = $this->db->prepare($db_statement);
            $stmt->bindValue(':id', $uid, PDO::PARAM_INT);

            if ($stmt->execute())
            {
                $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($list) > 0)
                {
                    $response["success"] = true;
                    $response["playlist"] = $list;
                }
                else
                {
                    $response["success"] = false;
                    $response["error"] = "No playlist items found";
                }
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }
        }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();
        }

        return $response;
    }

    public function getAllPlaylistsByUserID($user_id)
    {
        try
        {
            $db_statement = "SELECT ";
            // add fields
            $db_statement .= "pl.id AS playlist_id, ";
            $db_statement .= "pl.token AS token, ";
            $db_statement .= "pl.description AS description, ";
            $db_statement .= "u.id AS u_id, ";
            $db_statement .= "u.name AS u_name, ";
            $db_statement .= "team.id AS team_id, ";
            $db_statement .= "team.name AS team_name ";
            $db_statement .= "FROM  " . $this->db_table . " AS pl ";

            // add users
            $db_statement .= "INNER JOIN users AS u ";
            $db_statement .= "ON pl.user_id = u.id ";

            // add team users
            $db_statement .= "INNER JOIN users AS team ";
            $db_statement .= "ON pl.team_id = team.id ";
            $db_statement .= "WHERE pl.user_id = :id ";
            $db_statement .= "ORDER BY playlist_id ASC";

            $stmt = $this->db->prepare($db_statement);
            $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);

            if ($stmt->execute())
            {
                $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($list) > 0)
                {
                    $response["success"] = true;
                    $response["playlist"] = $list;
                }
                else
                {
                    $response["success"] = false;
                    $response["error"] = "No playlist items found";
                }
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }
        }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();
        }

        return $response;
    }

    public function getAllPlaylistsWithEntriesByID($playlist_id)
    {
        try
        {
            $db_statement = "SELECT ";
            // add fields
            $db_statement .= "pl.id AS playlist_id, ";
            $db_statement .= "pl.token AS token, ";
            $db_statement .= "pl.description AS description, ";
            $db_statement .= "u.id AS u_id, ";
            $db_statement .= "u.name AS u_name, ";
            $db_statement .= "team.id AS team_id, ";
            $db_statement .= "team.name AS team_name, ";
            $db_statement .= "ple.id AS ple_id, ";
            $db_statement .= "ple.dog_id AS dog_id, ";
            $db_statement .= "dogs.name AS dog_name, ";
            $db_statement .= "dogs.gender AS dog_gender, ";
            $db_statement .= "dogs.fixed AS dog_fixed, ";
            $db_statement .= "dogs.age AS dog_age, ";
            $db_statement .= "dogs.adopted AS dog_adopted, ";

            // select (1) image url from photos
            $db_statement .= "( ";
            $db_statement .= "SELECT full_image_url ";
            $db_statement .= "FROM photos ";
            $db_statement .= "WHERE dog_id = ple.dog_id ";
            $db_statement .= "LIMIT 1 ";
            $db_statement .= ") AS thumb_image_url  ";

            $db_statement .= "FROM  " . $this->db_table . " AS pl ";

            // add user
            $db_statement .= "INNER JOIN users AS u ";
            $db_statement .= "ON pl.user_id = u.id ";

            // add team user
            $db_statement .= "INNER JOIN users AS team ";
            $db_statement .= "ON pl.team_id = team.id ";

            // add entries
            $db_statement .= "INNER JOIN playlist_entries AS ple ";
            $db_statement .= "ON pl.id = ple.playlist_id ";

            // add dogs
            $db_statement .= "INNER JOIN dogs AS dogs ";
            $db_statement .= "ON ple.dog_id = dogs.id AND dogs.adopted = 'no'";

            $db_statement .= "WHERE pl.id = :id ";
            $db_statement .= "ORDER BY playlist_id ASC";

            $stmt = $this->db->prepare($db_statement);
            $stmt->bindValue(':id', $playlist_id, PDO::PARAM_INT);

            if ($stmt->execute())
            {
                $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($list) > 0)
                {
                    $response["success"] = true;
                    $response["playlist"] = $list;
                }
                else
                {
                    $response["success"] = false;
                    $response["error"] = "No playlist items found";
                }
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }
        }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();
        }

        return $response;
    }

    public function getAllPlaylistEntriesWithPromiseByID($playlist_id)
    {
        try
        {
            $db_statement = "SELECT ";
            // add fields
            $db_statement .= "pl.id AS playlist_id, ";
            $db_statement .= "pl.token AS token, ";
            $db_statement .= "pl.description AS description, ";
            $db_statement .= "u.id AS u_id, ";
            $db_statement .= "u.name AS u_name, ";
            $db_statement .= "team.id AS team_id, ";
            $db_statement .= "team.name AS team_name, ";
            $db_statement .= "ple.id AS ple_id, ";
            $db_statement .= "ple.dog_id AS dog_id, ";
            $db_statement .= "dogs.name AS dog_name, ";
            $db_statement .= "dogs.gender AS dog_gender, ";
            $db_statement .= "dogs.fixed AS dog_fixed, ";
            $db_statement .= "dogs.age AS dog_age, ";
            $db_statement .= "dogs.adopted AS dog_adopted, ";

            // select (1) image url from photos
            $db_statement .= "( ";
            $db_statement .= "SELECT full_image_url ";
            $db_statement .= "FROM photos ";
            $db_statement .= "WHERE dog_id = ple.dog_id ";
            $db_statement .= "LIMIT 1 ";
            $db_statement .= ") AS thumb_image_url  ";

            $db_statement .= "FROM  " . $this->db_table . " AS pl ";

            // add user
            $db_statement .= "INNER JOIN users AS u ";
            $db_statement .= "ON pl.user_id = u.id ";

            // add team user
            $db_statement .= "INNER JOIN users AS team ";
            $db_statement .= "ON pl.team_id = team.id ";

            // add entries
            $db_statement .= "INNER JOIN playlist_entries AS ple ";
            $db_statement .= "ON pl.id = ple.playlist_id ";

            // add dogs
            $db_statement .= "INNER JOIN dogs AS dogs ";
            $db_statement .= "ON ple.dog_id = dogs.id ";

            $db_statement .= "WHERE pl.id = :id ";
            $db_statement .= "ORDER BY playlist_id ASC";

            $stmt = $this->db->prepare($db_statement);
            $stmt->bindValue(':id', $playlist_id, PDO::PARAM_INT);

            if ($stmt->execute())
            {
                $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($list) > 0)
                {
                    $response["success"] = true;
                    $response["playlist"] = $list;
                }
                else
                {
                    $response["success"] = false;
                    $response["error"] = "No playlist items found";
                }
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }
        }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();
        }

        return $response;
    }

    public function addEntry($info)
    {
        // now register, add new database item
        $count = 1;
        $total = count($info);

        // add insert header
        $db_statement = "INSERT INTO playlist_entries(";
        foreach($info as $key=>$value)
        {
            $db_statement .= $key;

            if ($count < $total)  $db_statement .= ", ";
            else $db_statement .= ") ";

            $count++;
        }

        // reset count
        $count = 1;

        // add values info
        $db_statement .= "VALUES(";
        foreach($info as $key=>$value)
        {
            $db_statement .= ":" . $key;

            if ($count < $total)  $db_statement .= ", ";
            else $db_statement .= ")";

            $count++;
        }

        try
        {
            $stmt = $this->db->prepare($db_statement);

            foreach($info as $key=>$value)
            {
                $bindVal = ":" . $key;
                $stmt->bindValue($bindVal, $info[$key]);
            }

            if ($stmt->execute())
            {
                $response["success"] = true;
                $response["lastInsertId"] = $this->db->lastInsertId(); // Get the newly created ID
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }
        }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();
        }

        return $response;
    }

    public function playEntryExistByPlaylistID($playlistID, $dogID)
    {
        try
        {
            $db_statement = "SELECT ";
            // add fields
            $db_statement .= "id, ";
            $db_statement .= "playlist_id, ";
            $db_statement .= "dog_id ";
            $db_statement .= "FROM playlist_entries ";
            $db_statement .= "WHERE playlist_id = :playlist_id AND dog_id = :dog_id";

            $stmt = $this->db->prepare($db_statement);
            $stmt->bindValue(':playlist_id', $playlistID, PDO::PARAM_INT);
            $stmt->bindValue(':dog_id', $dogID, PDO::PARAM_INT);

            if ($stmt->execute())
            {
                $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($list) > 0)
                {
                    $response["success"] = true;
                    $response["playlist"] = $list[0];
                }
                else
                {
                    $response["success"] = false;
                    $response["error"] = "No entries found";
                }
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }
        }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();
        }

        return $response;
    }

    public function removePlaylistByID($id)
    {
        try
        {
            $stmt = $this->db->prepare("DELETE FROM " . $this->db_table . " WHERE id = :uid");

            $stmt->bindValue(':uid', $id, PDO::PARAM_INT);

            if ($stmt->execute())
            {
                $response["success"] = true;
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }

            return $response;
        }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();
            return $response;
        }
    }

    public function removePlaylistEntriesByID($playlist_id, $table_entries)
    {
        try
        {
            $stmt = $this->db->prepare("DELETE FROM " . $table_entries . " WHERE playlist_id = :pid");

            $stmt->bindValue(':pid', $playlist_id, PDO::PARAM_INT);

            if ($stmt->execute())
            {
                $response["success"] = true;
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }

            return $response;
        }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();
            return $response;
        }
    }


}