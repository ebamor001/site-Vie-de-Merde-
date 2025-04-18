<?php
    session_start(); 
    include('config.php');
    $db = new PDO("sqlite:" . $config["db_file"]);
 
    // Vérifier si le formulaire est soumis en POST
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        // Mauvaise méthode => erreur 405 Method Not Allowed
        http_response_code(405);
        echo "Erreur 405 Method Not Allowed";
        exit;
    }
    $vde_id = $_POST['vde_id'] ?? null;
    $pseudo = trim($_POST['pseudo']?? "") ;
    $comment = trim($_POST['comment']?? "");
    $date_creation = date('Y-m-d H:i:s');

    if (empty($vde_id) || empty($pseudo) || empty($comment)) {
        http_response_code(400);
        echo "Erreur 400 : Bad Request";
        exit;
    }
    // Vérifier que la VdE existe
    $stmt = $db->prepare("SELECT 1 FROM vde WHERE histoire_id = ?");
    $stmt->execute([$vde_id]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo "Erreur 404 : VdE introuvable.";
        exit;
    }
    
    if (strlen($pseudo) > 50) {
        http_response_code(413);
        echo "Erreur 413 :Payload Too Large";
        exit;
    }
    //ajout du commentaire
    $stmt = $db->prepare("INSERT INTO commentaires (histoire_id, pseudo, contenu, date_creation) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$vde_id, $pseudo, $comment, $date_creation])) {
        $_SESSION['pseudo'] = $pseudo;
      

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo json_encode([
                "pseudo" => $pseudo,
                "comment" => $comment,
                "date_creation" => $date_creation
            ]);
            exit;
        } else {
            // Si pas AJAX : redirection vers la page de la VdE
            header("Location: show_vde.php?id=" . $vde_id);
            exit;
        }
    } else {
        http_response_code(500);
        echo "Erreur 500 : Erreur interne du serveur.";
        exit;
    }
?>
