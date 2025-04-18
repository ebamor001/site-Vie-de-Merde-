<?php  
  session_start(); 
  $pseudo_saisi = $_SESSION['pseudo'] ?? "";
  $error = "";  

  include('config.php');
  $db = new PDO("sqlite:" . $config["db_file"]);
  if (!$db) {
      die("Erreur de connexion à la base SQLite.");
  }
  //verifier si le formulaire est soumis en POST et se protéger des injections SQL
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pseudo = isset($_POST['pseudo']) ? trim($_POST['pseudo']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    if(!empty($pseudo) && !empty($content)){
      $date_creation = date('Y-m-d H:i:s');
      // Limite de 50 caractères pour le pseudo
      if (strlen($pseudo) > 50) {
        http_response_code(413);
        echo "Erreur 413 :Payload Too Large";
        exit;
      }
      $_SESSION['pseudo'] = $pseudo;
      $sql = "INSERT INTO vde (pseudo, contenu, date_creation) VALUES (:pseudo, :contenu, :date)";
      $stmt = $db->prepare($sql);
      $stmt->bindParam(":pseudo",  $pseudo, PDO::PARAM_STR);  
      $stmt->bindParam(":contenu", $content, PDO::PARAM_STR); 
      $stmt->bindParam(":date",    $date_creation, PDO::PARAM_STR);
      $ret = $stmt->execute();
      if (!$ret) {
          $error = "Erreur lors de la récupération de l’article";
      } else {
          header("Location: index.php");
          exit;
      }
    } else {
      $error = "Tous les champs sont obligatoires.";
    }
  }
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
      <title>Vie d'Enseirb - Accueil</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
  </head>
  <body>
    <header class="bg-dark text-white py-3">
      <div class="container">
          <h1 class="text-center">Vie d'Enseirb</h1>
          <nav>
              <ul class="d-flex justify-content-center list-unstyled mb-0">
                  <li class="mx-3"><a href="index.php" class="text-white text-decoration-none">Accueil</a></li>
                  <li class="mx-3"><a href="add_vde.php" class="text-white text-decoration-none">Ajouter une VDE</a></li>
              </ul>
          </nav>
      </div>
    </header>

    <?php if  (!empty($error)){
      echo'<p style="color: red;">'. htmlentities($error) .'</p>' ;
    }?>
    <section class="mt-5">
      <div class="d-flex flex-column align-items-center">
        <h2 class="text-dark mb-4">Publier une Vie d'Enseirb</h2>
        <form method="POST" action="add_vde.php" class="w-50 bg-light p-4 rounded shadow-sm">

          <div class="mb-3">
            <label for="pseudo" class="form-label text-dark">Pseudo :</label>
            <input type="text" name="pseudo" id="pseudo" value="<?php echo htmlentities($pseudo_saisi); ?>" maxlength="50" required class="form-control border-dark">
          </div>

          <div class="mb-3">
            <label for="content" class="form-label text-dark">Votre VdE :</label>
            <textarea id="content" name="content" placeholder="Publier votre VDE" required class="form-control border-dark" rows="4"></textarea>
          </div>

          <button type="submit" class="btn btn-dark w-100">Publier le vde</button>

        </form>
      </div>
    </section>
    <br>
    <br>
    <br>
    <?php include("footer.php") ; ?>
  </body>
</html>


