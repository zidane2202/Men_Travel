<?php
// Ce fichier est un template. La variable $ticketData est injectée.
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        /* Styles en ligne pour une compatibilité maximale */
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .container { width: 90%; max-width: 600px; margin: 20px auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden; }
        .header { background: #003366; color: #fff; padding: 20px; }
        .body { padding: 30px; }
        .body h1 { color: #003366; } 
        .details { margin-top: 20px; border-collapse: collapse; width: 100%; }
        .details th, .details td { border: 1px solid #eee; padding: 10px; text-align: left; }
        .details th { background: #f8f9fa; width: 30%; }
        .footer { background: #f8f9fa; color: #777; padding: 20px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Men Travel - Votre Commande</h2>
        </div>
        <div class="body">
            
            <?= $ticketData['message_personnalise'] ?> 
            
            <table class="details">
                <tr>
                    <th>Passager</th>
                    <td><?= htmlspecialchars($ticketData['client_prenom'] . ' ' . $ticketData['client_nom']) ?></td>
                </tr>
                <tr>
                    <th>Voyage</th>
                    <td><?= htmlspecialchars($ticketData['ville_depart']) ?> → <?= htmlspecialchars($ticketData['ville_arrivee']) ?></td>
                </tr>
                <tr>
                    <th>Date & Heure</th>
                    <td><?= htmlspecialchars(date('d M Y \à H:i', strtotime($ticketData['date_depart']))) ?></td>
                </tr>
                
                <tr>
                    <th>Siège(s)</th>
                    <td>
                        <strong>
                            N° <?= htmlspecialchars(implode(', N°', $ticketData['sieges'])) ?>
                        </strong>
                    </td>
                </tr>
                
                <tr>
                    <th>Bus</th>
                    <td><?= htmlspecialchars($ticketData['vehicule_marque']) ?> (<?= htmlspecialchars($ticketData['immatriculation']) ?>)</td>
                </tr>
                <tr>
                    <th>Prix Total</th>
                    <td><?= htmlspecialchars(number_format($ticketData['montant_total'], 0, ',', ' ')) ?> XAF (Payé)</td>
                </tr>
                <tr>
                    <th>ID Commande</th>
                    <td>#<?= str_pad($ticketData['id_commande'], 6, '0', STR_PAD_LEFT) ?></td>
                </tr>
            </table>

            <p style="margin-top: 20px;">Merci de vous présenter à l'agence 30 minutes avant le départ.</p>
        </div>
        <div class="footer">
            © <?= date('Y') ?> Men Travel - Tous droits réservés.
        </div>
    </div>
</body>
</html>