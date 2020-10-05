<?php
	class User {
 	
		private $conn;
		private $table_name = "users";
		private $table_messages = "messages";
		
		public $id;
		public $name;
		public $email;
		public $message;
		
		public function __construct($db) {
			$this->conn = $db;
		}

		function create() {
	    
			$query = "INSERT INTO " . $this->table_name . "
					  SET
						name = :name,
						email = :email";
	    
			$stmt = $this->conn->prepare($query);
	    
			$this->name=htmlspecialchars(strip_tags($this->name));
			$this->email=htmlspecialchars(strip_tags($this->email));
			
			$stmt->bindParam(':name', $this->name);
			$stmt->bindParam(':email', $this->email);
	  
			if($stmt->execute()) {
				return true;
			}

			return false;
		}

		function userExist(){
		 
			$query = "SELECT 
						id_usr, name
					  FROM " . $this->table_name . "
					  WHERE 
						email = :email";
		 
			$stmt = $this->conn->prepare( $query );
	
			$this->email=htmlspecialchars(strip_tags($this->email));
	
			$stmt->bindParam(':email', $this->email);
		 
			$stmt->execute();
		 
			$num = $stmt->rowCount();
		
			if($num>0) {
		 
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
		 		
				$this->id = $row['id_usr'];
				$this->name = $row['name'];
		 
				return true;
		    }
		 
			return false;
		}
		 
		public function update(){		 
			$query = "UPDATE " . $this->table_name . "
					SET
						name = :name,
						email = :email
					WHERE 
						id_usr = :id";
		
			$stmt = $this->conn->prepare($query);
		 
			$this->name=htmlspecialchars(strip_tags($this->name));
			$this->email=htmlspecialchars(strip_tags($this->email));
		 
			$stmt->bindParam(':name', $this->name);
			$stmt->bindParam(':email', $this->email);
			$stmt->bindParam(':id', $this->id);
			
			if($stmt->execute()) {
		        return true;
			}
		
			return false;
		}

		function sendMessage(){
			$query = "INSERT INTO messages
					SET 
						message = :message,
						email_usr = :email_usr";
			$msg = $this->conn->prepare($query);
	    
			$this->message=htmlspecialchars(strip_tags($this->message));
			$this->email=htmlspecialchars(strip_tags($this->email));
			
			$msg->bindParam(':message', $this->message);
			$msg->bindParam(':email_usr', $this->email);
			
			if($msg->execute()) {
				return true;
			}

			return false;
		}

	}
?>