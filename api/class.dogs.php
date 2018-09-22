<?php

include 'class.photos.php';

class DOGS
{
	private $db;
	
	function __construct($DB_con)
	{
		$this->db = $DB_con;
	}
	
	public function trace($msg)
	{
		echo "Logger: [ " . $msg . "]";
	}
	
	public function generatePassword($name)
	{
		$new_date = date("h:i:sa");
		$new_password = MD5($name . $new_date);
		$final_password = strtoupper( substr($new_password, 0, 6) );
		
		return $final_password;
	}
	
	/*
	$itemInfo['id'] = $_GET['id'];
	$itemInfo['name'] = $_GET['name'];
	$itemInfo['age'] = $_GET['age'];
	$itemInfo['gender'] = $_GET['gender'];
	$itemInfo['description'] = $_GET['description'];
	$itemInfo['history'] = $_GET['history'];
	*/
	public function addItem($itemInfo)
	{
	    // remove ajax key
        unset($itemInfo['ajax']);

        $count = 1;
        $total = count($itemInfo);

        // add insert header
        $db_statement = "INSERT INTO dogs(";
        foreach($itemInfo as $key=>$value)
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
        foreach($itemInfo as $key=>$value)
        {
            $db_statement .= ":" . $key;

            if ($count < $total)  $db_statement .= ", ";
            else $db_statement .= ")";

            $count++;
        }

        try
		{
			$stmt = $this->db->prepare($db_statement);

            foreach($itemInfo as $key=>$value)
            {
                $bindVal = ":" . $key;
                $stmt->bindValue($bindVal, $itemInfo[$key]);
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

    public function fetchAllItemsInIds($idList, $statement = 'ORDER BY id', $sortOrder = 'ASC', $startId = 0, $max = 5000)
    {
        $db_statement = "SELECT * FROM dogs ";
        $db_statement .= "WHERE id IN ( '" . implode( "', '" , $idList ) . "' ) ";
        $db_statement .= " $statement $sortOrder";
        $db_statement .= " LIMIT $max OFFSET $startId";

        try
        {
            $stmt = $this->db->prepare($db_statement);

            if ($stmt->execute())
            {
                $response["success"] = true;
                $response["items"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $photos = new PHOTOS($this->db, null);
                $count = count($response["items"]);

                for ($i = 0; $i < $count; $i++)
                {
                    $item = $response["items"][$i];
                    $photoList = $photos->getPhotosByItemID($item["id"]);

                    if ($photoList['success'])
                    {
                        $response["items"][$i]['photos'] = $photoList['items'];
                    }
                }
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

    public function fetchAllItems($statement = 'ORDER BY id', $sortOrder = 'ASC', $startId = 0, $max = 5000)
	{
        $db_statement = "SELECT * FROM dogs ";
        $db_statement .= " $statement $sortOrder";
        // $db_statement .= " ORDER BY $orderBy $sortOrder";
        $db_statement .= " LIMIT $max OFFSET $startId";

		try
		{
			//$stmt = $this->db->prepare("SELECT *
			//	FROM dogs ORDER BY id DESC");

            $stmt = $this->db->prepare($db_statement);

			if ($stmt->execute()) 
			{
				$response["success"] = true;
				$response["items"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // highlight_string( var_export($response["items"], true));

                $photos = new PHOTOS($this->db, null);
                $count = count($response["items"]);

                for ($i = 0; $i < $count; $i++)
                {
                    $item = $response["items"][$i];
                    $photoList = $photos->getPhotosByItemID($item["id"]);
                    // highlight_string( var_export($photoList, true));
                    if ($photoList['success'])
                    {
                        $response["items"][$i]['photos'] = $photoList['items'];
                    }
                }
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

    public function fetchAllItemsWithPhotos($selectedItems)
    {
        try
        {
            $db_statement = "SELECT ";
            // add fields
            $db_statement .= "dogs.id AS dog_id, ";
            $db_statement .= "dogs.name AS dog_name, ";
            $db_statement .= "dogs.adopted AS dog_adopted, ";
            $db_statement .= "dogs.age AS dog_age, ";
            $db_statement .= "dogs.fixed AS dog_fixed, ";
            $db_statement .= "dogs.gender AS dog_gender, ";

            // select (1) image url from photos
            $db_statement .= "( ";
            $db_statement .= "SELECT thumb_image_url ";
            $db_statement .= "FROM photos ";
            $db_statement .= "WHERE dog_id = dogs.id ";
            $db_statement .= "LIMIT 1 ";
            $db_statement .= ") AS thumb_image_url  ";

            $db_statement .= "FROM dogs ";
            $db_statement .= "WHERE dogs.id NOT IN ( '" . implode( "', '" , $selectedItems ) . "' ) AND dogs.adopted = 'no'";
            $db_statement .= "ORDER BY dogs.updated DESC";

            $stmt = $this->db->prepare($db_statement);

            if ($stmt->execute())
            {
                $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($list) > 0)
                {
                    $response["success"] = true;
                    $response["items"] = $list;
                }
                else
                {
                    $response["success"] = false;
                    $response["error"] = "No items found";
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

	public function fetchItemById($itemID)
	{
		try
		{
			$stmt = $this->db->prepare("SELECT *
				FROM dogs 
				WHERE id = :itemID");
				
			$stmt->bindValue(':itemID', $itemID, PDO::PARAM_INT);
	
			if ($stmt->execute())  
			{
				$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				if (count($items) >= 1)
				{
					$response["success"] = true;
					$response["item"] = $items[0];

					// get associated photo list
                    // $photos = $this->getPhotosByItemID($response["item"]["id"]);
                    $photos = new PHOTOS($this->db, null);
                    $photoList = $photos->getPhotosByItemID($items[0]["id"]);

                    if ($photoList["success"])
                    {
                        $response["photos"] = $photoList;
                    }
                    else
                    {
                        $response["success"] = false;
                        $response["error"] = "Error getting photos.";
                    }
                }
				else
				{
					$response["success"] = false;
					$response["error"] = "Error getting info.";	
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

	public function getPhotosByItemID($itemID)
    {
        try
        {
            $stmt = $this->db->prepare("SELECT *
				FROM photos 
				WHERE dog_id = :itemID");

            $stmt->bindValue(':itemID', $itemID, PDO::PARAM_INT);

            if ($stmt->execute())
            {
                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $response["success"] = true;
                $response["photos"] = $items;
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

    /*
        $itemInfo['id'] = $_GET['id'];
        $itemInfo['name'] = $_GET['name'];
        $itemInfo['age'] = $_GET['age'];
        $itemInfo['gender'] = $_GET['gender'];
        $itemInfo['description'] = $_GET['description'];
        $itemInfo['history'] = $_GET['history'];

    $stmt = $this->db->prepare("UPDATE dogs SET
					name = :name,
					description = :description,
					history = :history,
					age = :age,
					gender = :gender
				WHERE id = :uid");
        */
    public function updateItem($itemID, $itemInfo)
    {
        // remove ajax key
        unset($itemInfo['ajax']);

        $count = 1;
        $total = count($itemInfo);

        // add insert header
        $db_statement = "UPDATE dogs SET ";
        foreach($itemInfo as $key=>$value)
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

            foreach($itemInfo as $key=>$value)
            {
                $bindVal = ":" . $key;
                $stmt->bindValue($bindVal, $itemInfo[$key]);
            }

            $stmt->bindValue(':itemID', $itemID, PDO::PARAM_INT);

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

	public function updatePromise($itemInfo)
    {
        try
        {
            $stmt = $this->db->prepare("UPDATE dogs SET 
					adopted = :adopted,
					adopted_by = :adopted_by
				WHERE id = :uid");

            $stmt->bindValue(':adopted', $itemInfo['adopted'], PDO::PARAM_STR);
            $stmt->bindValue(':adopted_by', $itemInfo['adopted_by'], PDO::PARAM_INT);
            $stmt->bindValue(':uid', $itemInfo['id'], PDO::PARAM_INT);

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

    public function fetchPromiseByID($uid)
    {
        try
        {
            $stmt = $this->db->prepare("SELECT 
                adopted,
                adopted_by
				FROM dogs 
				WHERE id = :uid");

            $stmt->bindValue(':uid', $uid, PDO::PARAM_INT);

            if ($stmt->execute())
            {
                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($items) > 0)
                {
                    $response["success"] = true;
                    $response["item"] = $items[0];
                }
                else
                {
                    $response["success"] = false;
                    $response["error"] = 'Could not fetch data';
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

	public function removeItemByID($id)
	{
		try
		{
			$stmt = $this->db->prepare("DELETE FROM dogs WHERE id = :uid");
			
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
}
?>