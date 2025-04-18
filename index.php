<?php
    include('config.php');
    // differement de l'exemple de cours avec mysql,là c avec mysqlite qui nécéssite pas de nom d'hote, ni utilisateur ni mot de passe 
    $db = new PDO("sqlite:" . $config["db_file"]);
    if (!$db) {
        die("Erreur de connexion à la base SQLite.");
    }
    // On récupère le nombre d'histoires à afficher par page
    $paginateBy = $config["paginate_by"];

    // Page courante
    $page_courrante = $_GET['page'] ?? '1';
    if (!ctype_digit($page_courrante) || (int)$page_courrante < 1) {
        header("HTTP/1.1 404 Not Found");
        echo "Erreur 404 : page invalide.";
        exit;
    }

    $page = (int)$page_courrante;

    // Nombre total de VDE
    $stmt = $db->query("SELECT COUNT(*) FROM VDE");
    $totalVDE = $stmt->fetchColumn();
    $totalPages = max(ceil($totalVDE / $paginateBy), 1); // toujours au moins 1 page

    if ($page > $totalPages) {
        header("HTTP/1.1 404 Not Found");
        echo "Erreur 404 : page inexistante.";
        exit;
    }

    // Récupérer les VDE pour la page actuelle
    $offset = ($page - 1) * $paginateBy;
    $sql = "SELECT histoire_id, pseudo, date_creation, contenu 
            FROM VDE 
            ORDER BY date_creation DESC 
            LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $paginateBy, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $histoires = $stmt->fetchAll();

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

        <section class="container mt-5">
            <div class="text-center">
                <h2 class="text-dark mb-4 ">Liste des Vies d'Enseirb</h2>
                <a href="add_vde.php" class="btn btn-dark">Ajouter une VdE</a>
            </div>
            <br>
            <div class="row">
                <?php  foreach ($histoires as $histoire){ 
                    // Récupérer le nombre de commentaires pour cette histoire
                    $stmt = $db->prepare("SELECT COUNT(*) AS nb FROM commentaires WHERE histoire_id = ?");
                    $stmt->execute([ $histoire['histoire_id']]);
                    $res= $stmt->fetch();
                ?>
                <div class="col-lg-4 col-md-6 mb-4"> 
                    <div class="card">
                        <div class="card-body">
                            <h5>Pseudo: <?php echo  nl2br(htmlentities($histoire['pseudo'])); ?></h5>
                            <p><?php echo  nl2br(htmlentities($histoire['contenu'])); ?></p>
                            <p>Published the <?php echo htmlentities($histoire['date_creation']); ?></p>
                        </div>
                        <div class="card-footer text-center bg-light ">
                            <a href="show_vde.php?id=<?php echo $histoire['histoire_id']; ?>" class="text-dark">
                            Accéder à ce vde d'id <?php echo $histoire['histoire_id']; ?> et ses <?php echo $res['nb']; ?> commentaires
                            </a>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <!-- Pagination -->
            <nav class="mt-4 d-flex justify-content-center">
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link bg-dark text-white" href="index.php?page=<?php echo $page - 1; ?>">Précédent</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                            <a class="page-link bg-dark text-white" href="index.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link bg-dark text-white" href="index.php?page=<?php echo $page + 1; ?>">Suivant</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </section>
        <footer class="bg-dark text-white py-3 mt-auto">
            <div class="container text-center">
            <?php include("footer.php"); ?>
            </div>
        </footer>
    </body>
</html>
