<?php 
$pageTitle = 'Choisir vos sièges'; 
// Les variables $voyage et $siegesReserves sont passées par le contrôleur
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Men Travel</title>
    <style>
        /* (Le CSS est identique à avant) */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 95vh; }
        .container { background-color: #ffffff; padding: 2rem; border-radius: 12px; box-shadow: 0 12px 30px rgba(0, 0, 0, 0.07); width: 100%; max-width: 600px; box-sizing: border-box; animation: fadeIn 0.6s ease-out; }
        h2 { text-align: center; color: #0056b3; margin-top: 0; margin-bottom: 0.5rem; font-weight: 700; }
        .subtitle { text-align: center; margin-top: -0.5rem; margin-bottom: 1.5rem; font-size: 1rem; color: #555; }
        .message { padding: 0.9rem; border-radius: 8px; margin-bottom: 1rem; text-align: center; font-size: 0.9rem; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .bus-layout { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 10px; padding: 1.5rem; margin: 1.5rem 0; display: flex; flex-direction: column; align-items: center; }
        .bus-layout p.bus-label { margin: 0 0 1rem; font-weight: 600; color: #868e96; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; }
        .seat-map { display: grid; grid-template-columns: 1fr 1fr 0.5fr 1fr 1fr; gap: 10px 15px; width: 100%; max-width: 350px; align-items: center; }
        .seat { width: 100%; aspect-ratio: 1 / 1; border: 2px solid #007bff; border-radius: 5px; background: #fff; display: flex; justify-content: center; align-items: center; font-size: 0.8rem; font-weight: 600; color: #007bff; cursor: pointer; transition: all 0.2s ease; }
        .seat.placeholder { border: none; background: none; cursor: default; }
        .seat:hover:not(.occupied):not(.selected) { background: #e6f2ff; transform: scale(1.05); }
        .seat.occupied { background: #adb5bd; border-color: #adb5bd; color: #fff; cursor: not-allowed; opacity: 0.7; }
        .seat.selected { background: #007bff; border-color: #007bff; color: #fff; transform: scale(1.1); box-shadow: 0 0 12px rgba(0,123,255,0.6); }
        .legend { display: flex; justify-content: center; flex-wrap: wrap; gap: 1rem; margin-top: 1.5rem; }
        .legend-item { display: flex; align-items: center; gap: 0.5rem; }
        .legend-item .seat { width: 25px; height: 25px; font-size: 0.7rem; cursor: default; }
        #confirmation-form { margin-top: 1.5rem; text-align: center; }
        .summary { font-size: 1.1rem; font-weight: 600; margin-bottom: 1.5rem; }
        .summary span { color: #007bff; font-size: 1.2rem; }
        .form-button { padding: 0.9rem 1.5rem; border: none; border-radius: 8px; background-color: #007bff; color: white; font-size: 1rem; font-weight: 700; cursor: pointer; transition: background-color 0.3s; }
        .form-button:disabled { background-color: #aaa; cursor: not-allowed; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Choisir vos sièges</h2>
        <p class="subtitle">
            <strong><?= htmlspecialchars($voyage['ville_depart']) ?> → <?= htmlspecialchars($voyage['ville_arrivee']) ?></strong>
            (Sélectionnez un ou plusieurs sièges)
        </p>
        
        <?php if (isset($_GET['error'])): ?>
            <p class="message error"><?= htmlspecialchars($_GET['error']) ?></p>
        <?php endif; ?>

        <div class="bus-layout">
            <p class="bus-label">AVANT DU BUS (Volant)</p>
            <div class="seat-map align-center ">
                <?php 
                $nombreSieges = $voyage['nombre_places'];
                for ($i = 1; $i <= $nombreSieges; $i++) {
                    $isOccupied = in_array($i, $siegesReserves);
                    $class = $isOccupied ? 'occupied' : '';
                    echo "<div class='seat $class' data-seat-number='$i'>$i</div>";
                    if ($i % 4 == 2) { 
                        echo "<div class='seat placeholder'></div>";
                    }
                }
                ?>
            </div>
            <p class="bus-label" style="margin-top: 3rem; text-align: center;">ARRIÈRE DU BUS</p>
        </div>

        <div class="legend">
            <div class="legend-item"><div class="seat"></div> Libre</div>
            <div class="legend-item"><div class="seat selected"></div> Sélectionné</div>
            <div class="legend-item"><div class="seat occupied"></div> Occupé</div>
        </div>

        <form action="/reservation/create" method="POST" id="confirmation-form">
            <input type="hidden" name="id_voyage" value="<?= $voyage['id_voyage'] ?>">
            
            <div id="selected-seats-container">
                </div>
            
            <div class="summary">
                Sièges sélectionnés : <span id="selected-seat-display">Aucun</span>
            </div>
            <div class="summary" style="margin-top: -10px;">
                Total : <span id="total-price-display">0 XAF</span>
            </div>
            
            <button type="submit" class="form-button" id="submit-button" disabled>
                Confirmer la réservation
            </button>
            <a href="/search" style="display: block; margin-top: 1rem; text-align:center;">Annuler</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const seats = document.querySelectorAll('.seat:not(.occupied)');
            const seatContainer = document.getElementById('selected-seats-container');
            const seatDisplay = document.getElementById('selected-seat-display');
            const priceDisplay = document.getElementById('total-price-display');
            const submitButton = document.getElementById('submit-button');
            
            const ticketPrice = <?= (float)$voyage['prix'] ?>;
            let selectedSeats = []; // Un tableau pour stocker les numéros

            seats.forEach(seat => {
                seat.addEventListener('click', () => {
                    if (seat.classList.contains('placeholder')) return;

                    const seatNumber = seat.dataset.seatNumber;
                    
                    // 1. Basculer (toggle) la classe et le tableau
                    if (seat.classList.contains('selected')) {
                        // Dé-sélectionner
                        seat.classList.remove('selected');
                        selectedSeats = selectedSeats.filter(s => s !== seatNumber); // Enlève du tableau
                    } else {
                        // Sélectionner
                        seat.classList.add('selected');
                        selectedSeats.push(seatNumber); // Ajoute au tableau
                    }

                    // 2. Mettre à jour le formulaire
                    updateForm();
                });
            });

            function updateForm() {
                // Vider le conteneur
                seatContainer.innerHTML = '';
                
                // Remplir le conteneur avec les nouveaux inputs cachés
                selectedSeats.forEach(seatNum => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'numero_siege[]'; // <-- Nom du tableau PHP
                    input.value = seatNum;
                    seatContainer.appendChild(input);
                });

                // Mettre à jour l'affichage
                if (selectedSeats.length === 0) {
                    seatDisplay.textContent = 'Aucun';
                    priceDisplay.textContent = '0 XAF';
                    submitButton.disabled = true;
                } else {
                    seatDisplay.textContent = `N° ${selectedSeats.join(', ')}`; // Ex: N° 5, 6, 10
                    const totalPrice = ticketPrice * selectedSeats.length;
                    priceDisplay.textContent = `${totalPrice.toLocaleString('fr-FR')} XAF`;
                    submitButton.disabled = false;
                }
            }
        });
    </script>
</body>
</html>