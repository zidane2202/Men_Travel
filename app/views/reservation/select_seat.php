<?php 
$pageTitle = 'Choisir un siège'; 
// Les variables $voyage (détails du voyage) et 
// $siegesReserves (tableau [5, 12]) sont passées par le contrôleur
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Men Travel</title>
    <style>
        /* (Style de base pour la page) */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
            background-color: #f4f7f6; 
            color: #333; 
            margin: 0; 
            padding: 20px; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 95vh; 
        }
        .container { 
            background-color: #ffffff; 
            padding: 2rem; 
            border-radius: 12px; 
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.07); 
            width: 100%; 
            max-width: 600px; /* Assez large pour 4 sièges */
            box-sizing: border-box; 
            animation: fadeIn 0.6s ease-out; 
        }
        h2 { 
            text-align: center; 
            color: #0056b3; 
            margin-top: 0; 
            margin-bottom: 0.5rem; 
            font-weight: 700; 
        }
        .subtitle {
            text-align: center;
            margin-top: -0.5rem;
            margin-bottom: 1.5rem;
            font-size: 1rem;
            color: #555;
        }
        
        /* Message d'erreur */
        .message { padding: 0.9rem; border-radius: 8px; margin-bottom: 1rem; text-align: center; font-size: 0.9rem; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* --- Style du Bus et des Sièges --- */
        .bus-layout {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .bus-layout p.bus-label {
            margin: 0 0 1rem;
            font-weight: 600;
            color: #868e96;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }
        
        .seat-map {
            display: grid;
            /* 4 colonnes, avec une allée au milieu */
            grid-template-columns: 1fr 1fr 0.5fr 1fr 1fr; /* 2 sièges, allée, 2 sièges */
            gap: 10px 15px; /* Espace rangées / colonnes */
            width: 100%;
            max-width: 350px; /* Largeur max du bus */
        }
        
        /* Style d'un siège individuel */
        .seat {
            width: 100%;
            aspect-ratio: 1 / 1; /* S'assure que le siège est carré */
            border: 2px solid #007bff;
            border-radius: 5px;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0.8rem;
            font-weight: 600;
            color: #007bff;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .seat.placeholder { /* Espace pour l'allée */
            border: none;
            background: none;
            cursor: default;
        }
        .seat:hover:not(.occupied):not(.selected) {
            background: #e6f2ff;
            transform: scale(1.05);
        }
        /* Siège occupé */
        .seat.occupied {
            background: #adb5bd;
            border-color: #adb5bd;
            color: #fff;
            cursor: not-allowed;
            opacity: 0.7;
        }
        /* Siège sélectionné */
        .seat.selected {
            background: #007bff;
            border-color: #007bff;
            color: #fff;
            transform: scale(1.1);
            box-shadow: 0 0 12px rgba(0,123,255,0.6);
        }

        /* Légende */
        .legend { display: flex; justify-content: center; flex-wrap: wrap; gap: 1rem; margin-top: 1.5rem; }
        .legend-item { display: flex; align-items: center; gap: 0.5rem; }
        .legend-item .seat { width: 25px; height: 25px; font-size: 0.7rem; cursor: default; }

        /* Formulaire de confirmation */
        #confirmation-form { margin-top: 1.5rem; text-align: center; }
        .summary { font-size: 1.1rem; font-weight: 600; margin-bottom: 1.5rem; }
        .summary span { color: #007bff; font-size: 1.2rem; }
        
        .form-button { padding: 0.9rem 1.5rem; border: none; border-radius: 8px; background-color: #007bff; color: white; font-size: 1rem; font-weight: 700; cursor: pointer; transition: background-color 0.3s; }
        .form-button:disabled { background-color: #aaa; cursor: not-allowed; }
        .form-button:hover:not(:disabled) { background-color: #0056b3; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Choisir votre siège</h2>
        <p class="subtitle">
            <strong><?= htmlspecialchars($voyage['ville_depart']) ?> → <?= htmlspecialchars($voyage['ville_arrivee']) ?></strong>
        </p>
        
        <?php if (isset($_GET['error'])): ?>
            <p class="message error"><?= htmlspecialchars($_GET['error']) ?></p>
        <?php endif; ?>

        <div class="bus-layout">
            <p class="bus-label">AVANT DU BUS (Volant)</p>
            <div class="seat-map">
                <?php 
                $nombreSieges = $voyage['nombre_places'];
                for ($i = 1; $i <= $nombreSieges; $i++) {
                    
                    $isOccupied = in_array($i, $siegesReserves);
                    $class = $isOccupied ? 'occupied' : '';
                    
                    echo "<div class='seat $class' data-seat-number='$i'>$i</div>";

                    // Ajoute l'allée (un 'placeholder' vide)
                    if ($i % 4 == 2) { 
                        echo "<div class='seat placeholder'></div>";
                    }
                }
                ?>
            </div>
            <p class="bus-label" style="margin-top: 1rem;">ARRIÈRE DU BUS</p>
        </div>

        <div class="legend">
            <div class="legend-item"><div class="seat"></div> Libre</div>
            <div class="legend-item"><div class="seat selected"></div> Sélectionné</div>
            <div class="legend-item"><div class="seat occupied"></div> Occupé</div>
        </div>

        <form action="/reservation/create" method="POST" id="confirmation-form">
            <input type="hidden" name="id_voyage" value="<?= $voyage['id_voyage'] ?>">
            <input type="hidden" name="numero_siege" id="selected-seat-input" value="">
            
            <div class="summary">
                Siège sélectionné : <span id="selected-seat-display">Aucun</span>
            </div>
            
            <button type="submit" class="form-button" id="submit-button" disabled>
                Confirmer (<?= number_format($voyage['prix'], 0, ',', ' ') ?> XAF)
            </button>
            <a href="/search" style="display: block; margin-top: 1rem; text-align:center;">Annuler</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const seats = document.querySelectorAll('.seat:not(.occupied)');
            const selectedSeatInput = document.getElementById('selected-seat-input');
            const selectedSeatDisplay = document.getElementById('selected-seat-display');
            const submitButton = document.getElementById('submit-button');
            let currentSelected = null;

            seats.forEach(seat => {
                seat.addEventListener('click', () => {
                    // Ne rien faire si c'est un placeholder
                    if (seat.classList.contains('placeholder')) return;

                    const seatNumber = seat.dataset.seatNumber;

                    // Dé-sélectionner si on clique sur le même
                    if (seat.classList.contains('selected')) {
                        seat.classList.remove('selected');
                        currentSelected = null;
                    } else {
                        // Enlever la sélection précédente
                        if (currentSelected) {
                            currentSelected.classList.remove('selected');
                        }
                        // Sélectionner le nouveau
                        seat.classList.add('selected');
                        currentSelected = seat;
                    }

                    // Mettre à jour le formulaire
                    if (currentSelected) {
                        selectedSeatInput.value = seatNumber;
                        selectedSeatDisplay.textContent = `N° ${seatNumber}`;
                        submitButton.disabled = false;
                    } else {
                        selectedSeatInput.value = '';
                        selectedSeatDisplay.textContent = 'Aucun';
                        submitButton.disabled = true;
                    }
                });
            });
        });
    </script>
</body>
</html>