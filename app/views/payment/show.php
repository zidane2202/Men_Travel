<?php 
$pageTitle = 'Finaliser le paiement'; 
// $reservationDetails est passé par le contrôleur
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Men Travel</title>
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 95vh; }
        .payment-container { background-color: #ffffff; padding: 2.5rem; border-radius: 12px; box-shadow: 0 12px 30px rgba(0, 0, 0, 0.07); width: 100%; max-width: 480px; box-sizing: border-box; animation: fadeIn 0.6s ease-out; text-align: center; }
        h2 { color: #0056b3; margin-top: 0; margin-bottom: 1.5rem; font-weight: 700; }
        
        .summary { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; }
        .summary p { margin: 0; font-size: 1.1rem; color: #333; }
        .summary .voyage { font-weight: 600; font-size: 1.2rem; }
        .summary .price { font-size: 2.5rem; font-weight: 700; color: #0056b3; margin-top: 1rem; }
        
        .payment-methods { display: flex; flex-direction: column; gap: 1rem; }
        .payment-button { width: 100%; padding: 1rem; border: none; border-radius: 8px; color: white; font-size: 1rem; font-weight: 700; cursor: pointer; transition: all 0.3s; display: flex; justify-content: center; align-items: center; gap: 0.5rem; text-decoration: none; }
        .btn-om { background-color: #f36f21; } /* Orange */
        .btn-momo { background-color: #ffcc00; color: #000; } /* MTN */
        .payment-button:hover { opacity: 0.8; transform: scale(1.02); }
        
        .message { padding: 0.9rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.9rem; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="payment-container">
        <h2>Confirmer le paiement</h2>
        
        <?php if (isset($_GET['error'])): ?>
            <p class="message error"><?= htmlspecialchars($_GET['error']) ?></p>
        <?php endif; ?>

        <div class="summary">
            <p>Voyage: <span class="voyage"><?= htmlspecialchars($reservationDetails['ville_depart']) ?> → <?= htmlspecialchars($reservationDetails['ville_arrivee']) ?></span></p>
            <p>Montant total à payer :</p>
            <div class="price"><?= number_format($reservationDetails['prix'], 0, ',', ' ') ?> XAF</div>
        </div>

        <p>Simuler le paiement avec :</p>
        
        <div class="payment-methods">
            <form action="/payment/process" method="POST">
                <input type="hidden" name="id_reservation" value="<?= $reservationDetails['id_reservation'] ?>">
                <input type="hidden" name="montant" value="<?= $reservationDetails['prix'] ?>">
                <input type="hidden" name="mode" value="ORANGE_MONEY">
                <button type="submit" class="payment-button btn-om">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Orange_Money_logo.svg/1280px-Orange_Money_logo.svg.png" height="20" alt="OM">
                    Payer avec Orange Money
                </button>
            </form>
            
            <form action="/payment/process" method="POST">
                <input type="hidden" name="id_reservation" value="<?= $reservationDetails['id_reservation'] ?>">
                <input type="hidden" name="montant" value="<?= $reservationDetails['prix'] ?>">
                <input type="hidden" name="mode" value="MTN_MOMO">
                <button type="submit" class="payment-button btn-momo">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/MoMo_-_Mobile_Money_-_Logo.svg/1280px-MoMo_-_Mobile_Money_-_Logo.svg.png" height="20" alt="MoMo">
                    Payer avec MTN MoMo
                </button>
            </form>
        </div>
        
        <a href="/my-reservations" style="display: block; margin-top: 1.5rem; color: #777;">Annuler et payer plus tard</a>
    </div>
</body>
</html>