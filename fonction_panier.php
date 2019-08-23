<?php
session_start();
try
  {
 
  $bdd = new PDO('mysql:host=localhost;dbname=projet;charset=utf8', 'root', '');
  $bdd->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER); // Les noms de champ seront en minuscule
  $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Les erreurs lanceront des excetions
 
  }
 
  catch(Exception $e){
    die("Une erreur est survenue");
 
  }
?>
 
<!DOCTYPE html>
<html>
 <head>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="projet.css" />
  <title>Accueil</title>
 </head>
 <body>
    <div id="entete">
        <h1>BUYSAFE</h1>
         
    <div>
    <div id="navigation">
        <table>
            <th><td><a href="index.php">Accueil</a></td><td><a href="inscription.php">Inscription</a></td><td><a href="panier.php">Panier</a></td><td><a href="boutique.php">Boutique</a></td><td><a href="contact.php">Contactez-nous</a></td><td><a href="connexion.php">Connexion</a></td><td><a href="../soum/admin/index.php">Adminitrateur</a></td></th>
        </table>
    </div>
    <div id="menu">
        <ul>
             
            <li><a href="chaussures.php">Chaussures</a></li>
             
            <li><a href="habits.php">Habits</a></li>
            <li><a href="bricolage.php">Bricolage</a></li>
        </ul>
    </div>
    <div id="conteneur">
       <div id="p_index">
      <?php
      //inclusion du fichier des fonctions
      include("fonction_panier.php");
 
       $erreur = false;
       $action = (isset($_POST['action'])?$_POST['action']:(isset($_GET['action'])?$_GET['action']:null));
 
       if($action !== null){
         
         if(!in_array($action, array('ajout','suppression','refresh')))
 
          $erreur = true;
 
          $l = (isset($_POST['l'])?$_POST['l']:(isset($_GET['l'])?$_GET['l']:null));
          $q = (isset($_POST['q'])?$_POST['q']:(isset($_GET['q'])?$_GET['q']:null));
          $p = (isset($_POST['p'])?$_POST['p']:(isset($_GET['p'])?$_GET['p']:null));
 
          $l = preg_replace('#\v#', '', $l);
          $p = floatval($p);
 
          if(is_array($q)){
 
            $qteProduit = array();
            $i=0;
            foreach ($q as  $contenu) {
               
              $qteProduit[$i++] = intval($contenu);
            }
 
          }else{
 
            $q = intval($q);
          }
          
        
       }
 
       if(!$erreur){
        //L'erreur pourrait venir de là
 
        switch ($action){
 
          Case "ajout":
          //Appel à la fonction d'ajout
          ajouterProduit($l,$q,$p);
 
          break;
           
          Case "suppression":
 
          supprimerProduit($l);
 
          break;
 
          Case "refresh":
 
          for($i=0;$i<count($qteProduit);$i++){
 
            modifierQteProduit($_SESSION['panier']['libelleProduit'][$i], round($qteProduit));
          }
             
          break;
             
          Default:
               
          break;
           
        }
 
       }
      ?>
         
      <form method="post" action="">
        <table width="400">
          <tr>
            <td colspan="4">Votre panier</td>
          </tr>
          <tr>
            <td>Libelle du Produit</td>
            <td>Prix unitaire</td>
            <td>Quantité</td>
            <td>TVA</td>
            <td>Action</td>
          </tr>
         
      <?php
       
    if(isset($_GET['deletepanier']) && $_GET['deletepanier'] == true){
 
      supprimerPanier();
    }
 
    if(createPanier()){
       
 
      $nbProduit = count($_SESSION['panier']['libelleProduit']);
      if($nbProduit <= 0){
         
        echo "Oups votre panier est vide!!";
 
      }else{
        //j'ai des supçons sur cette "for" aussi
         
        for($i=0; $i<$nbProduit; $i++){
           ?>
           <tr>
 
            <td><br/><?php echo $_SESSION['panier']['libelleProduit'][$i]; ?></td>
            <td><br/><?php echo $_SESSION['panier']['prixProduit']['$i']; ?></td>
            <td><br/><input name="q[]" value="<?php echo $_SESSION['panier']['qteProduit']['$i']; ?>" size="5"/></td>
            <td><br/><?php echo $_SESSION['panier']['tva']."%"; ?></td>
            <td><br/><a href="panier.php?action=suppression&amp; l=<?php echo rawurlencode($_SESSION['panier']['libelleProduit'][$i]); ?>">X</a></td>
           </tr>
      <?php } ?>
         
           
           <tr>
 
             <td colspan="2"><br/>
              <p>Total: <?php echo montantGlobal(); ?></p>
              <p>Total avec tva: <?php echo montantGlobalTva(); ?></p>
             </td>
 
           </tr>
 
           <tr>
 
             <td colspan="4">
              <input type="submit" value="rafraichir" />
              <input type="hidden" name="action" value="refresh"  />
              <a href="?deletepanier=true">Supprimer le panier</a>
             </td>
           </tr>
           <?php
         
 
      }
 
    }
      ?>
 
     </table>
 
      </form>
      </div>
    </div>
 
  <?php
//Mes fonctions
 
function createPanier(){
  try
  {
 
  $bdd = new PDO('mysql:host=localhost;dbname=projet;charset=utf8', 'root', '');
  $bdd->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER); // Les noms de champ seront en minuscule
  $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Les erreurs lanceront des excetions
 
   }
 
   catch(Exception $e){
    die("Une erreur est survenue");
 
   }
 
  if(isset($_SESSION['panier'])){
 
    $_SESSION['panier'] = array();
    $_SESSION['panier']['libelleProduit'] = array();
    $_SESSION['panier']['qteProduit'] = array();
    $_SESSION['panier']['prixProduit'] = array();
    $_SESSION['panier']['verrou'] = false;
    $select = $bdd->query("SELECT TVA FROM produit");
    $data = $select->fetch(PDO::FETCH_OBJ);
    $_SESSION['panier']['tva'] = $data->tva;
 
  }
  return true;
 
}
 // la fonction d'ajout de produit au panier
function ajouterProduit($libelleProduit,$qteProduit,$prixProduit){
 
  if(createPanier() && !isVerrouille())
  {
 
    $positionProduit = array_search($libelleProduit, $_SESSION['panier']['libelleProduit']);
    if($positionProduit !== false)
    {
 
      $_SESSION['panier']['qteProduit'][$positionProduit] += $qteProduit;
    }
    else
    {
 
      array_push($_SESSION['panier']['libelleProduit'],$libelleProduit);
      array_push($_SESSION['panier']['qteProduit'],$qteProduit);
      array_push($_SESSION['panier']['prixProduit'],$prixProduit);
    }
  }
  else{
    // c'estle message qui s'affiche
 
    echo 'Erreur!! Veuillez contacter l\'administrateur ajouterProduit';
  }
}
 
function modifierQteProduit($libelleProduit, $qteProduit){
   
  if(createPanier() && !isVerrouille()){
 
    if($qteProduit > 0){
 
      $positionProduit =array_search($libelleProduit, $_SESSION['panier']['libelleProduit']);
       
      if($positionProduit !== false){
 
        $_SESSION['panier']['libelleProduit'][$positionProduit] = $qteProduit;
 
      }
 
    }
    else{
 
      supprimerProduit($libelleProduit);
 
    }
 
  }else{
 
    echo 'Erreur!! Veuillez contacter l\'administrateur modifier produit';
  }
 
}
 
 
function supprimerProduit($libelleProduit){
 
  if(createPanier() && !isVerrouille()){
 
    $tmp  = array();
    $tmp['libelleProduit'] = array();
    $tmp['qteProduit'] = array();
    $tmp['prixProduit'] = array();
    $tmp['verrou'] = $_SESSION['panier']['verrou'];
 
    for($i= 0; $i<count($_SESSION['panier']['libelleProduit']); $i++){
 
      if($_SESSION['panier']['libelleProduit'][$i] !== $libelleProduit){
 
      array_push( $tmp['libelleProduit'],$_SESSION['panier']['libelleProduit'][$i]  );
      array_push( $tmp['qteProduit'], $_SESSION['panier']['qteProduit'][$i] );
      array_push($tmp['prixProduit'], $_SESSION['panier']['prixProduit'][$i] );
      }
    }
 
    $_SESSION['panier'] = $tmp;
    unset($tmp);
 
 
  }else{
 
    echo 'Erreur!! Veuillez contacter l\'administrateur sup';
  }
}
 
function montantGlobal(){
 
  $total = 0;
 
  for($i = 0; $i<count($_SESSION['panier']['libelleProduit']); $i++){
 
    $total = $_SESSION['panier']['qteProduit'][$i]*$_SESSION['panier']['prixProduit'][$i];
 
  }
 
  return $total;
 
}
 
function montantGlobalTva(){
 
  $total = 0;
 
  for($i; $i<count($_SESSION['panier']['libelleProduit']); $i++){
 
    $total += $_SESSION['panier']['qteProduit'][$i] * $_SESSION['panier']['prixProduit'][$i];
 
  }
 
  return $total + ($total*$_SESSION['panier']['tva']/100);
 
}
 
function supprimerPanier(){
 
   
 
    unset($_SESSION['panier']);
   
}
 
function isVerrouille(){
 
  if(isset($_SESSION['panier']) && isset($_SESSION['panier']['verrou'])){
 
    return true;
  }else{
 
    return false;
  }
}
 
function compterProduit(){
 
  if(isset($_SESSION['panier'])){
 
    return count($_SESSION['panier']['libelleProduit']);
  }else{
 
    return 0;
  }
}
 
?>
    <div id="pied">
         <a href="https://www.facebook.com/adama.soumare.16"><img  src ="../fb.jpe"  width="50" height="45" title="Suivez-nous sur facebook" id="facebook" /></a><a href="https://www.instagram.com/black_soumer/"><img  src ="../ig.jpe"  width="50" height="45" title="Suivez-nous sur intagram" id="insta" /></a>
 
    </div>   
 </body>
</html>