<?php
    session_start(); 
    $error_comment = $_SESSION['error_comment'] ?? ""; 
    $pseudo_saisi = $_SESSION['pseudo'] ?? "";
    unset($_SESSION['error_comment']); 
    // Connexion à la base de données
    include('config.php');
    $db = new PDO("sqlite:" . $config["db_file"]);
    if (!$db) {
        die("Erreur de connexion à la base SQLite.");
    }
    
    $vde = null;  
    $comments = [];//Mieux vaut initier à un tableau vide pour éviter une potentielle erreur en foreach
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(404);
        echo "Erreur 404 : VdE introuvable.";
        exit;
    }

    $id = (int)$_GET['id']; 
    $stmt = $db->prepare("SELECT * FROM vde WHERE histoire_id = ?");
    if (!$stmt->execute([$id])) {
        http_response_code(404);
        echo "Erreur 404 : VdE introuvable.";
        exit;
    }
    $vde = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$vde) {
        http_response_code(404);
        echo "Erreur 404 : VdE introuvable.";
        exit;
    }
    // Récupérer les commentaires associés à cette VdE
    $comment_stmt = $db->prepare("SELECT * FROM commentaires WHERE histoire_id = ? ORDER BY date_creation ASC");
    $comment_stmt->execute([$id]);
    $comments = $comment_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
        <title>VdE</title>
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
        
        <section class="mt-5">
            <div class="d-flex flex-column align-items-center">
                <h2 class="text-dark mb-4">Rechercher une VDE</h2>
                <form method="GET" action="show_vde.php" class="w-50">
                    <div class="mb-3">
                        <label for="id" class="form-label">ID de la VDE :</label>
                        <input type="number" id="id" name="id" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Rechercher</button>
                </form>
            </div>
        </section>
        <section class="mt-5">
            <div class="container">
                <h3 class="text-center text-dark">VdE de <?php echo htmlentities($vde['pseudo']); ?></h3>
                <div class="card mt-4">
                    <div class="card-body">
                        <p><strong>Date de création :</strong> <?php echo htmlentities($vde['date_creation']); ?></p>
                        <p><strong>Contenu :</strong></p>
                        <p><?php echo nl2br(htmlentities($vde['contenu'])); ?></p>
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-5" >
            <div class="container" id ="commentsection">        
                <h3 class="mt-5 text-dark">Commentaires :</h3>
                <?php if ($comments): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="card mt-3">
                            <div class="card-body">
                                <p><strong><?php echo htmlentities($comment['pseudo']); ?></strong> a écrit le <?php echo htmlentities($comment['date_creation']); ?> :</p>
                                <p><?php echo nl2br(htmlentities($comment['contenu'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">Aucun commentaire pour cette VdE.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="mt-5">
            <div class="container">
                <h3 class="text-center text-dark">Ajouter un commentaire :</h3>
                <form method="POST" action="add_comment.php" class="w-50 mx-auto mt-4">
                    <input type="hidden" name="vde_id" value="<?php echo htmlentities($vde['histoire_id']); ?>">
                    <div class="mb-3">
                        <label for="pseudo" class="form-label">Pseudo :</label>
                        <input type="text" id="pseudo" name="pseudo" class="form-control" value="<?php echo htmlentities($pseudo_saisi); ?>" maxlength="50" required>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Commentaire :</label>
                        <textarea id="comment" name="comment" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Envoyer</button>
                </form>
            </div>
        </section>
        <?php if ($error_comment): ?>
                        <p style="color: red;"><?php echo $error_comment; ?></p>
        <?php endif; ?>
        <br>
        <br>
        <?php include('footer.php'); ?>
        
        <script src="script.js"></script>
    </body>
</html>




