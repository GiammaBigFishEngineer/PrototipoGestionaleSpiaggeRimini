<?php
require_once(__ROOT__ . '/config/DB.php');

/*
La seguente funzione crea una tabella passando i parametri della classe
Se la tabella esiste già l'aggiorna

$fields = [
  "name" => "VARCHAR(255)",
  "email" => "VARCHAR(255)"
];
createTable("users", $fields);
*/
function createTable(string $tableName, array $fields) {
     // Verifica se la tabella esiste già
  $tableExists = false;
  $existingFields = array();

  try {
    $stmt = DB::get()->prepare("DESCRIBE $tableName");
    $stmt->execute();
    $existingFields = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $tableExists = true;
  } catch (PDOException $e) {
    // La tabella non esiste ancora
  }

  // Costruisce la query SQL per creare o modificare la tabella
  $sql = "CREATE TABLE $tableName (";

  foreach ($fields as $fieldName => $fieldInfo) {
    $sql .= "$fieldName {$fieldInfo['type']}";

    if (isset($fieldInfo['length'])) {
      $sql .= "({$fieldInfo['length']})";
    }

    if (isset($fieldInfo['notnull']) && $fieldInfo['notnull'] === true) {
      $sql .= " NOT NULL";
    }

    if (isset($fieldInfo['default'])) {
      $sql .= " DEFAULT '{$fieldInfo['default']}'";
    }

    if (isset($fieldInfo['autoincrement']) && $fieldInfo['autoincrement'] === true) {
      $sql .= " AUTO_INCREMENT";
    }

    $sql .= ",";
  }

  if ($tableExists) {
    // Rimuove eventuali campi esistenti che non sono più presenti nella nuova definizione
    $existingFields = array_intersect($existingFields, array_keys($fields));
    $sql .= " DROP " . implode(",", array_diff($existingFields, array_keys($fields)));
    $sql = rtrim($sql, ',');
    $sql .= ";";
    $sql = str_replace("CREATE TABLE", "ALTER TABLE", $sql);
  } else {
    $sql = rtrim($sql, ',');
    $sql .= ");";
  }

  // Esegue la query per creare o modificare la tabella
  DB::get()->exec($sql);
}