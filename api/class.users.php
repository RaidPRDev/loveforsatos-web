<?php
/**
 * Created by IntelliJ IDEA.
 * User: fania
 * Date: 9/15/2018
 * Time: 10:00 AM
 */

/*
CREATE TABLE `users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `role` enum('admin','team') NOT NULL,
 `name` varchar(220) NOT NULL,
 `username` varchar(100) NOT NULL,
 `password` varchar(100) NOT NULL,
 `pin` varchar(5) DEFAULT NULL,
 `email` varchar(200) NOT NULL,
 `updated` datetime DEFAULT current_timestamp(),
 `created` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8
 */

class USERS
{
    private $db;
    private $db_table;
    private $user_id;
    private $username;
    private $password;
    private $pin;

    function __construct($DB_con, $db_table)
    {
        $this->db = $DB_con;
        $this->db_table = $db_table;
    }

    public function register($userInfo)
    {
        // check if user exists
        $userExists = $this->userExists($userInfo);
        if (!$userExists['success']) return $userExists;

        // check if email exists
        if (isset($userInfo['email']))
        {
            $emailExists = $this->validateEmail($userInfo['email']);
            if (!$emailExists['success']) return $emailExists;
        }

        // now register, add new database item
        $count = 1;
        $total = count($userInfo);

        // add insert header
        $db_statement = "INSERT INTO " . $this->db_table . "(";
        foreach($userInfo as $key=>$value)
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
        foreach($userInfo as $key=>$value)
        {
            $db_statement .= ":" . $key;

            if ($count < $total)  $db_statement .= ", ";
            else $db_statement .= ")";

            $count++;
        }

        // echo 'db_statement: ' . $db_statement . '<br><br>';

        try
        {
            $stmt = $this->db->prepare($db_statement);

            foreach($userInfo as $key=>$value)
            {
                $bindVal = ":" . $key;
                $stmt->bindValue($bindVal, $userInfo[$key]);
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

    public function signIn($userInfo)
    {
        try
        {
            $stmt = $this->db->prepare("SELECT *
				FROM " . $this->db_table . " WHERE username=:username AND password=:password LIMIT 0,1");

            $stmt->bindValue(':username', $userInfo['username'], PDO::PARAM_STR);
            $stmt->bindValue(':password', $userInfo['password'], PDO::PARAM_STR);

            if ($stmt->execute())
            {
                $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                unset($list[0]['password']);
                if (count($list) == 1)
                {
                    $response["success"] = true;
                    $response["userInfo"] = $list[0];
                }
                else
                {
                    $response["success"] = false;
                    $response["error"] = "Wrong username or password, try again.";
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

    public function userExists($userInfo)
    {
        try
        {
            $stmt = $this->db->prepare("SELECT *
				FROM " . $this->db_table . " WHERE username=:username LIMIT 0,1");

            $stmt->bindValue(':username', $userInfo['username'], PDO::PARAM_STR);

            if ($stmt->execute())
            {
                $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($list) > 0)
                {
                    $response["success"] = false;
                    $response["error"] = 'Username is already being used.';
                }
                else
                {
                    $response["success"] = true;
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

    public function emailExists($userInfo)
    {
        try
        {
            $stmt = $this->db->prepare("SELECT *
				FROM " . $this->db_table . " WHERE email=:email LIMIT 0,1");

            $stmt->bindValue(':email', $userInfo['email'], PDO::PARAM_STR);

            if ($stmt->execute())
            {
                $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($list) > 0)
                {
                    $response["success"] = false;
                    $response["error"] = 'Email is already being used.';
                }
                else
                {
                    $response["success"] = true;
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

    public function update($userInfo)
    {
        // remove ajax key
        unset($userInfo['ajax']);

        $count = 1;
        $total = count($userInfo);

        // add insert header
        $db_statement = "UPDATE " . $this->db_table . " SET ";
        foreach($userInfo as $key=>$value)
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

            foreach($userInfo as $key=>$value)
            {
                $bindVal = ":" . $key;
                $stmt->bindValue($bindVal, $userInfo[$key]);
            }

            $stmt->bindValue(':itemID', $userInfo['id'], PDO::PARAM_INT);

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

    public function getUser($uid)
    {
        try
        {
            $stmt = $this->db->prepare("SELECT *
				FROM " . $this->db_table . " WHERE id = :id LIMIT 0,1");

            $stmt->bindValue(':id', $uid, PDO::PARAM_INT);

            if ($stmt->execute())
            {
                $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($list) > 0)
                {
                    $response["success"] = true;
                    $response["userInfo"] = $list[0];
                }
                else
                {
                    $response["success"] = false;
                    $response["error"] = "No user found";
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

    public function getAllUsers()
    {
        try
        {
            $stmt = $this->db->prepare("SELECT *
				FROM " . $this->db_table);

            if ($stmt->execute())
            {
                $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($list) > 0)
                {
                    $response["success"] = true;
                    $response["users"] = $list;
                }
                else
                {
                    $response["success"] = false;
                    $response["error"] = "No users found";
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

    public function getUsersByRole($role)
    {
        try
        {
            $stmt = $this->db->prepare("SELECT *
				FROM " . $this->db_table . " WHERE role = :role");

            $stmt->bindValue(':role', $role, PDO::PARAM_INT);

            if ($stmt->execute())
            {
                $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($list) > 0)
                {
                    $response["success"] = true;
                    $response["users"] = $list;
                }
                else
                {
                    $response["success"] = false;
                    $response["error"] = "No users found";
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

    public function removeUserByID($id)
    {
        try
        {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :uid");

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

    public function validateEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            return array("success"=>false, "error"=>"$email is not a valid email address");
        }

        return array("success"=>true);
    }

}