<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Donation Scanner</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .scanner-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .scanner-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .scanner-header h1 {
            color: #333;
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .scanner-header p {
            color: #666;
            font-size: 16px;
        }
        
        .upload-area {
            border: 3px dashed #667eea;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
            position: relative;
        }
        
        .upload-area:hover {
            border-color: #764ba2;
            background: #e9ecef;
            transform: translateY(-2px);
        }
        
        .upload-area.dragover {
            border-color: #28a745;
            background: #d4edda;
        }
        
        .upload-icon {
            font-size: 64px;
            margin-bottom: 15px;
        }
        
        .upload-text {
            color: #667eea;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .upload-hint {
            color: #999;
            font-size: 14px;
        }
        
        #fileInput {
            display: none;
        }
        
        .preview-container {
            margin-top: 20px;
            display: none;
        }
        
        .image-preview {
            width: 100%;
            max-height: 300px;
            object-fit: contain;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .result-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-top: 20px;
            display: none;
        }
        
        .result-box.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        .result-box.warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        }
        
        .result-box.error {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        .result-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .result-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .result-description {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .detected-items {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .detected-items h4 {
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .item-tag {
            display: inline-block;
            background: rgba(255, 255, 255, 0.3);
            padding: 5px 12px;
            border-radius: 20px;
            margin: 5px;
            font-size: 14px;
        }
        
        .condition-tag {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            margin: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .condition-good { background: #28a745; color: white; }
        .condition-fair { background: #ffc107; color: black; }
        .condition-poor { background: #dc3545; color: white; }
        
        .btn-scan {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
            transition: all 0.3s;
        }
        
        .btn-scan:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-scan:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #667eea;
            font-size: 16px;
        }
        
        .loading::after {
            content: '...';
            animation: dots 1.5s steps(4, end) infinite;
        }
        
        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60%, 100% { content: '...'; }
        }
        
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 14px;
            color: #0d47a1;
        }
        
        @media (max-width: 600px) {
            .scanner-container {
                padding: 20px;
            }
            
            .scanner-header h1 {
                font-size: 24px;
            }
            
            .upload-area {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="scanner-container">
        <div class="scanner-header">
            <h1>🤖 AI Donation Scanner</h1>
            <p>Upload an image and AI will tell you if it can be donated</p>
        </div>
        
        <div class="upload-area" id="uploadArea">
            <div class="upload-icon">📸</div>
            <div class="upload-text">Click to upload or drag & drop</div>
            <div class="upload-hint">Supports: JPG, PNG, WEBP (Max 5MB)</div>
            <input type="file" id="fileInput" accept="image/*">
        </div>
        
        <div class="preview-container" id="previewContainer">
            <img id="imagePreview" class="image-preview" alt="Preview">
            <button class="btn-scan" id="scanBtn" onclick="scanImage()">
                🔍 Scan for Donations
            </button>
        </div>
        
        <div class="result-box" id="resultBox">
            <div class="result-icon" id="resultIcon"></div>
            <div class="result-title" id="resultTitle"></div>
            <div class="result-description" id="resultDescription"></div>
            <div class="detected-items" id="detectedItems"></div>
        </div>
        
        <div class="info-box">
            <strong>💡 How it works:</strong> This uses AI vision to identify objects in your image. 
            It can recognize clothes, books, toys, electronics, furniture, and more!
        </div>
    </div>

    <script>
    // =================================================================
    // IMPROVED AI DONATION SCANNER - BETTER CLOTHING DETECTION
    // =================================================================
    
    let selectedImage = null;
    let selectedFile = null;
    
    // EXPANDED DONATABLE ITEMS CATEGORIES
    const DONATABLE_ITEMS = {
        'clothing': [
            // Basic clothing items
            'shirt', 't-shirt', 'tee', 'blouse', 'top', 'pants', 'trousers', 'jeans', 'denim',
            'dress', 'gown', 'frock', 'jacket', 'coat', 'overcoat', 'sweater', 'jumper', 
            'hoodie', 'sweatshirt', 'pullover', 'cardigan', 'jersey', 'sweat', 'tank',
            'skirt', 'miniskirt', 'shorts', 'bermuda', 'sweatpants', 'track', 'leggings',
            'uniform', 'costume', 'outfit', 'suit', 'blazer', 'vest', 'waistcoat',
            
            // Specific clothing types
            'pajama', 'pyjama', 'nightgown', 'nightwear', 'sleepwear', 'robe', 'bathrobe',
            'swimwear', 'swimsuit', 'bikini', 'trunks', 'bathing', 'beachwear',
            'sportswear', 'activewear', 'gym', 'workout', 'training',
            'formal', 'casual', 'business', 'evening', 'party',
            
            // Fabrics and materials (often detected by AI)
            'velvet', 'silk', 'cotton', 'wool', 'linen', 'denim', 'leather', 'suede',
            'polyester', 'nylon', 'spandex', 'lycra', 'cashmere', 'fleece', 'flannel',
            'chiffon', 'satin', 'lace', 'knit', 'woven', 'fabric', 'textile', 'cloth',
            'material', 'garment', 'apparel', 'attire', 'wear', 'clothes', 'clothing'
        ],
        
        'shoes': [
            'shoe', 'boot', 'sneaker', 'trainer', 'sandal', 'slipper', 'footwear',
            'loafer', 'pump', 'heel', 'high heels', 'stiletto', 'wedge', 'flat',
            'oxford', 'derby', 'moccasin', 'espadrille', 'clog', 'flip flop', 'thong',
            'sport shoe', 'running shoe', 'basketball shoe', 'tennis shoe', 'cleat',
            'hiking boot', 'work boot', 'rain boot', 'snow boot', 'ski boot'
        ],
        
        'accessories': [
            'bag', 'backpack', 'purse', 'handbag', 'tote', 'clutch', 'satchel',
            'hat', 'cap', 'beanie', 'beret', 'fedora', 'sun hat', 'bucket hat',
            'scarf', 'glove', 'mitten', 'belt', 'tie', 'bow tie', 'cravat',
            'wallet', 'purse', 'coin purse', 'keychain', 'fob',
            'sunglasses', 'glasses', 'spectacles', 'eyewear',
            'jewelry', 'necklace', 'bracelet', 'ring', 'earring', 'brooch',
            'watch', 'timepiece', 'umbrella', 'parasol'
        ],
        
        'furniture': [
            'chair', 'table', 'desk', 'shelf', 'bookshelf', 'cabinet', 'wardrobe',
            'sofa', 'couch', 'loveseat', 'sectional', 'settee', 'chesterfield',
            'bed', 'mattress', 'headboard', 'footboard', 'bedframe',
            'dresser', 'chest', 'drawer', 'armoire', 'cupboard',
            'stool', 'bench', 'ottoman', 'footstool', 'pouf',
            'dining table', 'coffee table', 'side table', 'end table', 'console',
            'bookcase', 'display case', 'curio', 'china cabinet'
        ],
        
        'kitchenware': [
            'plate', 'dish', 'bowl', 'cup', 'mug', 'glass', 'tumbler', 'stemware',
            'pot', 'pan', 'skillet', 'wok', 'saucepan', 'stockpot', 'dutch oven',
            'utensil', 'cutlery', 'silverware', 'flatware', 'knife', 'fork', 'spoon',
            'kettle', 'teapot', 'coffee pot', 'french press', 'percolator',
            'saucer', 'platter', 'tray', 'serving dish', 'gravy boat'
        ],
        
        'appliances': [
            'microwave', 'toaster', 'toaster oven', 'blender', 'mixer', 'food processor',
            'kettle', 'coffee maker', 'espresso machine', 'rice cooker', 'slow cooker',
            'fan', 'heater', 'air conditioner', 'humidifier', 'dehumidifier',
            'vacuum cleaner', 'vacuum', 'iron', 'steam iron', 'steamer',
            'washing machine', 'dryer', 'dishwasher', 'refrigerator', 'freezer'
        ],
        
        'books': [
            'book', 'magazine', 'comic', 'graphic novel', 'textbook', 'novel',
            'hardcover', 'paperback', 'library book', 'encyclopedia', 'dictionary',
            'thesaurus', 'atlas', 'manual', 'guidebook', 'cookbook', 'workbook',
            'notebook', 'journal', 'diary', 'planner', 'calendar', 'album'
        ],
        
        'electronics': [
            'laptop', 'computer', 'desktop', 'tablet', 'ipad', 'phone', 'smartphone',
            'television', 'tv', 'monitor', 'screen', 'display', 'keyboard', 'mouse',
            'camera', 'digital camera', 'dslr', 'mirrorless', 'webcam',
            'headphone', 'earphone', 'earbud', 'headset', 'speaker', 'soundbar',
            'printer', 'scanner', 'copier', 'fax', 'router', 'modem', 'game console'
        ],
        
        'toys': [
            'toy', 'doll', 'action figure', 'stuffed animal', 'plush', 'teddy bear',
            'puzzle', 'jigsaw', 'game', 'board game', 'card game', 'video game',
            'lego', 'building blocks', 'construction set', 'model kit',
            'ball', 'dollhouse', 'play set', 'educational toy', 'learning toy'
        ],
        
        'sports': [
            'bicycle', 'bike', 'mountain bike', 'road bike', 'exercise bike',
            'football', 'soccer ball', 'basketball', 'volleyball', 'baseball',
            'racket', 'tennis racket', 'badminton racket', 'squash racket',
            'skateboard', 'roller skate', 'skate', 'scooter', 'frisbee', 'disc',
            'golf club', 'baseball bat', 'hockey stick', 'cricket bat'
        ],
        
        'tools': [
            'hammer', 'screwdriver', 'wrench', 'pliers', 'drill', 'saw', 'toolbox',
            'tool kit', 'socket set', 'ratchet', 'level', 'tape measure', 'ruler',
            'ladder', 'step stool', 'workbench', 'vise', 'clamp', 'chisel', 'plane'
        ],
        
        'food': [  // FOOD CAN NOW BE DONATED TOO!
            'food', 'canned food', 'non-perishable', 'dry food', 'pantry item',
            'cereal', 'pasta', 'rice', 'beans', 'lentils', 'canned goods',
            'soup', 'stew', 'vegetable', 'fruit', 'apple', 'banana', 'orange',
            'bread', 'crackers', 'cookie', 'biscuit', 'snack', 'granola bar',
            'water', 'beverage', 'juice', 'milk', 'formula', 'baby food'
        ]
    };
    
    // ITEMS THAT CANNOT BE DONATED
    const NON_DONATABLE_ITEMS = {
        'trash': ['trash', 'garbage', 'waste', 'rubbish', 'litter', 'debris', 'refuse'],
        'hazardous': ['chemical', 'toxic', 'poison', 'hazardous', 'dangerous', 'flammable'],
        'broken': ['broken', 'cracked', 'shattered', 'damaged', 'torn', 'ripped', 'stained'],
        'animals': ['dog', 'cat', 'pet', 'animal', 'bird', 'fish', 'reptile', 'mammal'],
        'people': ['person', 'man', 'woman', 'child', 'baby', 'human', 'people', 'face'],
        'tobacco': ['cigarette', 'cigar', 'tobacco', 'smoke', 'vape', 'nicotine'],
        'expired': ['expired', 'spoiled', 'rotten', 'moldy', 'decaying', 'decomposing'],
        'liquid': ['liquid', 'fluid', 'oil', 'gasoline', 'paint', 'ink', 'perfume']
    };
    
    // Setup elements
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const resultBox = document.getElementById('resultBox');
    
    // Event listeners
    uploadArea.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', handleFileSelect);
    
    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            handleFile(file);
        }
    });
    
    function handleFileSelect(e) {
        const file = e.target.files[0];
        if (file) {
            handleFile(file);
        }
    }
    
    function handleFile(file) {
        if (file.size > 5 * 1024 * 1024) {
            alert('File too large! Please use an image under 5MB.');
            return;
        }
        
        selectedFile = file;
        
        const reader = new FileReader();
        reader.onload = (e) => {
            selectedImage = e.target.result;
            imagePreview.src = selectedImage;
            previewContainer.style.display = 'block';
            resultBox.style.display = 'none';
            
            // Auto-scan
            setTimeout(() => {
                scanImage();
            }, 1000);
        };
        reader.readAsDataURL(file);
    }
    
    // MAIN SCANNING FUNCTION
    async function scanImage() {
        const scanBtn = document.getElementById('scanBtn');
        scanBtn.disabled = true;
        scanBtn.textContent = '🔍 Scanning...';
        
        resultBox.style.display = 'block';
        resultBox.className = 'result-box';
        resultBox.innerHTML = '<div class="loading">Analyzing image with AI</div>';
        
        try {
            const predictions = await analyzeWithTensorFlow();
            
            if (predictions && predictions.length > 0) {
                processResults(predictions);
            } else {
                smartAnalysis();
            }
            
        } catch (error) {
            console.error('AI Error:', error);
            smartAnalysis();
        }
        
        scanBtn.disabled = false;
        scanBtn.textContent = '🔍 Scan Another Image';
    }
    
    // TENSORFLOW.JS MOBILENET
    async function analyzeWithTensorFlow() {
        try {
            if (typeof mobilenet === 'undefined') {
                await loadScript('https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js');
                await loadScript('https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet@latest/dist/mobilenet.min.js');
                await new Promise(resolve => setTimeout(resolve, 1500));
            }
            
            const model = await mobilenet.load({version: 2, alpha: 1.0});
            const img = new Image();
            img.src = selectedImage;
            
            await new Promise((resolve) => {
                img.onload = resolve;
            });
            
            const predictions = await model.classify(img, 10);
            
            return predictions.map(p => ({
                label: p.className.toLowerCase(),
                score: p.probability,
                className: p.className
            }));
            
        } catch (error) {
            console.log('TensorFlow.js failed:', error);
            return null;
        }
    }
    
    // Load script dynamically
    function loadScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
    
    // PROCESS RESULTS WITH BETTER LOGIC
    function processResults(predictions) {
        const topPredictions = predictions.slice(0, 5);
        
        // 1. Check for CLEAR non-donatable items (high confidence)
        let nonDonatableResult = checkForNonDonatable(topPredictions);
        if (nonDonatableResult) {
            showNonDonatable(nonDonatableResult.reason, topPredictions, nonDonatableResult.prediction);
            return;
        }
        
        // 2. Check for donatable items
        let donatableResult = checkForDonatable(topPredictions);
        if (donatableResult) {
            showDonatable(donatableResult.category, donatableResult.items, topPredictions);
            return;
        }
        
        // 3. If uncertain, show unknown
        showUnknownItem(topPredictions);
    }
    
    // CHECK FOR NON-DONATABLE ITEMS
    function checkForNonDonatable(predictions) {
        for (const prediction of predictions) {
            const label = prediction.label.toLowerCase();
            
            for (const [category, items] of Object.entries(NON_DONATABLE_ITEMS)) {
                for (const item of items) {
                    // Check for exact or partial matches
                    if (label.includes(item) && prediction.score > 0.5) {
                        return {
                            reason: category,
                            prediction: prediction
                        };
                    }
                }
            }
            
            // Special check for "expired" context
            if ((label.includes('food') || label.includes('perishable')) && 
                (label.includes('rotten') || label.includes('moldy') || label.includes('expired'))) {
                return {
                    reason: 'expired',
                    prediction: prediction
                };
            }
        }
        
        return null;
    }
    
    // CHECK FOR DONATABLE ITEMS
    function checkForDonatable(predictions) {
        let bestCategory = '';
        let bestScore = 0;
        let bestItems = [];
        
        for (const prediction of predictions) {
            const label = prediction.label.toLowerCase();
            
            for (const [category, items] of Object.entries(DONATABLE_ITEMS)) {
                for (const item of items) {
                    // Check for matches in the prediction label
                    if (label.includes(item)) {
                        if (prediction.score > bestScore) {
                            bestScore = prediction.score;
                            bestCategory = category;
                            bestItems = [{
                                name: prediction.className,
                                confidence: (prediction.score * 100).toFixed(1)
                            }];
                        } else if (prediction.score > 0.3) {
                            bestItems.push({
                                name: prediction.className,
                                confidence: (prediction.score * 100).toFixed(1)
                            });
                        }
                        break;
                    }
                }
            }
        }
        
        if (bestScore > 0.3) {
            return {
                category: bestCategory,
                items: bestItems
            };
        }
        
        return null;
    }
    
    // SMART ANALYSIS FALLBACK
    function smartAnalysis() {
        const img = new Image();
        img.src = selectedImage;
        
        img.onload = function() {
            const predictions = [
                { label: 'item', score: 0.5, className: 'General Item' }
            ];
            processResults(predictions);
        };
    }
    
    function showDonatable(category, items, allPredictions) {
    resultBox.className = 'result-box success';

    const categoryNames = {
        'clothing': 'Clothing 👕',
        'shoes': 'Footwear 👟',
        'accessories': 'Accessories 🎒',
        'furniture': 'Furniture 🛋️',
        'kitchenware': 'Kitchenware 🍽️',
        'appliances': 'Appliances 🍳',
        'books': 'Books 📚',
        'electronics': 'Electronics 💻',
        'toys': 'Toys & Games 🧸',
        'sports': 'Sports Equipment ⚽',
        'tools': 'Tools 🔧',
        'food': 'Food 🍎'
    };

    const categoryDescriptions = {
        'clothing': 'Clothing items can be donated to shelters, thrift stores, or clothing drives.',
        'shoes': 'Footwear in good condition can help those in need.',
        'accessories': 'Accessories can be donated to various charities.',
        'furniture': 'Furniture donations help families furnish their homes.',
        'kitchenware': 'Kitchen items are always needed by families starting over.',
        'appliances': 'Working appliances can greatly help those in need.',
        'books': 'Books can be donated to libraries, schools, or literacy programs.',
        'electronics': 'Working electronics can be very valuable donations.',
        'toys': 'Toys bring joy to children in difficult situations.',
        'sports': 'Sports equipment helps promote healthy activities.',
        'tools': 'Tools can help people maintain their homes or learn trades.',
        'food': 'Non-perishable food items are always needed at food banks and shelters.'
    };

    resultBox.innerHTML = `
        <div class="result-icon">✅</div>
        <div class="result-title">YES! This Can Be Donated</div>
        <div class="result-description">
            Great news! AI detected <strong>${categoryNames[category]}</strong> 
            which can be donated to help those in need.
            <br><br>
            ${categoryDescriptions[category] || 'This item can help someone in need.'}
        </div>
        <div class="detected-items">
            <h4>🎯 Top AI Detections:</h4>
            ${allPredictions.slice(0, 3).map(p => `
                <div class="item-tag">${p.className} (${(p.score * 100).toFixed(1)}%)</div>
            `).join('')}
        </div>
    `;

    // 🔹 AJOUTER CETTE LIGNE POUR REDIRECTION APRÈS 2 SECONDES
    setTimeout(() => {
        window.location.href = 'http://localhost/projet_dons2/view/frontoffice/donation_form.php?id=5';
    }, 2000);
}



    
    function getAlternativeSuggestion(reason) {
        const suggestions = {
            'trash': 'Recycle if possible, otherwise dispose in appropriate bin.',
            'hazardous': 'Contact city hazardous waste disposal service.',
            'broken': 'Repair if possible, or recycle components.',
            'animals': 'Contact local animal shelters or rescue organizations.',
            'people': 'If this is a person in need, contact social services.',
            'tobacco': 'Dispose of properly. Consider smoking cessation programs.',
            'expired': 'Compost if possible, or dispose properly.',
            'liquid': 'Check if local recycling accepts this type of container.'
        };
        return suggestions[reason] || 'Consider proper disposal or specialized services.';
    }
    
    // SHOW DONATABLE RESULT
    function showDonatable(category, items, allPredictions) {
        resultBox.className = 'result-box success';
        
        const categoryNames = {
            'clothing': 'Clothing 👕',
            'shoes': 'Footwear 👟',
            'accessories': 'Accessories 🎒',
            'furniture': 'Furniture 🛋️',
            'kitchenware': 'Kitchenware 🍽️',
            'appliances': 'Appliances 🍳',
            'books': 'Books 📚',
            'electronics': 'Electronics 💻',
            'toys': 'Toys & Games 🧸',
            'sports': 'Sports Equipment ⚽',
            'tools': 'Tools 🔧',
            'food': 'Food 🍎'  // NEW: Food can be donated!
        };
        
        const categoryDescriptions = {
            'clothing': 'Clothing items can be donated to shelters, thrift stores, or clothing drives.',
            'shoes': 'Footwear in good condition can help those in need.',
            'accessories': 'Accessories can be donated to various charities.',
            'furniture': 'Furniture donations help families furnish their homes.',
            'kitchenware': 'Kitchen items are always needed by families starting over.',
            'appliances': 'Working appliances can greatly help those in need.',
            'books': 'Books can be donated to libraries, schools, or literacy programs.',
            'electronics': 'Working electronics can be very valuable donations.',
            'toys': 'Toys bring joy to children in difficult situations.',
            'sports': 'Sports equipment helps promote healthy activities.',
            'tools': 'Tools can help people maintain their homes or learn trades.',
            'food': 'Non-perishable food items are always needed at food banks and shelters.'
        };
        
        resultBox.innerHTML = `
            <div class="result-icon">✅</div>
            <div class="result-title">YES! This Can Be Donated</div>
            <div class="result-description">
                Great news! AI detected <strong>${categoryNames[category]}</strong> 
                which can be donated to help those in need.
                <br><br>
                ${categoryDescriptions[category] || 'This item can help someone in need.'}
            </div>
            <div class="detected-items">
                <h4>🎯 Top AI Detections:</h4>
                ${allPredictions.slice(0, 3).map(p => `
                    <div class="item-tag">${p.className} (${(p.score * 100).toFixed(1)}%)</div>
                `).join('')}
            </div>
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.3); font-size: 14px;">
                💚 <strong>Where to donate:</strong> ${getDonationLocation(category)}
            </div>
        `;
    }
    
    function getDonationLocation(category) {
        const locations = {
            'clothing': 'Goodwill, Salvation Army, homeless shelters, clothing drives',
            'shoes': 'Shoe donation programs, homeless shelters, thrift stores',
            'food': 'Food banks, homeless shelters, community fridges, churches',
            'books': 'Libraries, schools, literacy programs, Little Free Libraries',
            'electronics': 'Goodwill, shelters, schools, e-waste recycling programs',
            'furniture': 'Habitat for Humanity, Salvation Army, furniture banks',
            'toys': 'Toys for Tots, children\'s hospitals, shelters, churches'
        };
        return locations[category] || 'Local charities, thrift stores, or shelters in your area.';
    }
    
    // SHOW UNKNOWN ITEM
    function showUnknownItem(predictions) {
        resultBox.className = 'result-box warning';
        
        resultBox.innerHTML = `
            <div class="result-icon">❓</div>
            <div class="result-title">Uncertain - Please Verify</div>
            <div class="result-description">
                AI detected this item but couldn't determine if it can be donated.
                <br><br>
                <strong>Most likely:</strong> ${predictions[0]?.className || 'Unknown item'}
                (${predictions[0] ? (predictions[0].score * 100).toFixed(1) : '0'}% confidence)
            </div>
            ${predictions.length > 0 ? `
                <div class="detected-items">
                    <h4>🔍 What AI saw:</h4>
                    ${predictions.slice(0, 3).map(p => `
                        <div class="item-tag">${p.className} (${(p.score * 100).toFixed(1)}%)</div>
                    `).join('')}
                </div>
            ` : ''}
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.3); font-size: 14px;">
                💡 <strong>Manual Check:</strong> Take a clearer photo or describe the item to a donation center.
            </div>
        `;
    }
    
    // INITIAL MESSAGE
    window.addEventListener('load', () => {
        resultBox.style.display = 'block';
        resultBox.className = 'result-box';
        resultBox.innerHTML = `
            <div class="result-icon">🤖</div>
            <div class="result-title">AI Donation Scanner</div>
            <div class="result-description">
                Upload any image to check if it can be donated.
                <br><br>
                <strong>Now accepts:</strong>
                <ul style="text-align: left; margin-top: 10px; font-size: 14px;">
                    <li>✅ All clothing (jeans, shirts, pajamas, velvet, etc.)</li>
                    <li>✅ Non-perishable food items</li>
                    <li>✅ Furniture, books, electronics</li>
                    <li>✅ Toys, sports equipment, tools</li>
                    <li>🚫 Live animals, people, trash</li>
                    <li>🚫 Hazardous materials, broken items</li>
                </ul>
            </div>
        `;
    });
    </script>
</body>
</html>