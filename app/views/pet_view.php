<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Details - <?= htmlspecialchars($pet['name']) ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #fffaf5 0%, #fff0e6 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .pet-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(255, 145, 77, 0.15);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease;
        }
        
        .pet-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #ff914d, #ffb47b);
        }
        
        .pet-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .pet-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px;
            display: block;
            border: 5px solid #ff914d;
            box-shadow: 0 8px 20px rgba(255, 145, 77, 0.3);
        }
        
        .pet-name {
            font-size: 36px;
            font-weight: 700;
            background: linear-gradient(135deg, #ff914d, #ffb47b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        
        .pet-species {
            color: #666;
            font-size: 18px;
            font-weight: 500;
        }
        
        .pet-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .detail-item {
            background: #fff7f0;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #ff914d;
            transition: all 0.3s ease;
        }
        
        .detail-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 145, 77, 0.2);
        }
        
        .detail-label {
            font-size: 13px;
            font-weight: 600;
            color: #ff914d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .detail-value {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        
        .medical-history {
            background: #fff7f0;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            border-left: 4px solid #ff914d;
        }
        
        .medical-history h3 {
            color: #ff914d;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .medical-history p {
            color: #555;
            line-height: 1.8;
            white-space: pre-wrap;
        }
        
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #ff914d, #ffb47b);
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 145, 77, 0.3);
        }
        
        .btn-back::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-back:hover::before {
            left: 100%;
        }
        
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 145, 77, 0.4);
        }
        
        .vaccinated-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .vaccinated-badge.yes {
            background: #d4edda;
            color: #155724;
        }
        
        .vaccinated-badge.no {
            background: #f8d7da;
            color: #721c24;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .pet-card {
                padding: 25px;
            }
            
            .pet-name {
                font-size: 28px;
            }
            
            .pet-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="pet-card">
            <div class="pet-header">
                <?php if (!empty($pet['photo'])): ?>
                    <img src="/<?= PUBLIC_DIR ?>/<?= ltrim($pet['photo'], '/') ?>" alt="<?= htmlspecialchars($pet['name']) ?>" class="pet-photo" onerror="this.onerror=null; this.src=this.src.replace('/<?= PUBLIC_DIR ?>/','/')">
                <?php else: ?>
                    <img src="/<?= PUBLIC_DIR ?>/assets/default-pet.svg" alt="Default Pet" class="pet-photo">
                <?php endif; ?>
                <h1 class="pet-name"><?= htmlspecialchars($pet['name']) ?></h1>
                <p class="pet-species"><?= htmlspecialchars($pet['species'] ?? 'Unknown') ?> • <?= htmlspecialchars($pet['breed']) ?></p>
            </div>
            
            <div class="pet-details">
                <div class="detail-item">
                    <div class="detail-label">Birthdate</div>
                    <div class="detail-value"><?= htmlspecialchars($pet['birthdate']) ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Age</div>
                    <div class="detail-value"><?= htmlspecialchars($pet['age']) ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Vaccination Status</div>
                    <div class="detail-value">
                        <span class="vaccinated-badge <?= $pet['vaccinated'] ? 'yes' : 'no' ?>">
                            <?= $pet['vaccinated'] ? '✓ Vaccinated' : '✗ Not Vaccinated' ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($pet['medical_history'])): ?>
            <div class="medical-history">
                <h3>📋 Medical History</h3>
                <p><?= nl2br(htmlspecialchars($pet['medical_history'])) ?></p>
            </div>
            <?php endif; ?>
            
        </div>
    </div>
</body>
</html>
