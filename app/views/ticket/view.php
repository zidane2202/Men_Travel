<?php 
$pageTitle = 'Votre Ticket de Voyage'; 
// $ticketData (un gros tableau) est passé par le contrôleur
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Men Travel</title>
    <style>
        /* Style pour l'écran */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .ticket-wrapper {
            width: 100%;
            max-width: 700px; /* Largeur d'un ticket standard */
            margin: 20px auto;
        }

        .ticket-container {
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
            overflow: hidden;
            font-size: 14px; /* Taille de police de base pour le ticket */
        }
        
        /* En-tête du ticket */
        .ticket-header {
            background-color: #003366; /* Bleu nuit */
            color: #fff;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ticket-header h1 {
            margin: 0;
            font-size: 1.8rem;
        }
        .ticket-header .status {
            background: #28a745; /* Vert succès */
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        /* Corps du ticket */
        .ticket-body {
            padding: 2rem;
        }
        
        .trip-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px dashed #eee;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .trip-info .location {
            font-size: 2rem;
            font-weight: 700;
            color: #003366;
        }
        .trip-info .arrow {
            font-size: 2rem;
            color: #007bff;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr; /* 2 colonnes */
            gap: 1.5rem;
        }
        .detail-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
        }
        .detail-item strong {
            display: block;
            font-size: 0.8rem;
            color: #777;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }
        .detail-item span {
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        /* Pied de page du ticket */
        .ticket-footer {
            background: #f8f9fa;
            padding: 1.5rem 2rem;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 0.9rem;
            color: #777;
        }
        .ticket-footer strong {
            display: block;
            color: #000;
            font-size: 1rem;
            margin-top: 0.5rem;
        }

        /* Boutons d'action (pour l'écran) */
        .action-buttons {
            text-align: center;
            margin-top: 1.5rem;
        }
        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-print {
            background-color: #007bff;
            color: white;
            margin-right: 0.5rem;
        }
        .btn-print:hover { background-color: #0056b3; }
        .btn-back {
            background-color: #6c757d;
            color: white;
        }
        .btn-back:hover { background-color: #5a6268; }

        /* Style pour l'impression (cache les boutons) */
        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }
            .ticket-wrapper {
                max-width: 100%;
                margin: 0;
                box-shadow: none;
                border: none;
            }
            .action-buttons, .btn-back {
                display: none; /* Cache les boutons à l'impression */
            }
        }
    </style>
</head>
<body>

    <div class="ticket-wrapper">
        <div class="action-buttons">
            <button class="btn btn-print" onclick="window.print()">Imprimer le ticket</button>
            <a href="/my-reservations" class="btn btn-back">Retour</a>
        </div>

        <div class="ticket-container" id="ticket">
            <header class="ticket-header">
                <h1>Men Travel</h1>
                <span class="status">TICKET PAYÉ</span>
            </header>
            
            <main class="ticket-body">
                <div class="trip-info">
                    <span class="location"><?= htmlspecialchars($ticketData['ville_depart']) ?></span>
                    <span class="arrow">→</span>
                    <span class="location"><?= htmlspecialchars($ticketData['ville_arrivee']) ?></span>
                </div>
                
                <div class="details-grid">
                    <div class="detail-item">
                        <strong>Passager</strong>
                        <span><?= htmlspecialchars($ticketData['client_prenom'] . ' ' . $ticketData['client_nom']) ?></span>
                    </div>
                    <div class="detail-item">
                        <strong>Téléphone</strong>
                        <span><?= htmlspecialchars($ticketData['client_telephone']) ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <strong>Date & Heure</strong>
                        <span><?= htmlspecialchars(date('d M Y \à H:i', strtotime($ticketData['date_depart']))) ?></span>
                    </div>
                    <div class="detail-item">
                        <strong>Siège N°</strong>
                        <span style="color: #007bff; font-weight: 700; font-size: 1.3rem;"><?= htmlspecialchars($ticketData['numero_siege']) ?></span>
                    </div>

                    <div class="detail-item">
                        <strong>Bus / Immatriculation</strong>
                        <span><?= htmlspecialchars($ticketData['vehicule_marque']) ?> / <?= htmlspecialchars($ticketData['immatriculation']) ?></span>
                    </div>
                    <div class="detail-item">
                        <strong>Chauffeur</strong>
                        <span><?= htmlspecialchars($ticketData['chauffeur_nom']) ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <strong>Réservé le</strong>
                        <span><?= htmlspecialchars(date('d M Y', strtotime($ticketData['date_reservation']))) ?></span>
                    </div>
                    <div class="detail-item">
                        <strong>Prix du Ticket</strong>
                        <span><?= htmlspecialchars(number_format($ticketData['prix'], 0, ',', ' ')) ?> XAF</span>
                    </div>
                </div>
            </main>
            
            <footer class="ticket-footer">
                <p>Merci de voyager avec Men Travel. Veuillez vous présenter à l'agence 30 minutes avant le départ.</p>
                <strong>ID Ticket: #<?= str_pad($ticketData['id_reservation'], 6, '0', STR_PAD_LEFT) ?></strong>
            </footer>
        </div>
    </div>

</body>
</html>