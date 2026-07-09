# 03: KODE PROGRAM IMPLEMENTASI - Hybrid Model (OPSI 3)

## BAGIAN 0: Konsep Code Implementation

```
Dokumentasi ini menjelaskan bagaimana OPSI 3 (Hybrid Model) diimplementasikan 
di backend untuk menggabungkan AHP Expert + User Adjustment.

Silakan lihat: 02_PERHITUNGAN_MANUAL_HYBRID.md 
untuk detail perhitungan manual yang mudah dipahami.

Dokumen ini fokus pada: Struktur code, algoritma, flow backend.
```

---

## BAGIAN 1: ARCHITECTURE - OPSI 3 HYBRID MODEL

### Flow Sistem Dinamis OPSI 3

```
╔═══════════════════════════════════════════════════════════════╗
║           FLOW SISTEM DINAMIS OPSI 3: HYBRID                ║
║    (AHP Pakar Baseline + User Adjustment = Personalized)    ║
╚═══════════════════════════════════════════════════════════════╝

BACKEND DATABASE/FILE (TETAP):
├─ decision_matrix = 5×6 (performa dari pakar research)
├─ criteria_type = [benefit, cost, benefit, cost, benefit, benefit]
├─ alternatives = ['Saham', 'Reksa Dana', 'SBN', 'Kripto', 'Emas']
└─ ahp_expert_weights = [0.3067, 0.2377, 0.1377, 0.1055, 0.1308, 0.0817]
                         └─ INI BASELINE! (hasil AHP expert)

FRONTEND/USER INPUT (DINAMIS):
├─ User lihat: "Expert recommends these importance levels:" + AHP weights
├─ User dapat adjust tiap kriteria via slider:
│  └─ Slider range: -20% to +20% dari expert baseline
├─ User submit: adjustment values [+10%, -5%, 0%, +8%, -3%, +1%]
└─ Sum validation: berakhir dengan normalized weights

BACKEND PROCESSING (FUSION):
1. Load AHP Expert Weights
   w_expert = [0.3067, 0.2377, 0.1377, 0.1055, 0.1308, 0.0817]

2. Receive User Adjustment
   adjustment = [+0.10, -0.05, 0.0, +0.08, -0.03, +0.01]

3. Calculate Adjusted Weights ✓ FUSION POINT
   w_adjusted = w_expert + adjustment
              = [0.4067, 0.1877, 0.1377, 0.1855, 0.0808, 0.0817]
   
4. Normalize (ensure sum = 1.0)
   w_final = w_adjusted / sum(w_adjusted)
           = [0.41, 0.19, 0.14, 0.19, 0.08, 0.08]

5. TOPSIS Calculation dengan w_final
   V_ij = w_final_j × r_ij
          └─ INI YANG BERBEDA PER USER!

6. Return Personalized Ranking

OUTPUT:
└─ User A ranking (berdasarkan w_A yang personalized)
   + Rating for each alternative + Explanation
```

---

## BAGIAN 2: PYTHON BACKEND - TOPSIS HYBRID CLASS

### Python Implementation

```python
# File: topsis_hybrid_backend.py
# KONSEP: AHP Expert Baseline + User Adjustment = Personalized Ranking

import numpy as np
from typing import Dict, List, Tuple

class TOPSISHybrid:
    """
    Hybrid TOPSIS dengan AHP Expert Baseline + User Adjustment
    
    Fitur:
    - Load expert AHP weights sebagai baseline
    - Accept user adjustment (increment/decrement dari baseline)
    - Fuse expert weights dengan user adjustment
    - Calculate personalized TOPSIS ranking
    """
    
    def __init__(self):
        # ═══ PERMANENT DATA (Stored in DB or File) ═══
        # Data tidak berubah untuk setiap request
        
        self.decision_matrix = np.array([
            [12,      50,     9,        100000,   2.5,    7],      # Saham
            [10,      35,     7,        50000,    2,      8],      # Reksa Dana
            [6,       5,      6,        1000000,  6.5,    5],      # SBN Ritel
            [45,      85,     10,       10000,    0,      6],      # Kripto
            [4,       15,     8,        100000,   0,      8]       # Emas Digital
        ], dtype=float)
        
        self.criteria_type = ['benefit', 'cost', 'benefit', 'cost', 'benefit', 'benefit']
        self.alternatives = ['Saham', 'Reksa Dana', 'SBN Ritel', 'Kripto', 'Emas Digital']
        self.criteria_names = ['Return', 'Risk', 'Liquidity', 'Capital', 'Income', 'Access']
        
        # ═══ EXPERT AHP WEIGHTS (BASELINE) ═══
        # Dari hasil AHP Saaty expert analysis - ini jadi starting point untuk semua user!
        self.ahp_expert_weights = np.array([0.3067, 0.2377, 0.1377, 0.1055, 0.1308, 0.0817])
        
        # ═══ NORMALIZATION CACHE ═══
        # Bisa di-cache karena sama untuk semua user
        self.normalized_matrix = None
    
    def calculate_ranking_hybrid(self, user_adjustment: List[float]) -> Dict:
        """
        HYBRID MODEL FUSION:
        
        INPUT: 
            user_adjustment = [+0.10, -0.05, 0.0, +0.08, -0.03, +0.01]
                             └─ increment/decrement dari expert baseline
        
        PROCESSING: 
            Combine AHP_Expert + User_Adjustment
        
        OUTPUT: 
            dictionary dengan personalized ranking, weights, explanation
        
        user_adjustment format: List of 6 floats (±0.20 max per kriteria)
        """
        
        # ═══ STEP 1: VALIDATE INPUT ═══
        if len(user_adjustment) != 6:
            raise ValueError("Adjustment harus 6 nilai (satu per kriteria)")
        
        user_adjustment = np.array(user_adjustment)
        
        # ═══ STEP 2: FUSE EXPERT + USER ═══
        # Calculate adjusted weights
        w_adjusted = self.ahp_expert_weights + user_adjustment
        
        # Ensure all weights are positive (validation rule)
        if np.any(w_adjusted < 0):
            raise ValueError("Adjustment too extreme - would result in negative weights")
        
        # Normalize to sum = 1.0
        w_final = w_adjusted / np.sum(w_adjusted)
        
        # ═══ STEP 3: STANDARD TOPSIS ═══
        # (dengan w_final yang sudah personalized)
        
        # Step 3a: Normalisasi decision matrix
        if self.normalized_matrix is None:
            self.normalized_matrix = self._normalize(self.decision_matrix)
        
        normalized = self.normalized_matrix.copy()
        
        # Step 3b: Weighted matrix ✓ KEY FUSION POINT
        # V_ij = w_j (personalized) × r_ij
        weighted = normalized * w_final
        
        # Step 3c: Ideal solutions (A+ dan A-)
        v_plus, v_minus = self._ideal_solutions(weighted)
        
        # Step 3d: Calculate distances
        d_plus, d_minus = self._distances(weighted, v_plus, v_minus)
        
        # Step 3e: Calculate preferences (C_i)
        preferences = self._preferences(d_plus, d_minus)
        
        # ═══ STEP 4: RETURN RESULTS ═══
        ranking = self._rank_alternatives(preferences)
        
        return {
            'user_adjustment': user_adjustment.tolist(),
            'expert_baseline': self.ahp_expert_weights.tolist(),
            'personalized_weights': w_final.tolist(),
            'preferences': preferences.tolist(),
            'ranking': ranking,
            'explanation': self._generate_explanation(w_final),
            'criteria_names': self.criteria_names
        }
    
    def _normalize(self, matrix: np.ndarray) -> np.ndarray:
        """
        Normalisasi matrix menggunakan vector normalization
        r_ij = x_ij / sqrt(sum(x_ij^2))
        """
        denominator = np.sqrt((matrix ** 2).sum(axis=0))
        return matrix / denominator
    
    def _ideal_solutions(self, weighted_matrix: np.ndarray) -> Tuple[np.ndarray, np.ndarray]:
        """
        Hitung ideal solutions:
        - A+ : max untuk benefit criteria, min untuk cost criteria
        - A- : min untuk benefit criteria, max untuk cost criteria
        """
        n_criteria = len(self.criteria_type)
        v_plus = np.zeros(n_criteria)
        v_minus = np.zeros(n_criteria)
        
        for j in range(n_criteria):
            if self.criteria_type[j] == 'benefit':
                v_plus[j] = np.max(weighted_matrix[:, j])
                v_minus[j] = np.min(weighted_matrix[:, j])
            else:  # cost
                v_plus[j] = np.min(weighted_matrix[:, j])
                v_minus[j] = np.max(weighted_matrix[:, j])
        
        return v_plus, v_minus
    
    def _distances(self, weighted_matrix: np.ndarray, v_plus: np.ndarray, 
                   v_minus: np.ndarray) -> Tuple[np.ndarray, np.ndarray]:
        """
        Hitung Euclidean distance setiap alternatif dari ideal solutions
        D_i+ = sqrt(sum((V_ij - V_j+)^2))
        D_i- = sqrt(sum((V_ij - V_j-)^2))
        """
        n_alternatives = weighted_matrix.shape[0]
        
        d_plus = np.zeros(n_alternatives)
        d_minus = np.zeros(n_alternatives)
        
        for i in range(n_alternatives):
            d_plus[i] = np.sqrt(np.sum((weighted_matrix[i, :] - v_plus) ** 2))
            d_minus[i] = np.sqrt(np.sum((weighted_matrix[i, :] - v_minus) ** 2))
        
        return d_plus, d_minus
    
    def _preferences(self, d_plus: np.ndarray, d_minus: np.ndarray) -> np.ndarray:
        """
        Hitung preference score (C_i)
        C_i = D_i- / (D_i+ + D_i-)
        Range: 0 to 1 (semakin tinggi = semakin baik)
        """
        preferences = d_minus / (d_plus + d_minus)
        return preferences
    
    def _rank_alternatives(self, preferences: np.ndarray) -> List[Dict]:
        """
        Rank alternatives berdasarkan preference score
        Return list of {alternative_name, score, rank}
        """
        ranking_indices = np.argsort(-preferences)  # Sort descending
        
        ranking = []
        for rank, idx in enumerate(ranking_indices, 1):
            ranking.append({
                'rank': rank,
                'alternative': self.alternatives[idx],
                'score': round(preferences[idx], 4),
                'percentage': f"{preferences[idx]*100:.2f}%"
            })
        
        return ranking
    
    def _generate_explanation(self, weights: np.ndarray) -> Dict[str, str]:
        """Generate explanation of why weights changed from expert baseline"""
        explanation = {}
        weight_changes = weights - self.ahp_expert_weights
        
        for i, (criteria, change) in enumerate(zip(self.criteria_names, weight_changes)):
            expert_pct = f"{self.ahp_expert_weights[i]*100:.1f}%"
            new_pct = f"{weights[i]*100:.1f}%"
            direction = "increased" if change > 0 else "decreased" if change < 0 else "maintained"
            
            explanation[criteria] = {
                'expert_weight': expert_pct,
                'new_weight': new_pct,
                'direction': direction,
                'change': f"{change*100:+.1f}%"
            }
        
        return explanation
```

---

## BAGIAN 3: FLASK WEB API

### Backend API Endpoint

```python
# File: app.py (Flask Backend)

from flask import Flask, request, jsonify
from topsis_hybrid_backend import TOPSISHybrid
import numpy as np

app = Flask(__name__)

# Initialize TOPSIS Hybrid
topsis = TOPSISHybrid()

@app.route('/api/expert-baseline', methods=['GET'])
def get_expert_baseline():
    """
    GET: Return expert AHP baseline weights
    Frontend bisa display ini di slider
    """
    return jsonify({
        'expert_weights': topsis.ahp_expert_weights.tolist(),
        'criteria_names': topsis.criteria_names,
        'alternatives': topsis.alternatives
    })

@app.route('/api/calculate-hybrid-ranking', methods=['POST'])
def calculate_hybrid_ranking():
    """
    POST: Calculate personalized ranking based on user adjustment
    
    Expected JSON input:
    {
        "adjustment": [+0.10, -0.05, 0.0, +0.08, -0.03, +0.01]
    }
    
    Return: Personalized ranking dengan explanation
    """
    
    try:
        # ===== TERIMA INPUT DARI FRONTEND =====
        data = request.json
        user_adjustment = np.array(data.get('adjustment', [0]*6))
        
        # Validation: range check
        if np.any(np.abs(user_adjustment) > 0.20):
            return jsonify({
                'status': 'error',
                'message': 'Adjustment max ±20% per kriteria'
            }), 400
        
        # ===== PROCESS DI BACKEND =====
        result = topsis.calculate_ranking_hybrid(user_adjustment)
        
        # ===== RETURN KE FRONTEND =====
        return jsonify({
            'status': 'success',
            'expert_baseline': topsis.ahp_expert_weights.tolist(),
            'user_adjustment': user_adjustment.tolist(),
            'personalized_weights': result['personalized_weights'],
            'ranking': result['ranking'],
            'explanation': result['explanation'],
            'message': 'Ranking personalized berdasarkan preferensi Anda (expert baseline + adjustment)'
        })
    
    except ValueError as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 400
    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': f'Server error: {str(e)}'
        }), 500

@app.route('/api/get-decision-matrix', methods=['GET'])
def get_decision_matrix():
    """GET: Return decision matrix untuk reference"""
    return jsonify({
        'decision_matrix': topsis.decision_matrix.tolist(),
        'alternatives': topsis.alternatives,
        'criteria_names': topsis.criteria_names,
        'criteria_type': topsis.criteria_type
    })

if __name__ == '__main__':
    app.run(debug=True, port=5000)
```

---

## BAGIAN 4: FRONTEND - UI INTERACTION

### HTML Form dengan Slider

```html
<!-- File: index.html -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK - Investment Recommendation (Hybrid Model)</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .slider-container {
            display: flex;
            align-items: center;
            margin: 15px 0;
            gap: 15px;
        }
        .slider-container label {
            width: 150px;
            font-weight: bold;
        }
        input[type="range"] {
            width: 300px;
        }
        .weight-display {
            width: 200px;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .result-table {
            margin-top: 30px;
            border-collapse: collapse;
            width: 100%;
        }
        .result-table th, .result-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .result-table th {
            background-color: #4CAF50;
            color: white;
        }
        .result-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h1>SPK: Rekomendasi Investasi - Hybrid Model</h1>

<div id="expertBaseline">
    <h3>Expert Baseline (Dari AHP Pakar):</h3>
    <p id="expertWeights"></p>
</div>

<h3>Sesuaikan Preferensi Anda:</h3>
<div id="sliderContainer"></div>

<button onclick="calculateRanking()">Calculate Personalized Ranking</button>

<div id="results"></div>

<script>
    const API_BASE = 'http://localhost:5000/api';
    const criteria = ['Return', 'Risk', 'Liquidity', 'Capital', 'Income', 'Access'];
    const adjustments = [0, 0, 0, 0, 0, 0];
    
    // Load expert baseline
    async function loadExpertBaseline() {
        const response = await fetch(`${API_BASE}/expert-baseline`);
        const data = await response.json();
        
        // Display expert weights
        const expertWeights = data.expert_weights
            .map((w, i) => `${criteria[i]}: ${(w*100).toFixed(1)}%`)
            .join(' | ');
        document.getElementById('expertWeights').textContent = expertWeights;
        
        // Create sliders
        const container = document.getElementById('sliderContainer');
        criteria.forEach((name, i) => {
            const div = document.createElement('div');
            div.className = 'slider-container';
            
            const label = document.createElement('label');
            label.textContent = name + ':';
            
            const slider = document.createElement('input');
            slider.type = 'range';
            slider.min = '-20';
            slider.max = '20';
            slider.value = '0';
            slider.step = '1';
            slider.onchange = (e) => {
                adjustments[i] = parseFloat(e.target.value) / 100;
                updateWeightDisplay(i, data.expert_weights[i]);
            };
            
            const display = document.createElement('div');
            display.className = 'weight-display';
            display.id = `weight-${i}`;
            display.textContent = `Expert: ${(data.expert_weights[i]*100).toFixed(1)}%`;
            
            div.appendChild(label);
            div.appendChild(slider);
            div.appendChild(display);
            container.appendChild(div);
        });
    }
    
    function updateWeightDisplay(index, expertWeight) {
        const adjusted = expertWeight + adjustments[index];
        document.getElementById(`weight-${index}`).textContent = 
            `Expert: ${(expertWeight*100).toFixed(1)}% → Your: ${(adjusted*100).toFixed(1)}%`;
    }
    
    // Calculate ranking
    async function calculateRanking() {
        const response = await fetch(`${API_BASE}/calculate-hybrid-ranking`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ adjustment: adjustments })
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            displayResults(result);
        } else {
            alert('Error: ' + result.message);
        }
    }
    
    function displayResults(result) {
        let html = '<h3>Hasil Personalized Ranking:</h3>';
        
        // Weights comparison
        html += '<h4>Weights Comparison:</h4><table class="result-table">';
        html += '<tr><th>Kriteria</th><th>Expert</th><th>Your Adjustment</th><th>Final Weight</th></tr>';
        criteria.forEach((name, i) => {
            html += `<tr>
                <td>${name}</td>
                <td>${(result.expert_baseline[i]*100).toFixed(1)}%</td>
                <td>${(result.user_adjustment[i]*100):+.1f}%</td>
                <td>${(result.personalized_weights[i]*100).toFixed(1)}%</td>
            </tr>`;
        });
        html += '</table>';
        
        // Ranking results
        html += '<h4>Investment Ranking (Personalized):</h4><table class="result-table">';
        html += '<tr><th>Rank</th><th>Investment</th><th>Score</th><th>Percentage</th></tr>';
        result.ranking.forEach(item => {
            html += `<tr>
                <td>${item.rank}</td>
                <td>${item.alternative}</td>
                <td>${item.score}</td>
                <td>${item.percentage}</td>
            </tr>`;
        });
        html += '</table>';
        
        document.getElementById('results').innerHTML = html;
    }
    
    // Load on page load
    window.onload = loadExpertBaseline;
</script>

</body>
</html>
```

---

## BAGIAN 5: DATABASE SCHEMA (Optional - MySQL)

### Schema untuk Hybrid Model

```sql
-- Table: expert_data (Store AHP expertise once)
CREATE TABLE expert_data (
    id INT PRIMARY KEY AUTO_INCREMENT,
    criteria_name VARCHAR(50),
    criteria_order INT,
    ahp_weight DECIMAL(5,4),
    criteria_type ENUM('benefit', 'cost'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data
INSERT INTO expert_data (criteria_name, criteria_order, ahp_weight, criteria_type) VALUES
('Return', 1, 0.3067, 'benefit'),
('Risk', 2, 0.2377, 'cost'),
('Liquidity', 3, 0.1377, 'benefit'),
('Capital', 4, 0.1055, 'cost'),
('Income', 5, 0.1308, 'benefit'),
('Access', 6, 0.0817, 'benefit');

-- Table: decision_matrix (Store performa data once)
CREATE TABLE decision_matrix (
    id INT PRIMARY KEY AUTO_INCREMENT,
    alternative_name VARCHAR(50),
    k1_return DECIMAL(10,2),
    k2_risk DECIMAL(10,2),
    k3_liquidity DECIMAL(10,2),
    k4_capital DECIMAL(10,2),
    k5_income DECIMAL(10,2),
    k6_access DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data
INSERT INTO decision_matrix (alternative_name, k1_return, k2_risk, k3_liquidity, k4_capital, k5_income, k6_access) VALUES
('Saham', 12, 50, 9, 100000, 2.5, 7),
('Reksa Dana', 10, 35, 7, 50000, 2, 8),
('SBN Ritel', 6, 5, 6, 1000000, 6.5, 5),
('Kripto', 45, 85, 10, 10000, 0, 6),
('Emas Digital', 4, 15, 8, 100000, 0, 8);

-- Table: user_adjustments (Store per-user adjustments)
CREATE TABLE user_adjustments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    k1_adjustment DECIMAL(5,4),
    k2_adjustment DECIMAL(5,4),
    k3_adjustment DECIMAL(5,4),
    k4_adjustment DECIMAL(5,4),
    k5_adjustment DECIMAL(5,4),
    k6_adjustment DECIMAL(5,4),
    resulting_ranking JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## BAGIAN 6: IMPLEMENTATION CHECKLIST

```
✓ Backend Setup:
  - [ ] Create topsis_hybrid_backend.py dengan class TOPSISHybrid
  - [ ] Implement all TOPSIS methods (_normalize, _ideal_solutions, etc)
  - [ ] Test dengan dummy input

✓ Flask API:
  - [ ] Setup Flask app
  - [ ] Create /api/expert-baseline endpoint
  - [ ] Create /api/calculate-hybrid-ranking endpoint
  - [ ] Test dengan Postman/cURL

✓ Frontend:
  - [ ] Create HTML form dengan sliders
  - [ ] Fetch expert baseline on page load
  - [ ] Display adjustment weights in real-time
  - [ ] Submit adjustment dan display ranking results

✓ Testing:
  - [ ] Test User A scenario (aggressive)
  - [ ] Test User B scenario (conservative)
  - [ ] Test User C scenario (no adjustment)
  - [ ] Verify calculation accuracy

✓ Documentation:
  - [ ] Document API contracts
  - [ ] Document deployment steps
  - [ ] Create README.md
```

---

## REFERENSI

**Untuk perhitungan manual detail:**
→ Lihat: [02_PERHITUNGAN_MANUAL_HYBRID.md](02_PERHITUNGAN_MANUAL_HYBRID.md)

**Untuk teori AHP & TOPSIS:**
→ Lihat: [01_FRAMEWORK_PENGAMBILAN_DATA_PAKAR.md](01_FRAMEWORK_PENGAMBILAN_DATA_PAKAR.md)
