<?php

// Fonctions Debug 
function debug($tableau)
{
  echo '<pre>';
    print_r($tableau);
  echo '</pre>';
}

// Fonctions Securité
function cleanXss($toClean){
   return trim(strip_tags($toClean));
}

// Fonction SQL 
function SQL_INSERT($table_name,$columns,$values,$debug = false) {
  $incre = 1; // Variable d'incrementation pour la creations de varaible dynamique 
  //Completion automatique de la requete SQL
  $sql = "INSERT INTO $table_name ($columns) VALUES (";
  foreach ($values as $value) {
    ${'val_'.$incre} = $value; //Creation d'une variable dynamique pour chaque elements dans l'array $value
    if (substr($sql, -1) == '(') {
      $sql .= ':val_'.$incre;
    } else {
      $sql .= ',:val_'.$incre;
    }
    $incre += 1;
  }
  $sql .= ')';
  global $pdo;
  $query = $pdo->prepare($sql);
  $incre = 1;
  foreach ($values as $value) {
    $query->bindValue(':val_'.$incre,${'val_'.$incre},PDO::PARAM_STR);
    $incre += 1;
  }
  $query->execute();
}

function SQL_SELECT($table_name,$fetchall = false,$param = '',$value = '',$debug = false) {
  // Verification si where
  if (!empty($param)) {
    $piece = explode(' ',$param);
    $name = $piece[1];
    $sql = "SELECT * FROM $table_name ".$param;
    echo $sql;
    global $pdo;
    $query = $pdo->prepare($sql);
    $query->bindValue(':'.$name,$value,PDO::PARAM_STR);
    $query->execute();
    if($debug) {
      if ($fetchall) {
        debug($query->fetchall());
      } else {
        debug($query->fetch());
      }
    } else {
      if ($fetchall) {
        return $query->fetchall();
      } else {
        return $query->fetch();
      }
    }
  }else {
    $sql = "SELECT * FROM $table_name";
    global $pdo;
    $query = $pdo->prepare($sql);
    $query->execute();
    if($debug) {
      debug($query->fetchall());
    } else {
      return $query->fetchall();
    }
  }
}
// Fonctions verifications
function is_logged(): bool
{
  $isLogged = true;
  if (empty($_SESSION['user'])) {
    return $isLogged = false;
  } else {
    foreach ($_SESSION['user'] as $key => $value) {
      if (!isset($key) && !isset($value)) {
        return $isLogged = false;

      }
    }
  }
  return $isLogged;
}

function isAdmin()
{
  if (!is_logged()) {
    header('Location: ../admin/403.php');
  } elseif ($_SESSION['user']['role'] != 'admin') {
    header('Location: ../admin/403.php');
  }
}