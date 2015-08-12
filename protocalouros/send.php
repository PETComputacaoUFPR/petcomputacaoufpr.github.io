<?php
if(isset($_POST['fields'])){
	$fields =  $_POST['fields'];

	if(isset($fields['name']) && isset($fields['email']) && isset($fields['met']))
	{
		$name = $fields['name'];
		$email = $fields['email'];
		$met = $fields['met'];


		$db = new SQLite3('../../_data/data.db');

		if($db){
			$stmt = $db->prepare("INSERT INTO inscricao (email,name,met) VALUES (:email,:name,:met)");
			$stmt->bindValue(':email',$email,SQLITE3_TEXT);
			$stmt->bindValue(':name',$name,SQLITE3_TEXT);
			$stmt->bindValue(':met',$met,SQLITE3_TEXT);

			$result = $stmt->execute();
			$db->close();
			echo json_encode(true);
		}else{
			echo json_encode(false);
		}
	}else{
		echo json_encode(false);
}
}
